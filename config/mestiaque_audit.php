<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Master switch. When false, HasAudit observers, auth listeners and the
    | admin UI are never registered, and the manual Audit facade becomes a
    | no-op. Overhead when disabled should be effectively zero.
    |
    */
    'enabled' => env('AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'connection' => env('AUDIT_DB_CONNECTION'),
        'table' => 'es_audit_logs',
    ],

    'sessions' => [
        'table' => 'es_audit_sessions',

        // Minimum seconds between last_activity_at updates for the same
        // session, to avoid a write on every single request.
        'activity_throttle_seconds' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auditable models
    |--------------------------------------------------------------------------
    |
    | Models that `use HasAudit` are always audited. These options add
    | optional, blanket controls on top of that opt-in mechanism.
    |
    */
    'exclude_models' => [
        // \App\Models\SomeNoisyModel::class,
    ],

    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],

    /*
    |--------------------------------------------------------------------------
    | Parent/child nesting
    |--------------------------------------------------------------------------
    */
    'max_parent_depth' => 3,

    /*
    |--------------------------------------------------------------------------
    | Authentication & sessions
    |--------------------------------------------------------------------------
    */
    'track_authentication' => true,

    'track_sessions' => true,

    // Guards to attach login/logout/failed-login listeners to. Empty = all
    // guards configured in config('auth.guards').
    'guards' => [],

    /*
    |--------------------------------------------------------------------------
    | Request/response tracking
    |--------------------------------------------------------------------------
    */
    'track_requests' => true,

    'track_ip' => true,

    'track_location' => true,

    'track_device' => true,

    'track_user_agent' => true,

    'track_url' => true,

    'track_route' => true,

    'track_query_string' => false,

    // Request body logging is OFF by default: it can contain passwords,
    // tokens, files and other personal data. See README "Request Data".
    'request_data' => [
        'enabled' => false,
        'allow' => [],
        'deny' => [],
        'mask' => [],
        'max_bytes' => 10_000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted proxies for IP resolution
    |--------------------------------------------------------------------------
    |
    | Left null, the package trusts Laravel's own Request::ip() resolution
    | (which already honours the app's TrustProxies middleware). Set this to
    | restrict which proxy headers are honoured independently of that.
    |
    */
    'trusted_proxies' => null,

    /*
    |--------------------------------------------------------------------------
    | IP location resolver
    |--------------------------------------------------------------------------
    |
    | Bind ME\Audit\Contracts\IpLocationResolver to your own
    | implementation to enable country/city resolution. No provider is
    | bundled; the default resolver always returns a null location.
    |
    */
    'ip_location' => [
        'driver' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive data protection
    |--------------------------------------------------------------------------
    */
    'exclude_fields' => [
        'password',
        'password_confirmation',
        'remember_token',
        'access_token',
        'refresh_token',
        'token',
        'secret',
        'api_key',
        'api_token',
        'authorization',
        'cookie',
    ],

    'mask_fields' => [
        // 'card_number',
        // 'nid',
        // 'bank_account',
    ],

    'ignore_fields' => [
        'created_at',
        'updated_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue mode
    |--------------------------------------------------------------------------
    |
    | When enabled, the fully-resolved AuditBatch DTO (never a live Eloquent
    | model) is dispatched to a job for persistence instead of writing
    | synchronously inside the request/transaction-commit cycle.
    |
    */
    'queue' => [
        'enabled' => false,
        'connection' => null,
        'queue' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Failure handling
    |--------------------------------------------------------------------------
    |
    | What happens when persisting an audit batch itself throws (e.g. the
    | audit database is temporarily unavailable). This must never take down
    | the host application's primary business operation.
    |
    | Supported: "silent", "log", "throw"
    |
    */
    'audit_failure' => env('AUDIT_FAILURE_MODE', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Retention / pruning
    |--------------------------------------------------------------------------
    */
    'retention' => [
        'enabled' => false,
        'days' => 365,
        'chunk_size' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin UI
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'enabled' => true,
        'route_prefix' => 'audit',
        'route_middleware' => ['web'],
        'per_page' => 25,
    ],

    'authorization' => [
        'enabled' => true,

        // Ability name checked via Gate::allows() against the authenticated
        // user. Register it in your AuthServiceProvider, or override the
        // whole check by binding ME\Audit\Contracts\ActorResolver /
        // by publishing AuthorizeAuditAccess.
        'ability' => 'viewAuditLogs',
    ],

];
