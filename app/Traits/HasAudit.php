<?php

namespace App\Traits;

/**
 * Local wrapper around ME\Audit\Traits\HasAudit — every model across this
 * app and its packages (hr-new, acc-sfl, sfl-inventory, production-trace,
 * merchandising-trace) `use App\Traits\HasAudit;` instead of importing the
 * vendor trait directly.
 *
 * Why: a PHP `use SomeTrait;` fails at class-load time if the trait class
 * doesn't exist — a bare `composer remove mestiaque/audit` would fatal
 * every one of the 230+ models using it, i.e. the whole app. Routing all
 * of them through this local trait, which only ever *references* the
 * package's classes by string (`class_exists()`, `::class`), means the
 * package can be removed and every model keeps working — auditing simply
 * turns itself off.
 */
trait HasAudit
{
    public static function bootHasAudit(): void
    {
        if (! class_exists(\ME\Audit\Observers\AuditObserver::class)) {
            return;
        }

        if (! config('mestiaque_audit.enabled', true)) {
            return;
        }

        if (in_array(static::class, config('mestiaque_audit.exclude_models', []), true)) {
            return;
        }

        static::observe(\ME\Audit\Observers\AuditObserver::class);
    }

    public function getAuditInclude(): array
    {
        return $this->auditInclude ?? [];
    }

    public function getAuditExclude(): array
    {
        return $this->auditExclude ?? [];
    }

    public function getAuditRelations(): array
    {
        return $this->auditRelations ?? [];
    }

    public function getAuditLabelField(): ?string
    {
        return $this->auditLabel ?? null;
    }

    /**
     * Override on the model to fully control its audit label; returning
     * null/blank falls through to the rest of LabelResolver's chain.
     */
    public function getAuditLabel(): ?string
    {
        return null;
    }
}
