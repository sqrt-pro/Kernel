<?php

namespace SQRT;

use League\Plates\Engine;
use League\Container\Container;
use League\Plates\Template\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller
{
  /** @var Container */
  protected $container;

  function __construct(Container $container)
  {
    $this->container = $container;
  }

  /** @return Layout */
  public function layout($template = null)
  {
    return new Layout($this->getTemplatesEngine(), $template);
  }

  /** @return Request */
  public function getRequest()
  {
    return $this->container->get(Request::class);
  }

  /** @return URLImmutable */
  public function getUrl()
  {
    return $this->container->get(URL::class);
  }

  /** @return Session */
  public function getSession($autostart = true)
  {
    $req = $this->getRequest();

    if (!$req->hasSession() && $autostart) {
      $req->setSession($this->container->get(Session::class));
    }

    return $req->getSession();
  }

  /** Добавить всплывающее сообщение */
  public function notice($message, $type = null)
  {
    $type = is_null($type)
      ? 'info'
      : (is_bool($type)
        ? ($type
          ? 'success'
          : 'error')
        : $type);

    $this->getSession()->getFlashBag()->add($type, $message);

    return $this;
  }

  /** Получить все всплывающие сообщения */
  public function getNotices()
  {
    return $this->getSession()->getFlashBag()->all();
  }

  /**
   * Движок шаблонизатора Plates
   *
   * @return Engine
   */
  public function getTemplatesEngine()
  {
    $this->container->get(Engine::class);
  }

  /**
   * Создать шаблон Plates
   *
   * @return Template
   */
  public function template($name, $data = null)
  {
    $t = $this->getTemplatesEngine()->make($name);

    if ($data) {
      $t->data($data);
    }

    return $t;
  }

  /**
   * Рендеринг шаблона Plates
   */
  public function render($name, $data = null)
  {
    $t = $this->template($name, $data);

    return $t->render();
  }

  /** Проверка сделан ли запрос через Ajax */
  public function isAjax()
  {
    return $this->getRequest()->isXmlHttpRequest();
  }

  /**
   * Выброс исключения: страница не найдена
   *
   * @throws HttpException
   */
  public function notFound($msg = null)
  {
    throw new HttpException(404, $msg);
  }

  /**
   * Выброс исключения: доступ запрещен
   *
   * @throws HttpException
   */
  public function forbidden($msg = null)
  {
    throw new HttpException(403, $msg);
  }

  /** Response с редиректом */
  public function redirect($url, $status = null)
  {
    return new RedirectResponse($url, $status ?: 302);
  }

  /** Response с редиректом по HTTP REFERER`у или, при его отсутствии, на главную */
  public function back()
  {
    return $this->redirect($this->getRequest()->server->get('HTTP_REFERER', '/'));
  }
}