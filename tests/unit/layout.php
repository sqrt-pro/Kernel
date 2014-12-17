<?php

require_once __DIR__ . '/../init.php';

use League\Plates\Engine;
use SQRT\Layout;

class LayoutTest extends PHPUnit_Framework_TestCase
{
  function testHeaderAndTitle()
  {
    $l = new Layout(new Engine());

    $l->setTitle('Hello');
    $this->assertEquals('<title>Hello</title>', $l->title()->toHTML(), 'Тайтл');
    $this->assertEquals('<h1>Hello</h1>', $l->header()->toHTML(), 'Заголовок дублирует тайтл');

    $l->setHeader('World');
    $l->setTitleTmpl('%s - Awesome site');

    $this->assertEquals('<title>Hello - Awesome site</title>', $l->title()->toHTML(), 'Тайтл генерится по шаблону');
    $this->assertEquals('<h1>World</h1>', $l->header()->toHTML(), 'Заголовок');
  }

  function testCSS()
  {
    $l = new Layout(new Engine());

    $l->addCSS('one.css');
    $l->addCSS('two.css', true, 'print', 'IE');

    $exp = array(
      'one.css' => array(
        'file'       => 'one.css',
        'media'      => null,
        'versioning' => null,
        'if'         => null,
      ),
      'two.css' => array(
        'file'       => 'two.css',
        'media'      => 'print',
        'versioning' => true,
        'if'         => 'IE',
      ),
    );
    $this->assertEquals($exp, $l->getCSS(), 'Массив CSS');

    $l->clearCSS();

    $this->assertFalse($l->getCSS(), 'Стили удалены');
  }

  function testJS()
  {
    $l = new Layout(new Engine());
    $l->addJS('jquery.js');
    $l->addJS('js.js', true, 'IE');

    $exp = array(
      'jquery.js' => array(
        'file'       => 'jquery.js',
        'versioning' => null,
        'if'         => null,
      ),
      'js.js'     => array(
        'file'       => 'js.js',
        'versioning' => true,
        'if'         => 'IE',
      ),
    );
    $this->assertEquals($exp, $l->getJS(), 'Массив JS');

    $l->clearJS();

    $this->assertFalse($l->getJS(), 'Стили удалены');
  }

  function testMeta()
  {
    $l = new Layout(new Engine());
    $l->setTitle('Hi');

    $this->assertEquals('<meta content="Hi" name="description" />', $l->description()->toHTML(), 'Description дублирует Title');
    $this->assertEquals('<meta content="Hi" name="keywords" />', $l->keywords()->toHTML(), 'Keywords дублирует Title');

    $l->setDefaultDescription('Hello');
    $l->setDefaultKeywords(false);

    $this->assertEquals('<meta content="Hello" name="description" />', $l->description()->toHTML(), 'Default Description явно задан');
    $this->assertEquals('<meta content="" name="keywords" />', $l->keywords()->toHTML(), 'Default Keywords отключен');

    $this->assertEquals('<meta content="Hi" name="description" />', $l->description('Hi')->toHTML(), 'Default Description передан в рендер');
    $this->assertEquals('<meta content="Hi" name="keywords" />', $l->keywords('Hi')->toHTML(), 'Default Keywords передан в рендер');

    $l->setKeywords('One');
    $l->setDescription('Two');

    $this->assertEquals('<meta content="One" name="keywords" />', $l->keywords()->toHTML(), 'Keywords явно задан');
    $this->assertEquals('<meta content="Two" name="description" />', $l->description()->toHTML(), 'Description явно задан');
  }
}