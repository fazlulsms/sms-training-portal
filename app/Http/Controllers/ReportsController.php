<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\ElearningEnrollment;
use App\Models\Enrollment;
use App\Models\TrainingSchedule;
use App\Models\Trainer;
use App\Models\Invoice;
use App\Models\CorporateSession;
use App\Models\CorporateProject;
use App\Models\CorporateCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /* ══════════════════════════════════════════════
     |  LANDING HUB
     ══════════════════════════════════════════════ */
    public function index()
    {
        $stats = [
            'elearning_enrollments' => ElearningEnrollment::count(),
            'ilt_enrollments'       => Enrollment::count(),
            'invoices'              => Invoice::count(),
            'total_paid'            => Invoice::sum('amount_paid'),
            'certificates_elearning'=> ElearningEnrollment::where('certificate_status','issued')->count(),
            'certificates_ilt'      => Enrollment::whereNotNull('certificate_number')->count(),
            'certificates_corp'     => CorporateCertificate::count(),
            'countries'             => Enrollment::whereNotNull('country')->distinct('country')->count('country'),
        ];
        return view('reports.index', compact('stats'));
    }

    /* ══════════════════════════════════════════════
     |  A. eLEARNING REPORT
     ══════════════════════════════════════════════ */
    public function elearning(Request $request)
    {
        $filters = $request->only(['date_from','date_to','course_id','company','payment_status','completion_status','certificate_status','q']);

        $query = ElearningEnrollment::with('course')
            ->when($filters['date_from'] ?? null, fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to']   ?? null, fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->when($filters['course_id'] ?? null, fn($q) => $q->where('course_id', $filters['course_id']))
            ->when($filters['company']   ?? null, fn($q) => $q->where('company', 'like', '%'.$filters['company'].'%'))
            ->when($filters['payment_status']     ?? null, fn($q) => $q->where('payment_status', $filters['payment_status']))
            ->when($filters['completion_status']  ?? null, fn($q) => $q->where('completion_status', $filters['completion_status']))
            ->when($filters['certificate_status'] ?? null, fn($q) => $q->where('certificate_status', $filters['certificate_status']))
            ->when($filters['q'] ?? null, fn($q2) => $q2->where(fn($sub) =>
                $sub->where('participant_name','like','%'.$filters['q'].'%')
                    ->orWhere('email','like','%'.$filters['q'].'%')
                    ->orWhere('company','like','%'.$filters['q'].'%')
            ));

        $enrollments = (clone $query)->orderByDesc('created_at')->paginate(25)->withQueryString();

        // Summary stats
        $all = (clone $query)->get();
        $stats = [
            'total_courses'      => Course::where('course_type','elearning')->count(),
            'published_courses'  => Course::where('course_type','elearning')->where('status',1)->count(),
            'total_enrollments'  => $all->count(),
            'in_progress'        => $all->where('completion_status','in_progress')->count(),
            'completed'          => $all->where('completion_status','completed')->count(),
            'certificates'       => $all->where('certificate_status','issued')->count(),
            'completion_pct'     => $all->count() > 0 ? round($all->where('completion_status','completed')->count() / $all->count() * 100) : 0,
            'paid_amount'        => $all->whereIn('payment_status',['paid','manual_approved'])->sum('amount'),
            'due_amount'         => $all->where('payment_status','pending')->sum('amount'),
        ];

        // Chart data: monthly enrollments last 6 months
        $monthly = ElearningEnrollment::select(
                DB::raw("DATE_FORMAT(created_at,'%b %Y') as month"),
                DB::raw("DATE_FORMAT(created_at,'%Y-%m') as sort_key"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as revenue')
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month','sort_key')
            ->orderBy('sort_key')
            ->get();

        $courses = Course::where('course_type','elearning')->orderBy('name')->get();

        return view('reports.elearning', compact('enrollments','stats','monthly','courses','filters'));
    }

    /* ══════════════════════════════════════════════
     |  B. INSTRUCTOR-LED TRAINING REPORT
     ══════════════════════════════════════════════ */
    public function ilt(Request $request)
    {
        $filters = $request->only(['date_from','date_to','course_id','trainer_id','company','venue','country','attendance_status','certificate_status','q']);

        // Instructor-led (TrainingSchedule-based) enrollments
        $query = Enrollment::with('trainingSchedule.course','trainingSchedule.trainer')
            ->when($filters['date_from'] ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->whereDate('start_date', '>=', $filters['date_from'])))
            ->when($filters['date_to']   ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->whereDate('start_date', '<=', $filters['date_to'])))
            ->when($filters['course_id'] ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->where('course_id', $filters['course_id'])))
            ->when($filters['trainer_id'] ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->where('trainer_id', $filters['trainer_id'])))
            ->when($filters['company']   ?? null, fn($q) => $q->where('company','like','%'.$filters['company'].'%'))
            ->when($filters['country']   ?? null, fn($q) => $q->where('country', $filters['country']))
            ->when($filters['venue']     ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->where('venue','like','%'.$filters['venue'].'%')))
            ->when($filters['attendance_status']  ?? null, fn($q) => $q->where('attendance_status', $filters['attendance_status']))
            ->when($filters['certificate_status'] ?? null, fn($q) => match($filters['certificate_status']) {
                'issued'   => $q->where('certificate_generated', true),
                'pending'  => $q->where('certificate_generated', false),
                default    => $q,
            })
            ->when($filters['q'] ?? null, fn($q2) => $q2->where(fn($sub) =>
                $sub->where('full_name','like','%'.$filters['q'].'%')
                    ->orWhere('email','like','%'.$filters['q'].'%')
                    ->orWhere('company','like','%'.$filters['q'].'%')
            ));

        $enrollments = (clone $query)->orderByDesc('created_at')->paginate(25)->withQueryString();
        $all = (clone $query)->with('trainingSchedule.course','trainingSchedule.trainer')->get();

        $scheduleIds = $all->pluck('training_schedule_id')->unique();
        $schedules   = TrainingSchedule::whereIn('id', $scheduleIds)->with('course','trainer')->get();

        $stats = [
            'total_manual_courses'  => Course::where('course_type','manual')->count(),
            'total_sessions'        => $schedules->count(),
            'total_participants'    => $all->count(),
            'present'               => $all->where('attendance_status','Present')->count(),
            'absent'                => $all->where('attendance_status','Absent')->count(),
            'certificates'          => $all->where('certificate_generated', true)->count(),
            'total_hours'           => $schedules->sum(fn($s) => is_numeric($s->duration) ? (float)$s->duration : 0),
            'facilities'            => $schedules->pluck('venue')->filter()->unique()->count(),
            'cities'                => $schedules->pluck('venue')->filter()->unique()->count(), // venue used as city proxy
            'countries'             => $all->pluck('country')->filter()->unique()->count(),
            'trainers'              => $schedules->pluck('trainer_id')->filter()->unique()->count(),
        ];

        // Monthly participant trend
        $monthly = Enrollment::select(
                DB::raw("DATE_FORMAT(created_at,'%b %Y') as month"),
                DB::raw("DATE_FORMAT(created_at,'%Y-%m') as sort_key"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month','sort_key')
            ->orderBy('sort_key')
            ->get();

        $courses  = Course::where('course_type','manual')->orderBy('name')->get();
        $trainers = Trainer::orderBy('name')->get();
        $countries= Enrollment::whereNotNull('country')->distinct()->orderBy('country')->pluck('country');

        return view('reports.ilt', compact('enrollments','stats','monthly','courses','trainers','countries','filters'));
    }

    /* ══════════════════════════════════════════════
     |  C. FINANCIAL REPORT
     ══════════════════════════════════════════════ */
    public function financial(Request $request)
    {
        $filters = $request->only(['date_from','date_to','service_type','payment_method','payment_status','company','q']);

        $query = Invoice::query()
            ->when($filters['date_from']    ?? null, fn($q) => $q->whereDate('invoice_date', '>=', $filters['date_from']))
            ->when($filters['date_to']      ?? null, fn($q) => $q->whereDate('invoice_date', '<=', $filters['date_to']))
            ->when($filters['service_type'] ?? null, fn($q) => $q->where('service_type', $filters['service_type']))
            ->when($filters['payment_method']?? null, fn($q) => $q->where('payment_method', $filters['payment_method']))
            ->when($filters['payment_status']?? null, fn($q) => $q->where('payment_status', $filters['payment_status']))
            ->when($filters['company']      ?? null, fn($q) => $q->where('client_company','like','%'.$filters['company'].'%'))
            ->when($filters['q']            ?? null, fn($q2) => $q2->where(fn($sub) =>
                $sub->where('invoice_number','like','%'.$filters['q'].'%')
                    ->orWhere('client_name','like','%'.$filters['q'].'%')
                    ->orWhere('client_company','like','%'.$filters['q'].'%')
            ));

        $invoices = (clone $query)->orderByDesc('invoice_date')->paginate(25)->withQueryString();
        $all      = (clone $query)->get();

        $stats = [
            'total_amount'  => $all->sum('total_amount'),
            'paid_amount'   => $all->sum('amount_paid'),
            'due_amount'    => $all->sum(fn($i) => max(0, (float)$i->total_amount - (float)$i->amount_paid)),
            'pending_count' => $all->where('payment_status','Pending')->count(),
            'invoice_count' => $all->count(),
        ];

        // Payment method breakdown
        $methodBreakdown = $all->groupBy('payment_method')->map(fn($g) => [
            'count'  => $g->count(),
            'amount' => $g->sum('amount_paid'),
        ]);

        // Monthly income trend
        $monthly = Invoice::select(
                DB::raw("DATE_FORMAT(invoice_date,'%b %Y') as month"),
                DB::raw("DATE_FORMAT(invoice_date,'%Y-%m') as sort_key"),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('SUM(amount_paid) as paid')
            )
            ->where('invoice_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month','sort_key')
            ->orderBy('sort_key')
            ->get();

        $paymentMethods = ['Cash','Bank Transfer','bKash','Nagad','SSLCommerz','Card','Other'];

        return view('reports.financial', compact('invoices','stats','methodBreakdown','monthly','paymentMethods','filters'));
    }

    /* ══════════════════════════════════════════════
     |  D. GEOGRAPHIC REPORT
     ══════════════════════════════════════════════ */
    public function geographic(Request $request)
    {
        $filters = $request->only(['date_from','date_to','training_type','course_id','country','city','q']);

        // ILT participant countries
        $iltByCountry = Enrollment::select('country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country')
            ->when($filters['date_from'] ?? null, fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to']   ?? null, fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->when($filters['country']   ?? null, fn($q) => $q->where('country', $filters['country']))
            ->groupBy('country')->orderByDesc('count')->get();

        // eLearning participant countries
        $elByCountry = ElearningEnrollment::select('country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country')
            ->when($filters['date_from'] ?? null, fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to']   ?? null, fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->when($filters['country']   ?? null, fn($q) => $q->where('country', $filters['country']))
            ->groupBy('country')->orderByDesc('count')->get();

        // ILT sessions by venue (used as city proxy)
        $sessionsByVenue = TrainingSchedule::select('venue', DB::raw('COUNT(*) as count'))
            ->whereNotNull('venue')
            ->when($filters['date_from'] ?? null, fn($q) => $q->whereDate('start_date', '>=', $filters['date_from']))
            ->when($filters['date_to']   ?? null, fn($q) => $q->whereDate('start_date', '<=', $filters['date_to']))
            ->when($filters['city']      ?? null, fn($q) => $q->where('venue','like','%'.$filters['city'].'%'))
            ->groupBy('venue')->orderByDesc('count')->get();

        // Corporate sessions by project address
        $corpByCountry = CorporateProject::select('address', DB::raw('COUNT(*) as count'))
            ->whereNotNull('address')->groupBy('address')->orderByDesc('count')->get();

        // Invoice countries
        $invoiceByCountry = Invoice::select('client_country', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_paid) as paid'))
            ->whereNotNull('client_country')
            ->when($filters['date_from'] ?? null, fn($q) => $q->whereDate('invoice_date', '>=', $filters['date_from']))
            ->when($filters['date_to']   ?? null, fn($q) => $q->whereDate('invoice_date', '<=', $filters['date_to']))
            ->when($filters['country']   ?? null, fn($q) => $q->where('client_country', $filters['country']))
            ->groupBy('client_country')->orderByDesc('count')->get();

        $stats = [
            'countries_ilt'   => $iltByCountry->count(),
            'countries_el'    => $elByCountry->count(),
            'countries_inv'   => $invoiceByCountry->count(),
            'venues'          => $sessionsByVenue->count(),
            'participants_ilt'=> $iltByCountry->sum('count'),
            'participants_el' => $elByCountry->sum('count'),
            'sessions'        => $sessionsByVenue->sum('count'),
        ];

        $courses   = Course::orderBy('name')->get();
        $countries = collect($iltByCountry->pluck('country'))
            ->merge($elByCountry->pluck('country'))
            ->unique()->sort()->values();

        return view('reports.geographic', compact(
            'iltByCountry','elByCountry','sessionsByVenue','invoiceByCountry','corpByCountry',
            'stats','courses','countries','filters'
        ));
    }

    /* ══════════════════════════════════════════════
     |  E. EXPORT CENTER
     ══════════════════════════════════════════════ */
    public function exportCenter()
    {
        return view('reports.export-center');
    }

    /* ══════════════════════════════════════════════
     |  EXPORTS — eLearning
     ══════════════════════════════════════════════ */
    public function exportElearningCsv(Request $request)
    {
        $data = $this->getElearningData($request);
        return $this->streamCsv('elearning_report_'.now()->format('Ymd'), [
            ['#','Participant','Email','Company','Course','Payment Status','Amount','Completion','Certificate','Enrolled Date'],
        ], $data->map(fn($e, $i) => [
            $i+1, $e->participant_name, $e->email, $e->company,
            $e->course?->name, $e->payment_status, $e->amount,
            $e->completion_status, $e->certificate_status,
            $e->created_at?->format('d M Y'),
        ])->toArray());
    }

    public function exportElearningPdf(Request $request)
    {
        $filters     = $request->only(['date_from','date_to','course_id','company','payment_status','completion_status','certificate_status']);
        $enrollments = $this->getElearningData($request);
        $stats       = $this->calcElearningStats($enrollments);
        $pdf = Pdf::loadView('reports.pdf.elearning', compact('enrollments','stats','filters'))
                  ->setPaper('a4','landscape');
        return $pdf->download('elearning_report_'.now()->format('Ymd').'.pdf');
    }

    public function exportElearningExcel(Request $request)
    {
        $data = $this->getElearningData($request);
        return $this->streamExcel('elearning_report_'.now()->format('Ymd'), [
            ['#','Participant','Email','Company','Course','Payment Status','Amount','Completion','Certificate','Enrolled Date'],
        ], $data->map(fn($e, $i) => [
            $i+1, $e->participant_name, $e->email, $e->company,
            $e->course?->name, $e->payment_status, $e->amount,
            $e->completion_status, $e->certificate_status,
            $e->created_at?->format('d M Y'),
        ])->toArray());
    }

    /* ── ILT exports ────────────────────────────── */
    public function exportIltCsv(Request $request)
    {
        $data = $this->getIltData($request);
        return $this->streamCsv('ilt_report_'.now()->format('Ymd'), [
            ['#','Date','Course','Batch','Trainer','Venue','Participant','Company','Country','Attendance','Certificate'],
        ], $data->map(fn($e, $i) => [
            $i+1,
            $e->trainingSchedule?->start_date?->format('d M Y'),
            $e->trainingSchedule?->course?->name,
            $e->trainingSchedule?->batch_code,
            $e->trainingSchedule?->trainer?->name,
            $e->trainingSchedule?->venue,
            $e->full_name, $e->company, $e->country,
            $e->attendance_status,
            $e->certificate_generated ? 'Issued' : 'Pending',
        ])->toArray());
    }

    public function exportIltPdf(Request $request)
    {
        $filters     = $request->only(['date_from','date_to','course_id','trainer_id','company','country','attendance_status']);
        $enrollments = $this->getIltData($request);
        $stats       = $this->calcIltStats($enrollments);
        $pdf = Pdf::loadView('reports.pdf.ilt', compact('enrollments','stats','filters'))
                  ->setPaper('a4','landscape');
        return $pdf->download('ilt_report_'.now()->format('Ymd').'.pdf');
    }

    public function exportIltExcel(Request $request)
    {
        $data = $this->getIltData($request);
        return $this->streamExcel('ilt_report_'.now()->format('Ymd'), [
            ['#','Date','Course','Batch','Trainer','Venue','Participant','Company','Country','Attendance','Certificate'],
        ], $data->map(fn($e, $i) => [
            $i+1,
            $e->trainingSchedule?->start_date?->format('d M Y'),
            $e->trainingSchedule?->course?->name,
            $e->trainingSchedule?->batch_code,
            $e->trainingSchedule?->trainer?->name,
            $e->trainingSchedule?->venue,
            $e->full_name, $e->company, $e->country,
            $e->attendance_status,
            $e->certificate_generated ? 'Issued' : 'Pending',
        ])->toArray());
    }

    /* ── Financial exports ─────────────────────── */
    public function exportFinancialCsv(Request $request)
    {
        $data = $this->getFinancialData($request);
        return $this->streamCsv('financial_report_'.now()->format('Ymd'), [
            ['#','Date','Invoice No','Service Type','Client','Company','Country','Payment Method','Total Amount','Paid Amount','Due Amount','Status'],
        ], $data->map(fn($inv, $i) => [
            $i+1,
            $inv->invoice_date instanceof \Carbon\Carbon ? $inv->invoice_date->format('d M Y') : $inv->invoice_date,
            $inv->invoice_number,
            $inv->service_type,
            $inv->client_name,
            $inv->client_company,
            $inv->client_country,
            $inv->payment_method,
            $inv->total_amount,
            $inv->amount_paid,
            max(0, (float)$inv->total_amount - (float)$inv->amount_paid),
            $inv->payment_status,
        ])->toArray());
    }

    public function exportFinancialPdf(Request $request)
    {
        $filters  = $request->only(['date_from','date_to','service_type','payment_method','payment_status','company']);
        $invoices = $this->getFinancialData($request);
        $stats = [
            'total_amount' => $invoices->sum('total_amount'),
            'paid_amount'  => $invoices->sum('amount_paid'),
            'due_amount'   => $invoices->sum(fn($i) => max(0, (float)$i->total_amount - (float)$i->amount_paid)),
            'count'        => $invoices->count(),
        ];
        $pdf = Pdf::loadView('reports.pdf.financial', compact('invoices','stats','filters'))
                  ->setPaper('a4','landscape');
        return $pdf->download('financial_report_'.now()->format('Ymd').'.pdf');
    }

    public function exportFinancialExcel(Request $request)
    {
        $data = $this->getFinancialData($request);
        return $this->streamExcel('financial_report_'.now()->format('Ymd'), [
            ['#','Date','Invoice No','Service Type','Client','Company','Country','Payment Method','Total Amount','Paid Amount','Due Amount','Status'],
        ], $data->map(fn($inv, $i) => [
            $i+1,
            $inv->invoice_date instanceof \Carbon\Carbon ? $inv->invoice_date->format('d M Y') : $inv->invoice_date,
            $inv->invoice_number, $inv->service_type,
            $inv->client_name, $inv->client_company, $inv->client_country,
            $inv->payment_method, $inv->total_amount, $inv->amount_paid,
            max(0, (float)$inv->total_amount - (float)$inv->amount_paid),
            $inv->payment_status,
        ])->toArray());
    }

    /* ── Geographic exports ────────────────────── */
    public function exportGeographicCsv(Request $request)
    {
        $iltByCountry = Enrollment::select('country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country')->groupBy('country')->orderByDesc('count')->get();
        $elByCountry  = ElearningEnrollment::select('country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country')->groupBy('country')->orderByDesc('count')->get();

        return $this->streamCsv('geographic_report_'.now()->format('Ymd'), [
            ['Country','ILT Participants','eLearning Participants','Total'],
        ], collect($iltByCountry->pluck('country')->merge($elByCountry->pluck('country'))->unique())
            ->map(fn($c) => [
                $c,
                $iltByCountry->where('country',$c)->sum('count'),
                $elByCountry->where('country',$c)->sum('count'),
                $iltByCountry->where('country',$c)->sum('count') + $elByCountry->where('country',$c)->sum('count'),
            ])->sortByDesc(fn($r) => $r[3])->toArray());
    }

    public function exportGeographicPdf(Request $request)
    {
        $filters      = $request->only(['date_from','date_to','country']);
        $iltByCountry = Enrollment::select('country', DB::raw('COUNT(*) as count'))->whereNotNull('country')->groupBy('country')->orderByDesc('count')->get();
        $elByCountry  = ElearningEnrollment::select('country', DB::raw('COUNT(*) as count'))->whereNotNull('country')->groupBy('country')->orderByDesc('count')->get();
        $sessionsByVenue = \App\Models\TrainingSchedule::select('venue', DB::raw('COUNT(*) as count'))->whereNotNull('venue')->groupBy('venue')->orderByDesc('count')->get();

        $pdf = Pdf::loadView('reports.pdf.geographic', compact('iltByCountry','elByCountry','sessionsByVenue','filters'))
                  ->setPaper('a4','portrait');
        return $pdf->download('geographic_report_'.now()->format('Ymd').'.pdf');
    }

    /* ══════════════════════════════════════════════
     |  PRIVATE HELPERS
     ══════════════════════════════════════════════ */
    private function getElearningData(Request $request)
    {
        $f = $request->only(['date_from','date_to','course_id','company','payment_status','completion_status','certificate_status','q']);
        return ElearningEnrollment::with('course')
            ->when($f['date_from']          ?? null, fn($q) => $q->whereDate('created_at','>=',$f['date_from']))
            ->when($f['date_to']            ?? null, fn($q) => $q->whereDate('created_at','<=',$f['date_to']))
            ->when($f['course_id']          ?? null, fn($q) => $q->where('course_id',$f['course_id']))
            ->when($f['company']            ?? null, fn($q) => $q->where('company','like','%'.$f['company'].'%'))
            ->when($f['payment_status']     ?? null, fn($q) => $q->where('payment_status',$f['payment_status']))
            ->when($f['completion_status']  ?? null, fn($q) => $q->where('completion_status',$f['completion_status']))
            ->when($f['certificate_status'] ?? null, fn($q) => $q->where('certificate_status',$f['certificate_status']))
            ->when($f['q']                  ?? null, fn($q2) => $q2->where(fn($s) =>
                $s->where('participant_name','like','%'.$f['q'].'%')
                  ->orWhere('email','like','%'.$f['q'].'%')))
            ->orderByDesc('created_at')->get();
    }

    private function calcElearningStats($enrollments): array
    {
        return [
            'total'        => $enrollments->count(),
            'completed'    => $enrollments->where('completion_status','completed')->count(),
            'certificates' => $enrollments->where('certificate_status','issued')->count(),
            'paid_amount'  => $enrollments->whereIn('payment_status',['paid','manual_approved'])->sum('amount'),
        ];
    }

    private function getIltData(Request $request)
    {
        $f = $request->only(['date_from','date_to','course_id','trainer_id','company','country','venue','attendance_status','certificate_status','q']);
        return Enrollment::with('trainingSchedule.course','trainingSchedule.trainer')
            ->when($f['date_from']  ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->whereDate('start_date','>=',$f['date_from'])))
            ->when($f['date_to']    ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->whereDate('start_date','<=',$f['date_to'])))
            ->when($f['course_id']  ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->where('course_id',$f['course_id'])))
            ->when($f['trainer_id'] ?? null, fn($q) => $q->whereHas('trainingSchedule', fn($ts) => $ts->where('trainer_id',$f['trainer_id'])))
            ->when($f['company']    ?? null, fn($q) => $q->where('company','like','%'.$f['company'].'%'))
            ->when($f['country']    ?? null, fn($q) => $q->where('country',$f['country']))
            ->when($f['attendance_status'] ?? null, fn($q) => $q->where('attendance_status',$f['attendance_status']))
            ->when($f['certificate_status'] ?? null, fn($q) => match($f['certificate_status']) {
                'issued'  => $q->where('certificate_generated',true),
                'pending' => $q->where('certificate_generated',false),
                default   => $q,
            })
            ->when($f['q'] ?? null, fn($q2) => $q2->where(fn($s) =>
                $s->where('full_name','like','%'.$f['q'].'%')
                  ->orWhere('email','like','%'.$f['q'].'%')))
            ->orderByDesc('created_at')->get();
    }

    private function calcIltStats($enrollments): array
    {
        return [
            'total'        => $enrollments->count(),
            'present'      => $enrollments->where('attendance_status','Present')->count(),
            'absent'       => $enrollments->where('attendance_status','Absent')->count(),
            'certificates' => $enrollments->where('certificate_generated',true)->count(),
        ];
    }

    private function getFinancialData(Request $request)
    {
        $f = $request->only(['date_from','date_to','service_type','payment_method','payment_status','company','q']);
        return Invoice::query()
            ->when($f['date_from']     ?? null, fn($q) => $q->whereDate('invoice_date','>=',$f['date_from']))
            ->when($f['date_to']       ?? null, fn($q) => $q->whereDate('invoice_date','<=',$f['date_to']))
            ->when($f['service_type']  ?? null, fn($q) => $q->where('service_type',$f['service_type']))
            ->when($f['payment_method']?? null, fn($q) => $q->where('payment_method',$f['payment_method']))
            ->when($f['payment_status']?? null, fn($q) => $q->where('payment_status',$f['payment_status']))
            ->when($f['company']       ?? null, fn($q) => $q->where('client_company','like','%'.$f['company'].'%'))
            ->when($f['q']             ?? null, fn($q2) => $q2->where(fn($s) =>
                $s->where('invoice_number','like','%'.$f['q'].'%')
                  ->orWhere('client_name','like','%'.$f['q'].'%')))
            ->orderByDesc('invoice_date')->get();
    }

    /**
     * Stream a CSV response.
     * $header = [[col1, col2, ...]]  — header rows
     * $rows   = [[val1, val2, ...]]  — data rows
     */
    private function streamCsv(string $name, array $header, array $rows)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$name}.csv\"",
        ];
        return response()->stream(function () use ($header, $rows) {
            $h = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fputs($h, "\xEF\xBB\xBF");
            foreach (array_merge($header, $rows) as $row) {
                fputcsv($h, $row);
            }
            fclose($h);
        }, 200, $headers);
    }

    /**
     * Stream an Excel (.xlsx) using PhpSpreadsheet if available,
     * otherwise falls back to BOM-CSV with .xlsx mime.
     */
    private function streamExcel(string $name, array $header, array $rows)
    {
        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $rowNum = 1;
            foreach (array_merge($header, $rows) as $row) {
                $col = 1;
                foreach ($row as $val) {
                    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $val);
                }
                $rowNum++;
            }
            // Bold header
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($header[0]));
            $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $tmpFile = tempnam(sys_get_temp_dir(), 'xl_') . '.xlsx';
            $writer->save($tmpFile);

            return response()->download($tmpFile, "{$name}.xlsx", [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        }

        // Fallback to BOM-CSV
        return $this->streamCsv($name, $header, $rows);
    }
}
