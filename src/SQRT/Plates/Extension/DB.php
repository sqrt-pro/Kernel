<?php

namespace SQRT\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use SQRT\DB\Manager;

class DB implements ExtensionInterface
{
  protected $manager;

  function __construct(Manager $manager)
  {
    $this->manager = $manager;
  }

  public function register(Engine $engine)
  {
    $engine->registerFunction('db', array($this, 'getManager'));
  }

  public function getManager()
  {
    return $this->manager;
  }
}