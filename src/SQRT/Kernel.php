<?php

namespace SQRT;

use League\Container\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Kernel implements HttpKernelInterface
{
  /** @var Container */
  protected $container;

  function __construct(Container $container)
  {
    $this->container = $container;
  }

  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
  {
    $this->prepareRequest($request);

    try {
      $router = $this->container->get(RouteCollection::class);

      return $router->getDispatcher()->dispatch($request->getMethod(), $request->getPathInfo());
    } catch (\Exception $e) {
      return $this->handleException($e);
    }
  }

  protected function prepareRequest(Request $request)
  {
    $session = $this->container->get(Session::class);
    $request->setSession($session);

    if ($request->getContentType() == 'json') {
      if ($data = json_decode($request->getContent(), true)) {
        $request->request->replace($data);
      }
    }

    $this->container->add(Request::class, $request);
  }

  protected function handleException(\Exception $e)
  {
    $headers = [];
    $code    = Response::HTTP_INTERNAL_SERVER_ERROR;
    $title   = $e->getMessage();

    if ($e instanceof \League\Route\Http\Exception) {
      $code    = $e->getStatusCode();
      $headers = $e->getHeaders();
      $title   = $e->getMessage();
    }

    return Response::create('<h3>' . $title . '</h3>', $code, $headers);
  }
}