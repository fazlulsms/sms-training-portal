<?php

namespace App\Http\Controllers;

use App\Models\AiUsageLog;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private OpenAIService $ai) {}

    // ── Super-admin gate ──────────────────────────────────────

    private function guardSuperAdmin()
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'AI administration is restricted to Super Admins.');
        }
    }

    // ── AI Settings ───────────────────────────────────────────

    public function settings()
    {
        $this->guardSuperAdmin();

        $todayCount    = AiUsageLog::today()->count();
        $monthCount    = AiUsageLog::thisMonth()->count();
        $monthCostUsd  = AiUsageLog::thisMonth()->successful()->sum('estimated_cost_usd');
        $totalCount    = AiUsageLog::count();
        $recentLogs    = AiUsageLog::with('user')->latest()->take(10)->get();

        return view('ai.settings', compact(
            'todayCount', 'monthCount', 'monthCostUsd', 'totalCount', 'recentLogs'
        ));
    }

    // ── AI Test ───────────────────────────────────────────────

    public function test()
    {
        $this->guardSuperAdmin();
        return view('ai.test');
    }

    public function runTest(Request $request)
    {
        $this->guardSuperAdmin();

        $request->validate([
            'prompt' => 'required|string|min:10|max:4000',
        ]);

        $result = $this->ai->generateText(
            prompt:  $request->input('prompt'),
            feature: 'test',
            userId:  auth()->id(),
        );

        return response()->json($result);
    }
}
