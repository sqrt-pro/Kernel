<?php

namespace SQRT;

use Stringy\StaticStringy;
use League\Plates\Template\Template;
use League\Route\Strategy\AbstractStrategy;
use League\Route\Strategy\StrategyInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class RouteStrategy extends AbstractStrategy implements StrategyInterface
{
  /**
   * {@inheritdoc}
   */
  public function dispatch($controller, array $vars)
  {
    if (is_string($controller) && strpos($controller, '::')) {
      $controller = explode('::', $controller);
    }

    if (is_array($controller)) {
      $controller = [
        $this->container->get('Controller\\' . $controller[0]),
        StaticStringy::camelize($controller[1] . ' action')
      ];
    }

    $response = $this->container->call($controller, $vars);

    return $this->determineResponse($response);
  }

  protected function determineResponse($response)
  {
    if ($response instanceof Response) {
      return $response;
    }

    if ($response instanceof Layout || $response instanceof Template) {
      $response = $response->render();
    }

    if (is_array($response)) {
      return JsonResponse::create($response);
    }

    try {
      $response = Response::create($response);
    } catch (\Exception $e) {
      throw new \RuntimeException('Unable to build Response from controller return value', 0, $e);
    }

    return $response;
  }
}
