<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        $settings  = NotificationSetting::orderBy('group')->orderBy('id')->get()->groupBy('group');
        $recentLogs = EmailLog::orderBy('created_at', 'desc')->limit(50)->get();

        return view('settings.notifications', compact('settings', 'recentLogs'));
    }

    public function toggle(Request $request, NotificationSetting $setting)
    {
        $setting->update(['enabled' => !$setting->enabled]);
        Cache::forget("notif_setting_{$setting->key}");

        return back()->with('success', '"' . $setting->label . '" ' . ($setting->enabled ? 'enabled' : 'disabled') . '.');
    }

    public function toggleAll(Request $request)
    {
        $group   = $request->group;
        $enabled = (bool) $request->enabled;

        $query = NotificationSetting::query();
        if ($group) $query->where('group', $group);
        $settings = $query->get();

        foreach ($settings as $s) {
            $s->update(['enabled' => $enabled]);
            Cache::forget("notif_setting_{$s->key}");
        }

        return back()->with('success', 'All ' . ($group ? $group : '') . ' notifications ' . ($enabled ? 'enabled' : 'disabled') . '.');
    }
}
