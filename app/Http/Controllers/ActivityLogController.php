<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display the activity log index page
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'action',
            'model_type',
            'user_id',
            'start_date',
            'end_date',
            'search'
        ]);

        $logs = ActivityLogService::getLogs($filters)
            ->paginate(20)
            ->withQueryString();

        // Get filter options for the form
        $actions = \App\Models\ActivityLog::distinct()->pluck('action')->sort();
        $modelTypes = \App\Models\ActivityLog::distinct()->pluck('model_type')->sort();
        $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);

        return view('activity-logs.index', compact('logs', 'filters', 'actions', 'modelTypes', 'users'));
    }

    /**
     * Show a specific activity log entry
     */
    public function show(\App\Models\ActivityLog $activityLog): View
    {
        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $filters = $request->only([
            'action',
            'model_type',
            'user_id',
            'start_date',
            'end_date',
            'search'
        ]);

        $logs = ActivityLogService::getLogs($filters)->get();

        // Log the export activity
        ActivityLogService::logExport('ActivityLog', 'Exported activity logs');

        $filename = 'activity-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'User',
                'Action',
                'Model Type',
                'Model ID',
                'Description',
                'IP Address',
                'Created At'
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user->name ?? 'Unknown',
                    $log->action_label,
                    $log->model_type_label,
                    $log->model_id,
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear old activity logs
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = $request->input('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = \App\Models\ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        // Log the cleanup activity
        ActivityLogService::log(
            'cleanup',
            "Cleared {$deletedCount} activity logs older than {$days} days"
        );

        return redirect()
            ->route('activity-logs.index')
            ->with('success', "Successfully cleared {$deletedCount} old activity logs.");
    }
}
