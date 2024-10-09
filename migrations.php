<?php

return [
    'table_storage' => [
        'table_name' => 'doctrine_migration_versions',
        'version_column_name' => 'version',
        'version_column_length' => 191,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],

    'migrations_paths' => [
        'Migrations' => './database/migrations',
    ],

    'all_or_nothing' => true,
    'check_database_platform' => true,

    'db' => [
        'dbname' => getenv('MYSQL_TEST_DATABASE'),
        'user' => getenv('MYSQL_TEST_USER'),
        'password' => getenv('MYSQL_TEST_PASSWORD'),
        'host' => 'db',
        'driver' => 'pdo_mysql',
    ],
];
