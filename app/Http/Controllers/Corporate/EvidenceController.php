<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CorporateSession;
use App\Models\CorporateEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    public function index(CorporateSession $session)
    {
        $evidences = $session->evidences()->latest()->get();
        return view('corporate.evidence.index', compact('session', 'evidences'));
    }

    public function store(Request $request, CorporateSession $session)
    {
        $request->validate([
            'type'    => 'required|in:Training Photo,Group Photo,Presentation,Document,Other',
            'files.*' => 'required|file|max:10240',
            'caption' => 'nullable|string|max:200',
        ]);

        foreach ($request->file('files', []) as $file) {
            $path = $file->store('corporate/evidence/' . $session->id, 'public');
            CorporateEvidence::create([
                'corporate_session_id' => $session->id,
                'type'           => $request->type,
                'file_path'      => $path,
                'original_name'  => $file->getClientOriginalName(),
                'caption'        => $request->caption,
            ]);
        }

        return back()->with('success', 'Evidence uploaded.');
    }

    public function destroy(CorporateSession $session, CorporateEvidence $evidence)
    {
        Storage::disk('public')->delete($evidence->file_path);
        $evidence->delete();
        return back()->with('success', 'Evidence removed.');
    }
}
