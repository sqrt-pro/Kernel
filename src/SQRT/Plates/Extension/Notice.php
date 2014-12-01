<?php

namespace SQRT\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class Notice implements ExtensionInterface
{
  /** @var FlashBagInterface */
  protected $bag;

  /** @var Engine */
  protected $engine;

  function __construct(FlashBagInterface $bag)
  {
    $this->bag = $bag;
  }

  public function register(Engine $engine)
  {
    $this->engine = $engine;

    $engine->registerFunction('notice', array($this, 'renderNotice'));
    $engine->registerFunction('getNotices', array($this, 'getNotices'));
  }

  public function getNotices()
  {
    return $this->bag->all();
  }

  public function renderNotice($template = 'notice')
  {
    return $this->engine->render($template, array('notices' => $this->getNotices()));
  }
}