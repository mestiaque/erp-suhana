<?php return array (
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => '12',
      'verify' => true,
      'limit' => NULL,
    ),
    'argon' => 
    array (
      'memory' => 65536,
      'threads' => 1,
      'time' => 4,
      'verify' => true,
    ),
    'rehash_on_login' => true,
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'concurrency' => 
  array (
    'default' => 'process',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/home/erpanrf/public_html/resources/views',
    ),
    'compiled' => '/home/erpanrf/public_html/storage/framework/views',
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'reverb' => 
      array (
        'driver' => 'reverb',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'host' => NULL,
          'port' => 443,
          'scheme' => 'https',
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'cluster' => NULL,
          'host' => 'api-mt1.pusher.com',
          'port' => 443,
          'scheme' => 'https',
          'encrypted' => true,
          'useTLS' => true,
        ),
        'client_options' => 
        array (
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'app' => 
  array (
    'name' => 'anrfashion',
    'env' => 'production',
    'debug' => true,
    'url' => 'https://erp.anrfashion.com/',
    'frontend_url' => 'http://localhost:3000',
    'asset_url' => NULL,
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'cipher' => 'AES-256-CBC',
    'key' => 'base64:iue4wtzYqDHKo7dy7txaWqmy1CnHDxT+w/DXuAS4naw=',
    'previous_keys' => 
    array (
    ),
    'maintenance' => 
    array (
      'driver' => 'file',
      'store' => 'database',
    ),
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Concurrency\\ConcurrencyServiceProvider',
      6 => 'Illuminate\\Cookie\\CookieServiceProvider',
      7 => 'Illuminate\\Database\\DatabaseServiceProvider',
      8 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      9 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      10 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      11 => 'Illuminate\\Hashing\\HashServiceProvider',
      12 => 'Illuminate\\Mail\\MailServiceProvider',
      13 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      14 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      15 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      16 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      17 => 'Illuminate\\Queue\\QueueServiceProvider',
      18 => 'Illuminate\\Redis\\RedisServiceProvider',
      19 => 'Illuminate\\Session\\SessionServiceProvider',
      20 => 'Illuminate\\Translation\\TranslationServiceProvider',
      21 => 'Illuminate\\Validation\\ValidationServiceProvider',
      22 => 'Illuminate\\View\\ViewServiceProvider',
      23 => 'App\\Providers\\AppServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Benchmark' => 'Illuminate\\Support\\Benchmark',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Concurrency' => 'Illuminate\\Support\\Facades\\Concurrency',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Context' => 'Illuminate\\Support\\Facades\\Context',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'Date' => 'Illuminate\\Support\\Facades\\Date',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Js' => 'Illuminate\\Support\\Js',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Number' => 'Illuminate\\Support\\Number',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Process' => 'Illuminate\\Support\\Facades\\Process',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'RateLimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schedule' => 'Illuminate\\Support\\Facades\\Schedule',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'Uri' => 'Illuminate\\Support\\Uri',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Vite' => 'Illuminate\\Support\\Facades\\Vite',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'sanctum' => 
      array (
        'driver' => 'sanctum',
        'provider' => NULL,
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'session' => 
      array (
        'driver' => 'session',
        'key' => '_cache',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'connection' => NULL,
        'table' => 'cache',
        'lock_connection' => NULL,
        'lock_table' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/home/erpanrf/public_html/storage/framework/cache/data',
        'lock_path' => '/home/erpanrf/public_html/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
      'octane' => 
      array (
        'driver' => 'octane',
      ),
      'failover' => 
      array (
        'driver' => 'failover',
        'stores' => 
        array (
          0 => 'database',
          1 => 'array',
        ),
      ),
    ),
    'prefix' => 'anrfashion-cache-',
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'erpanrf_erp',
        'prefix' => '',
        'foreign_key_constraints' => true,
        'busy_timeout' => NULL,
        'journal_mode' => NULL,
        'synchronous' => NULL,
        'transaction_mode' => 'DEFERRED',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'erpanrf_erp',
        'username' => 'erpanrf_erp',
        'password' => 'L#zv!oetNHzelqZh',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'mariadb' => 
      array (
        'driver' => 'mariadb',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'erpanrf_erp',
        'username' => 'erpanrf_erp',
        'password' => 'L#zv!oetNHzelqZh',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'erpanrf_erp',
        'username' => 'erpanrf_erp',
        'password' => 'L#zv!oetNHzelqZh',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'erpanrf_erp',
        'username' => 'erpanrf_erp',
        'password' => 'L#zv!oetNHzelqZh',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 
    array (
      'table' => 'migrations',
      'update_date_on_publish' => true,
    ),
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'anrfashion-database-',
        'persistent' => false,
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
        'max_retries' => 3,
        'backoff_algorithm' => 'decorrelated_jitter',
        'backoff_base' => 100,
        'backoff_cap' => 1000,
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'username' => NULL,
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
        'max_retries' => 3,
        'backoff_algorithm' => 'decorrelated_jitter',
        'backoff_base' => 100,
        'backoff_cap' => 1000,
      ),
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/home/erpanrf/public_html/storage/app/private',
        'serve' => true,
        'throw' => false,
        'report' => false,
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/home/erpanrf/public_html/storage/app/public',
        'url' => 'https://erp.anrfashion.com//storage',
        'visibility' => 'public',
        'throw' => false,
        'report' => false,
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
        'use_path_style_endpoint' => false,
        'throw' => false,
        'report' => false,
      ),
    ),
    'links' => 
    array (
      '/home/erpanrf/public_html/public/storage' => '/home/erpanrf/public_html/storage/app/public',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'deprecations' => 
    array (
      'channel' => NULL,
      'trace' => false,
    ),
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/home/erpanrf/public_html/storage/logs/laravel.log',
        'level' => 'info',
        'replace_placeholders' => true,
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/home/erpanrf/public_html/storage/logs/laravel.log',
        'level' => 'info',
        'days' => 14,
        'replace_placeholders' => true,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'info',
        'replace_placeholders' => true,
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'info',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
          'connectionString' => 'tls://:',
        ),
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'info',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'handler_with' => 
        array (
          'stream' => 'php://stderr',
        ),
        'formatter' => NULL,
        'processors' => 
        array (
          0 => 'Monolog\\Processor\\PsrLogMessageProcessor',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'info',
        'facility' => 8,
        'replace_placeholders' => true,
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'info',
        'replace_placeholders' => true,
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => '/home/erpanrf/public_html/storage/logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'scheme' => NULL,
        'url' => NULL,
        'host' => 'mail.nit.hostrb.com',
        'port' => '465',
        'username' => 'demo@nit.hostrb.com',
        'password' => 'Dhaka12!@#',
        'timeout' => NULL,
        'local_domain' => 'erp.anrfashion.com',
        'encryption' => 'ssl',
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'resend' => 
      array (
        'transport' => 'resend',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs -i',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
      'failover' => 
      array (
        'transport' => 'failover',
        'mailers' => 
        array (
          0 => 'smtp',
          1 => 'log',
        ),
        'retry_after' => 60,
      ),
      'roundrobin' => 
      array (
        'transport' => 'roundrobin',
        'mailers' => 
        array (
          0 => 'ses',
          1 => 'postmark',
        ),
        'retry_after' => 60,
      ),
    ),
    'from' => 
    array (
      'address' => 'your_email@yourdomain.com',
      'name' => 'anrfashion',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/home/erpanrf/public_html/resources/views/vendor/mail',
      ),
    ),
  ),
  'permission' => 
  array (
    'modules' => 
    array (
      'company' => 
      array (
        'label' => 'Customers Management',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'export' => 'Export',
          'sales' => 'Sales',
          'duecollect' => 'Due Collect',
          'service' => 'Services',
          'all' => 'All',
        ),
      ),
      'engineers' => 
      array (
        'label' => 'Engineers Management',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'export' => 'Export',
          'all' => 'All',
        ),
      ),
      'leads' => 
      array (
        'label' => 'Leads Management',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'all' => 'All',
        ),
      ),
      'tasks' => 
      array (
        'label' => 'Task Management',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'all' => 'All',
        ),
      ),
      'meetings' => 
      array (
        'label' => 'Meeting Management',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'all' => 'All',
        ),
      ),
      'visits' => 
      array (
        'label' => 'Visit Management',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'all' => 'All',
        ),
      ),
      'sales' => 
      array (
        'label' => 'Sales Invoice',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'all' => 'All',
        ),
      ),
      'quotation' => 
      array (
        'label' => 'Quotation',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'all' => 'All',
        ),
      ),
      'expenses' => 
      array (
        'label' => 'expenses',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'type' => 'Type',
          'all' => 'All',
        ),
      ),
      'accounts' => 
      array (
        'label' => 'Accounts',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'type' => 'Type',
          'all' => 'All',
        ),
      ),
      'paymentMethod' => 
      array (
        'label' => 'Payment Method',
        'permissions' => 
        array (
          'add' => 'Create/Update',
          'view' => 'View',
          'delete' => 'Delete',
          'type' => 'Type',
          'all' => 'All',
        ),
      ),
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'connection' => NULL,
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => '',
        'secret' => '',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'default',
        'suffix' => NULL,
        'region' => 'us-east-1',
        'after_commit' => false,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'after_commit' => false,
      ),
      'deferred' => 
      array (
        'driver' => 'deferred',
      ),
      'failover' => 
      array (
        'driver' => 'failover',
        'connections' => 
        array (
          0 => 'database',
          1 => 'deferred',
        ),
      ),
      'background' => 
      array (
        'driver' => 'background',
      ),
    ),
    'batching' => 
    array (
      'database' => 'mysql',
      'table' => 'job_batches',
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'postmark' => 
    array (
      'key' => NULL,
    ),
    'resend' => 
    array (
      'key' => NULL,
    ),
    'ses' => 
    array (
      'key' => '',
      'secret' => '',
      'region' => 'us-east-1',
    ),
    'slack' => 
    array (
      'notifications' => 
      array (
        'bot_user_oauth_token' => NULL,
        'channel' => NULL,
      ),
    ),
    'facebook' => 
    array (
      'client_id' => '#',
      'client_secret' => '#',
      'redirect' => '#',
    ),
    'google' => 
    array (
      'client_id' => NULL,
      'client_secret' => '#',
      'redirect' => '#',
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/home/erpanrf/public_html/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'anrfashion-session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
  ),
  'sidebar' => 
  array (
    0 => 
    array (
      'group_title' => 'MAIN',
      0 => 
      array (
        'title' => 'Dashboard',
        'icon' => 'fa-solid fa-gauge-high',
        'route' => '/admin/dashboard',
        'permission' => '',
      ),
      1 => 
      array (
        'title' => 'My Profile',
        'icon' => 'fa-solid fa-user',
        'route' => '/admin/my-profile',
        'permission' => '',
      ),
    ),
    1 => 
    array (
      'group_title' => '',
      0 => 
      array (
        'title' => 'Purchases Management',
        'icon' => 'fa-solid fa-store',
        'icon_color' => 'text-primary',
        'permission' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => 'Purchases',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/purchases-orders',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          1 => 
          array (
            'title' => 'Creditor List',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/suppliers',
            'icon_color' => 'text-primary',
            'permission' => '',
          ),
          2 => 
          array (
            'title' => 'Goods Items',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/purchases-items',
            'icon_color' => 'text-primary',
            'permission' => '',
          ),
          3 => 
          array (
            'title' => 'Requisitions',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/purchases-requisitions',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          4 => 
          array (
            'title' => 'Goods Receive (GRN)',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/purchases-received',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          5 => 
          array (
            'title' => 'Damages / Returns',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/purchases-damage-returns',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          6 => 
          array (
            'title' => 'Supplier Ledgers',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/suppliers-ladgers',
            'icon_color' => 'text-primary',
            'permission' => '',
          ),
          7 => 
          array (
            'title' => 'Purchase Reports',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/purchases-reports',
            'icon_color' => 'text-primary',
            'permission' => '',
          ),
          8 => 
          array (
            'title' => 'Purchase Stock',
            'icon' => 'fa-solid fa-arrow-right',
            'route' => '/admin/purchases-stocks',
            'icon_color' => 'text-primary',
            'permission' => '',
          ),
        ),
      ),
    ),
    2 => 
    array (
      'group_title' => '',
      0 => 
      array (
        'title' => 'Accounts Management',
        'icon' => 'fa-solid fa-cogs',
        'icon_color' => 'text-primary',
        'permission' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => 'Expenses List',
            'icon' => 'fa-solid fa-list',
            'route' => '/admin/expenses',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          1 => 
          array (
            'title' => 'Expense Head',
            'icon' => 'fa-solid fa-layer-group',
            'route' => '/admin/expenses/types',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          2 => 
          array (
            'title' => 'Expense Reports',
            'icon' => 'fa-solid fa-layer-group',
            'route' => '/admin/expenses/reports',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          3 => 
          array (
            'title' => 'I.O.U List',
            'icon' => 'fa-solid fa-layer-group',
            'route' => '/admin/expenses/iou',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          4 => 
          array (
            'title' => 'I.O.U Reports',
            'icon' => 'fa-solid fa-layer-group',
            'route' => '/admin/expenses/iou-reports',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          5 => 
          array (
            'title' => 'Payment Method',
            'icon' => 'fa-solid fa-credit-card',
            'route' => '/admin/accounts/payment-methods',
            'icon_color' => 'text-primary',
            'permission' => '',
          ),
          6 => 
          array (
            'title' => 'Account List',
            'icon' => 'fa-solid fa-list',
            'route' => '/admin/accounts/list',
            'icon_color' => 'text-primary',
            'permission' => '',
          ),
          7 => 
          array (
            'title' => 'Bill Payment',
            'icon' => 'fas fa-credit-card',
            'route' => '/admin/bill-payments',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          8 => 
          array (
            'title' => 'Bill Collection',
            'icon' => 'fas fa-wallet',
            'route' => '/admin/bill-collections',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          9 => 
          array (
            'title' => 'Deposits',
            'icon' => 'fas fa-wallet',
            'route' => '/admin/accounts/deposits',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          10 => 
          array (
            'title' => 'Withdrawal',
            'icon' => 'fas fa-wallet',
            'route' => '/admin/accounts/withdrawal',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
          11 => 
          array (
            'title' => 'Statement',
            'icon' => 'fas fa-wallet',
            'route' => '/admin/accounts/statement',
            'icon_color' => 'text-warning',
            'permission' => '',
          ),
        ),
      ),
    ),
    3 => 
    array (
      'group_title' => '',
      0 => 
      array (
        'title' => 'HR / User Management',
        'icon' => 'fa-solid fa-users',
        'icon_color' => 'text-success',
        'permission' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => 'Employee List',
            'icon' => 'fa-solid fa-id-badge',
            'route' => '/admin/users/employee',
            'icon_color' => 'text-success',
            'permission' => '',
          ),
          1 => 
          array (
            'title' => 'Staff List',
            'icon' => 'fa-solid fa-user-tie',
            'route' => '/admin/users/staff',
            'icon_color' => 'text-success',
            'permission' => '',
          ),
          2 => 
          array (
            'title' => 'Admin List',
            'icon' => 'fa-solid fa-user-shield',
            'route' => '/admin/users/admin',
            'icon_color' => 'text-success',
            'permission' => '',
          ),
          3 => 
          array (
            'title' => 'Roles Setup',
            'icon' => 'fa-solid fa-user-gear',
            'route' => '/admin/users/roles',
            'icon_color' => 'text-success',
            'permission' => '',
          ),
          4 => 
          array (
            'title' => 'Branch/Factory',
            'icon' => 'fa-solid fa-building',
            'route' => '/admin/hr/branchs',
            'icon_color' => 'text-info',
            'permission' => '',
          ),
          5 => 
          array (
            'title' => 'Departments',
            'icon' => 'fa-solid fa-sitemap',
            'route' => '/admin/hr/departments',
            'icon_color' => 'text-info',
            'permission' => '',
          ),
          6 => 
          array (
            'title' => 'Designation',
            'icon' => 'fa-solid fa-id-card-clip',
            'route' => '/admin/hr/designations',
            'icon_color' => 'text-info',
            'permission' => '',
          ),
        ),
      ),
    ),
    4 => 
    array (
      'group_title' => 'APP SETTING',
      0 => 
      array (
        'title' => 'Setting',
        'icon' => 'fa-solid fa-sliders-h',
        'icon_color' => 'text-secondary',
        'permission' => '',
        'children' => 
        array (
          0 => 
          array (
            'title' => 'General Setting',
            'icon' => 'fa-solid fa-cog',
            'route' => '/admin/setting/general',
            'icon_color' => 'text-secondary',
            'permission' => '',
          ),
          1 => 
          array (
            'title' => 'Mail Setting',
            'icon' => 'fa-solid fa-envelope',
            'route' => '/admin/setting/mail',
            'icon_color' => 'text-secondary',
            'permission' => '',
          ),
          2 => 
          array (
            'title' => 'SMS Setting',
            'icon' => 'fa-solid fa-sms',
            'route' => '/admin/setting/sms',
            'icon_color' => 'text-secondary',
            'permission' => '',
          ),
        ),
      ),
    ),
  ),
  'sanctum' => 
  array (
    'stateful' => 
    array (
      0 => 'localhost',
      1 => 'localhost:3000',
      2 => '127.0.0.1',
      3 => '127.0.0.1:8000',
      4 => '::1',
      5 => 'erp.anrfashion.com',
    ),
    'guard' => 
    array (
      0 => 'web',
    ),
    'expiration' => NULL,
    'token_prefix' => '',
    'middleware' => 
    array (
      'authenticate_session' => 'Laravel\\Sanctum\\Http\\Middleware\\AuthenticateSession',
      'encrypt_cookies' => 'Illuminate\\Cookie\\Middleware\\EncryptCookies',
      'validate_csrf_token' => 'Illuminate\\Foundation\\Http\\Middleware\\ValidateCsrfToken',
    ),
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
