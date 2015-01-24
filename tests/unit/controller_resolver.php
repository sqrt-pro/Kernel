<?php

require_once __DIR__ . '/../init.php';

use SQRT\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolverTest extends PHPUnit_Framework_TestCase
{
  /** @dataProvider dataGoodRoutes */
  function testGoodRoutes($url, $controller, $action, $result = null, $path_on_server = null)
  {
    $req = Request::create($url);

    $resolver = new ControllerResolver(CTRL_PATH, $path_on_server);
    $arr = $resolver->getController($req);

    $this->assertInstanceOf($controller, $arr[0], $url . ' неверный класс контроллера');
    $this->assertEquals($action, $arr[1], $url . ' неверный экшн');

    if (!is_null($result)) {
      $this->assertEquals($result, call_user_func($arr), $url . ' неверный результат выполнения');
    }
  }

  function dataGoodRoutes()
  {
    return array(
      array('/', 'defaultController', 'indexAction'),
      array('/test/', 'defaultController', 'testAction'),
      array('/test.html', 'defaultController', 'testAction'),
      array('/some/', 'someController', 'indexAction'),
      array('/some.html', 'someController', 'indexAction'),
      array('/some/test/', 'someController', 'testAction', 'some_test'),
      array('/some/magic/', 'someController', 'magicAction', 'some_magic'),
      array('/some/magic.html', 'someController', 'magicAction', 'some_magic'),
      array('/some/magic.json', 'someController', 'magicAction', 'some_magic'),
      array('/path/on/server/some/magic/', 'someController', 'magicAction', 'some_magic', '/path/on/server/'),
    );
  }

  /** @dataProvider dataBadRoutes */
  function testBadRoutes($url, $status = null)
  {
    $req = Request::create($url);

    $resolver = new ControllerResolver(CTRL_PATH);

    try {
      $resolver->getController($req);

      $this->fail($url . ' должен быть Exception');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
      if ($status) {
        $this->assertEquals($status, $e->getStatusCode(), 'Неверный статус');
      }
    }
  }

  function dataBadRoutes()
  {
    return array(
      array('/not_exists/', 404),
      array('/bad/', 404),
      array('/file.txt', 404),
    );
  }
}