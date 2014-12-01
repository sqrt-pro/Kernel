<?php

namespace SQRT\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class URL implements ExtensionInterface
{
  /** @var \SQRT\URL */
  protected $url;

  function __construct(\SQRT\URL $url)
  {
    $this->url = $url;
  }

  public function register(Engine $engine)
  {
    $engine->registerFunction('url', array($this, 'getURL'));
  }

  public function getURL()
  {
    return $this->url;
  }
}