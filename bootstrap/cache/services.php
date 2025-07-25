<?php return array (
  'providers' => 
  array (
    0 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
    1 => 'Illuminate\\Database\\DatabaseServiceProvider',
    2 => 'Illuminate\\Database\\MigrationServiceProvider',
    3 => 'Illuminate\\Foundation\\Providers\\ComposerServiceProvider',
    4 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
    5 => 'Illuminate\\Redis\\RedisServiceProvider',
    6 => 'Illuminate\\Cache\\CacheServiceProvider',
    7 => 'Illuminate\\Session\\SessionServiceProvider',
    8 => 'Illuminate\\Queue\\QueueServiceProvider',
    9 => 'Illuminate\\Log\\LogServiceProvider',
    10 => 'Illuminate\\Mail\\MailServiceProvider',
    11 => 'Illuminate\\Routing\\RoutingServiceProvider',
    12 => 'Illuminate\\Translation\\TranslationServiceProvider',
    13 => 'Illuminate\\Validation\\ValidationServiceProvider',
    14 => 'Illuminate\\View\\ViewServiceProvider',
    15 => 'Illuminate\\Hashing\\HashServiceProvider',
    16 => 'L5Swagger\\L5SwaggerServiceProvider',
    17 => 'Laravel\\Sail\\SailServiceProvider',
    18 => 'Laravel\\Tinker\\TinkerServiceProvider',
    19 => 'Carbon\\Laravel\\ServiceProvider',
    20 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    21 => 'Termwind\\Laravel\\TermwindServiceProvider',
    22 => 'Spatie\\LaravelIgnition\\IgnitionServiceProvider',
    23 => 'L5Swagger\\L5SwaggerServiceProvider',
    24 => 'App\\Infrastructure\\Providers\\AppServiceProvider',
    25 => 'App\\Infrastructure\\Providers\\RouteServiceProvider',
    26 => 'App\\Modules\\Products\\Providers\\ProductsServiceProvider',
    27 => 'App\\Modules\\Cart\\Providers\\CartServiceProvider',
    28 => 'App\\Modules\\Orders\\Providers\\OrdersServiceProvider',
    29 => 'App\\Modules\\Coupons\\Providers\\CouponsServiceProvider',
    30 => 'App\\Modules\\Email\\Providers\\EmailServiceProvider',
    31 => 'App\\Modules\\Auth\\Providers\\AuthServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
    1 => 'Illuminate\\Database\\DatabaseServiceProvider',
    2 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
    3 => 'Illuminate\\Session\\SessionServiceProvider',
    4 => 'Illuminate\\Log\\LogServiceProvider',
    5 => 'Illuminate\\Routing\\RoutingServiceProvider',
    6 => 'Illuminate\\View\\ViewServiceProvider',
    7 => 'L5Swagger\\L5SwaggerServiceProvider',
    8 => 'Carbon\\Laravel\\ServiceProvider',
    9 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    10 => 'Termwind\\Laravel\\TermwindServiceProvider',
    11 => 'Spatie\\LaravelIgnition\\IgnitionServiceProvider',
    12 => 'L5Swagger\\L5SwaggerServiceProvider',
    13 => 'App\\Infrastructure\\Providers\\AppServiceProvider',
    14 => 'App\\Infrastructure\\Providers\\RouteServiceProvider',
    15 => 'App\\Modules\\Products\\Providers\\ProductsServiceProvider',
    16 => 'App\\Modules\\Cart\\Providers\\CartServiceProvider',
    17 => 'App\\Modules\\Orders\\Providers\\OrdersServiceProvider',
    18 => 'App\\Modules\\Coupons\\Providers\\CouponsServiceProvider',
    19 => 'App\\Modules\\Email\\Providers\\EmailServiceProvider',
    20 => 'App\\Modules\\Auth\\Providers\\AuthServiceProvider',
  ),
  'deferred' => 
  array (
    'migrator' => 'Illuminate\\Database\\MigrationServiceProvider',
    'migration.repository' => 'Illuminate\\Database\\MigrationServiceProvider',
    'migration.creator' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\MigrateCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\FreshCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\InstallCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\RefreshCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\ResetCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\RollbackCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\StatusCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'Illuminate\\Database\\Console\\Migrations\\MigrateMakeCommand' => 'Illuminate\\Database\\MigrationServiceProvider',
    'composer' => 'Illuminate\\Foundation\\Providers\\ComposerServiceProvider',
    'redis' => 'Illuminate\\Redis\\RedisServiceProvider',
    'redis.connection' => 'Illuminate\\Redis\\RedisServiceProvider',
    'cache' => 'Illuminate\\Cache\\CacheServiceProvider',
    'cache.store' => 'Illuminate\\Cache\\CacheServiceProvider',
    'cache.psr6' => 'Illuminate\\Cache\\CacheServiceProvider',
    'memcached.connector' => 'Illuminate\\Cache\\CacheServiceProvider',
    'Illuminate\\Cache\\RateLimiter' => 'Illuminate\\Cache\\CacheServiceProvider',
    'queue' => 'Illuminate\\Queue\\QueueServiceProvider',
    'queue.connection' => 'Illuminate\\Queue\\QueueServiceProvider',
    'queue.failer' => 'Illuminate\\Queue\\QueueServiceProvider',
    'queue.listener' => 'Illuminate\\Queue\\QueueServiceProvider',
    'queue.worker' => 'Illuminate\\Queue\\QueueServiceProvider',
    'mail.manager' => 'Illuminate\\Mail\\MailServiceProvider',
    'mailer' => 'Illuminate\\Mail\\MailServiceProvider',
    'Illuminate\\Mail\\Markdown' => 'Illuminate\\Mail\\MailServiceProvider',
    'translator' => 'Illuminate\\Translation\\TranslationServiceProvider',
    'translation.loader' => 'Illuminate\\Translation\\TranslationServiceProvider',
    'validator' => 'Illuminate\\Validation\\ValidationServiceProvider',
    'validation.presence' => 'Illuminate\\Validation\\ValidationServiceProvider',
    'Illuminate\\Contracts\\Validation\\UncompromisedVerifier' => 'Illuminate\\Validation\\ValidationServiceProvider',
    'hash' => 'Illuminate\\Hashing\\HashServiceProvider',
    'hash.driver' => 'Illuminate\\Hashing\\HashServiceProvider',
    'Laravel\\Sail\\Console\\InstallCommand' => 'Laravel\\Sail\\SailServiceProvider',
    'Laravel\\Sail\\Console\\PublishCommand' => 'Laravel\\Sail\\SailServiceProvider',
    'command.tinker' => 'Laravel\\Tinker\\TinkerServiceProvider',
  ),
  'when' => 
  array (
    'Illuminate\\Database\\MigrationServiceProvider' => 
    array (
    ),
    'Illuminate\\Foundation\\Providers\\ComposerServiceProvider' => 
    array (
    ),
    'Illuminate\\Redis\\RedisServiceProvider' => 
    array (
    ),
    'Illuminate\\Cache\\CacheServiceProvider' => 
    array (
    ),
    'Illuminate\\Queue\\QueueServiceProvider' => 
    array (
    ),
    'Illuminate\\Mail\\MailServiceProvider' => 
    array (
    ),
    'Illuminate\\Translation\\TranslationServiceProvider' => 
    array (
    ),
    'Illuminate\\Validation\\ValidationServiceProvider' => 
    array (
    ),
    'Illuminate\\Hashing\\HashServiceProvider' => 
    array (
    ),
    'Laravel\\Sail\\SailServiceProvider' => 
    array (
    ),
    'Laravel\\Tinker\\TinkerServiceProvider' => 
    array (
    ),
  ),
);