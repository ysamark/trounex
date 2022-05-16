<?php
# Load composer
require __DIR__ . '/vendor/autoload.php';

use App\Database\Database;

## require_once (__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php');

#print_r (Database::GetConfig ());

$databaseConfig = Database::GetConfig ();

return [
  'paths' => [
    'migrations'    => './db/migrations',
    'seeds'         => './db/seeds',
  ],
  'templates' => [
    #'file' => 'phinx-template.php.dist'
  ],
  'migration_base_class' => 'App\Database\Migration',
  'seed_base_class' => 'App\Database\Seeder',
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_database' => 'default',
    'default_environment' => 'default',
    'default' => array_merge ($databaseConfig, [
      "adapter" => isset ($databaseConfig ['driver']) ? $databaseConfig ['driver'] : null,
      "user" => isset ($databaseConfig ['username']) ? $databaseConfig ['username'] : null,
      "pass" => isset ($databaseConfig ['password']) ? $databaseConfig ['password'] : null,
      "name" => isset ($databaseConfig ['database']) ? $databaseConfig ['database'] : null
    ])
  ]
];
