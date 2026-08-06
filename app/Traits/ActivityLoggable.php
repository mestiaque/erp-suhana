<?php

namespace App\Traits;

/**
 * Deprecated: kept only so models across other packages (acc-sfl, hr-new)
 * that still `use ActivityLoggable;` keep working without edits.
 *
 * Actual audit logging is now handled globally, for every Eloquent model,
 * by App\Providers\AuditLogServiceProvider — so this trait intentionally
 * does nothing anymore (no boot hook), to avoid duplicate log entries.
 */
trait ActivityLoggable
{
    //
}
