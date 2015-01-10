<?php

require_once __DIR__ . '/../init.php';

use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends PHPUnit_Framework_TestCase
{
  function testPlatesEngineAndLayout()
  {
    $r = Request::create('/hello/');
    $c = new \SQRT\Controller($r);

    $this->assertInstanceOf('League\Plates\Engine', $c->getTemplatesEngine(), 'Движок создался');
    $this->assertInstanceOf('League\Plates\Template\Template', $c->template('ololo'), 'Шаблон создался');
    $this->assertInstanceOf('SQRT\Layout', $c->layout(), 'Layout создался');
  }

  function testUrlFromRequest()
  {
    $r = Request::create('/hello/привет:мир/');
    $c = new \SQRT\Controller($r);

    $this->assertEquals(
      'http://localhost/hello/%D0%BF%D1%80%D0%B8%D0%B2%D0%B5%D1%82:%D0%BC%D0%B8%D1%80/',
      $c->getUrl()->asString(true),
      'URL создался из Request'
    );

    $this->assertInstanceOf('SQRT\URLImmutable', $c->getUrl(), 'URL является объектом Immutable');

    $c = new \SQRT\Controller($r, new \SQRT\URL('/hello/'));
    $this->assertInstanceOf('SQRT\URLImmutable', $c->getUrl(), 'URL трансформируется в объект Immutable');
  }

  /**
   * @dataProvider dataExceptions
   */
  function testExceptions($method, $code)
  {
    $r = Request::create('/');
    $c = new \SQRT\Controller($r);

    try {
      call_user_func_array(array($c, $method), array());

      $this->fail($method . ' expected exception');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
      $this->assertEquals($code, $e->getStatusCode(), $method . ' HTTP STATUS не совпадает');
    }
  }

  function dataExceptions()
  {
    return array(
      array('forbidden', 403),
      array('notfound', 404)
    );
  }
}