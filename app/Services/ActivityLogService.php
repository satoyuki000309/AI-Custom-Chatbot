<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $description,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'session_id' => Request::session()->getId(),
        ]);
    }

    /**
     * Log a creation activity
     */
    public static function logCreate(Model $model, string $description = null): ActivityLog
    {
        $description = $description ?? 'Created new ' . class_basename($model);

        return self::log(
            'create',
            $description,
            $model,
            null,
            $model->toArray()
        );
    }

    /**
     * Log an update activity
     */
    public static function logUpdate(Model $model, array $oldValues, array $newValues, string $description = null): ActivityLog
    {
        $description = $description ?? 'Updated ' . class_basename($model);

        return self::log(
            'update',
            $description,
            $model,
            $oldValues,
            $newValues
        );
    }

    /**
     * Log a deletion activity
     */
    public static function logDelete(Model $model, string $description = null): ActivityLog
    {
        $description = $description ?? 'Deleted ' . class_basename($model);

        return self::log(
            'delete',
            $description,
            $model,
            $model->toArray(),
            null
        );
    }

    /**
     * Log a view activity
     */
    public static function logView(Model $model, string $description = null): ActivityLog
    {
        $description = $description ?? 'Viewed ' . class_basename($model);

        return self::log(
            'view',
            $description,
            $model
        );
    }

    /**
     * Log a login activity
     */
    public static function logLogin(string $description = null): ActivityLog
    {
        $description = $description ?? 'User logged in';

        return self::log(
            'login',
            $description
        );
    }

    /**
     * Log a logout activity
     */
    public static function logLogout(string $description = null): ActivityLog
    {
        $description = $description ?? 'User logged out';

        return self::log(
            'logout',
            $description
        );
    }

    /**
     * Log an export activity
     */
    public static function logExport(string $modelType, string $description = null): ActivityLog
    {
        $description = $description ?? "Exported {$modelType} data";

        return self::log(
            'export',
            $description,
            null
        );
    }

    /**
     * Log an import activity
     */
    public static function logImport(string $modelType, int $count, string $description = null): ActivityLog
    {
        $description = $description ?? "Imported {$count} {$modelType} records";

        return self::log(
            'import',
            $description,
            null,
            null,
            ['count' => $count]
        );
    }

    /**
     * Get activity logs with filters
     */
    public static function getLogs(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by action
        if (isset($filters['action'])) {
            $query->byAction($filters['action']);
        }

        // Filter by model type
        if (isset($filters['model_type'])) {
            $query->byModel($filters['model_type']);
        }

        // Filter by user
        if (isset($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        // Filter by date range
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        // Filter by search term
        if (isset($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query;
    }
}
