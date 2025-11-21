<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public static function log(
        string $action,
        ?string $description = null,
        ?string $tableName = null,
        ?int $recordId = null,
        ?array $requestPayload = null,
        ?array $responsePayload = null
    ): ActivityLog {
        $user = Auth::user();
        $userIdentifier = $user ? ($user->matricule ?? $user->id) : 'system';

        return ActivityLog::create([
            'user_identifier' => $userIdentifier,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'request_payload' => $requestPayload,
            'response_payload' => $responsePayload,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $description,
        ]);
    }

    public static function logModelAction($model, string $action, ?array $payload = null): ActivityLog
    {
        $tableName = $model->getTable();
        $recordId = $model->id;

        return self::log(
            $action,
            "{$action} on {$tableName} #{$recordId}",
            $tableName,
            $recordId,
            $payload
        );
    }
}

