<?php

namespace SQRT\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class User implements ExtensionInterface
{
  protected $user;

  function __construct($user = null)
  {
    $this->user = $user;
  }

  public function register(Engine $engine)
  {
    $engine->registerFunction('user', array($this, 'getUser'));
  }

  public function getUser()
  {
    return $this->user;
  }
}