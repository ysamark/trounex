<?php

namespace App\Database;

use Illuminate\Database\Capsule\Manager;
use Phinx\Migration\AbstractMigration;
use Doctrine\DBAL\Types\{StringType, Type};

abstract class Migration extends AbstractMigration {
  /** @var \Illuminate\Database\Capsule\Manager $capsule */
  public $capsule;
  /** @var \Illuminate\Database\Schema\Builder $capsule */
  public $schema;

  public static $CAPSULE;

  public function init () {

    if (self::$CAPSULE) {
      $this->capsule = self::$CAPSULE;
      $this->schema = $this->capsule->schema ();
    } else {
      $this->capsule = new Manager;
      $this->capsule->addConnection (Database::GetConfig ());

      $platform = $this->capsule
        ->getConnection ()
        ->getDoctrineSchemaManager ()
        ->getDatabasePlatform ();

      $platform->registerDoctrineTypeMapping ('enum', 'string');

      $this->capsule->bootEloquent ();
      $this->capsule->setAsGlobal ();
      $this->schema = $this->capsule->schema ();

      self::$CAPSULE = $this->capsule;
    }
  }

  public function createTable () {
    return call_user_func_array ([Manager::schema (), 'create'], func_get_args ());
  }

  public function useTable () {
    return call_user_func_array ([Manager::schema (), 'table'], func_get_args ());
  }
}
