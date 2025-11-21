<?php

use App\Services\ActivityLogService;

if (!function_exists('logActivity')) {
    function logActivity(
        string $action,
        ?string $description = null,
        ?string $tableName = null,
        ?int $recordId = null,
        ?array $requestPayload = null,
        ?array $responsePayload = null
    ) {
        return ActivityLogService::log(
            $action,
            $description,
            $tableName,
            $recordId,
            $requestPayload,
            $responsePayload
        );
    }
}

if (!function_exists('logModelAction')) {
    function logModelAction($model, string $action, ?array $payload = null)
    {
        return ActivityLogService::logModelAction($model, $action, $payload);
    }
}

