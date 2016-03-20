<?php

namespace SQRT;

use SQRT\Tag;
use League\Plates\Engine;

class Layout extends \SQRT\Helpers\Container
{
  /** @var Engine */
  protected $engine;
  protected $template;

  protected $default_keywords;
  protected $default_description;
  protected $title_tmpl = '%s';

  /** Список подключаемых JS */
  protected $js;
  /** Список подключаемых CSS */
  protected $css;

  function __construct(Engine $engine, $template = null)
  {
    $this->engine   = $engine;
    $this->template = $template;

    $this->init();
  }

  function __toString()
  {
    return $this->render();
  }

  /** Рендеринг страницы */
  public function render($template = null)
  {
    return $this->getEngine()->render($template ?: $this->getTemplate(), array('layout' => $this));
  }

  /**
   * Заголовок на странице
   * @return Tag
   */
  public function header($tag = 'h1', $attr = null)
  {
    return new Tag($tag, $this->getHeader() ?: $this->getTitle(), $attr);
  }

  /**
   * Генерация тега title
   * @return Tag
   */
  public function title()
  {
    return new Tag('title', sprintf($this->getTitleTmpl(), $this->getTitle()));
  }

  /**
   * Генерация тега keywords
   * @return Tag
   */
  public function keywords($default = null)
  {
    if (!$k = $this->getKeywords()) {
      $d = $default ?: $this->getDefaultKeywords();
      $k = is_null($d) ? $this->getTitle() : $d;
    }

    return new Tag('meta', null, array('name' => 'keywords', 'content' => $k), true);
  }

  /**
   * Генерация тега description
   * @return Tag
   */
  public function description($default = null)
  {
    if (!$k = $this->getDescription()) {
      $d = $default ?: $this->getDefaultDescription();
      $k = is_null($d) ? $this->getTitle() : $d;
    }

    return new Tag('meta', null, array('name' => 'description', 'content' => $k), true);
  }

  /** Название страницы в теге title */
  public function getTitle()
  {
    return $this->get('title');
  }

  /**
   * Название страницы в теге title
   * @return static
   */
  public function setTitle($title)
  {
    return $this->set('title', $title);
  }

  /** Главный заголовок на странице */
  public function getHeader()
  {
    return $this->get('header');
  }

  /**
   * Главный заголовок на странице
   * @return static
   */
  public function setHeader($header)
  {
    return $this->set('header', $header);
  }

  /**
   * META Keywords
   * @return static
   */
  public function setKeywords($keywords)
  {
    return $this->set('keywords', $keywords);
  }

  /** META Keywords */
  public function getKeywords()
  {
    return $this->get('keywords');
  }

  /**
   * META Description
   * @return static
   */
  public function setDescription($description)
  {
    return $this->set('description', $description);
  }

  /** META Description */
  public function getDescription()
  {
    return $this->get('description');
  }

  /** Содержимое BODY */
  public function getContent()
  {
    return $this->get('content');
  }

  /**
   * Содержимое BODY
   * @return static
   */
  public function setContent($content)
  {
    return $this->set('content', $content);
  }

  /** @return Engine */
  public function getEngine()
  {
    return $this->engine;
  }

  /** Движок Plates */
  public function setEngine(Engine $engine)
  {
    $this->engine = $engine;

    return $this;
  }

  /** Шаблон страницы */
  public function getTemplate()
  {
    return $this->template;
  }

  /**
   * Шаблон страницы
   * @return static
   */
  public function setTemplate($template)
  {
    $this->template = $template;

    return $this;
  }

  /** META Description по-умолчанию */
  public function getDefaultDescription()
  {
    return $this->default_description;
  }

  /** META Description по-умолчанию */
  public function setDefaultDescription($default_description)
  {
    $this->default_description = $default_description;

    return $this;
  }

  /** META Keywords по-умолчанию */
  public function setDefaultKeywords($default_keywords)
  {
    $this->default_keywords = $default_keywords;

    return $this;
  }

  /** META Keywords по-умолчанию */
  public function getDefaultKeywords()
  {
    return $this->default_keywords;
  }

  /** Sprintf шаблон для title */
  public function getTitleTmpl()
  {
    return $this->title_tmpl;
  }

  /**
   * Sprintf шаблон для title
   * @return static
   */
  public function setTitleTmpl($title_tmpl)
  {
    $this->title_tmpl = $title_tmpl;

    return $this;
  }

  /**
   * Добавление CSS.
   * $media - аттрибут media, по-умолчанию = all
   * $versioning - версионирование вида style.123.css
   * $if - для формирования условной конструкции <!--[if $if]><link ... /><![endif]-->
   */
  public function addCSS($file, $versioning = null, $media = null, $if = null)
  {
    $this->css[$file] = array(
      'file'       => $file,
      'media'      => $media,
      'versioning' => $versioning,
      'if'         => $if,
    );

    return $this;
  }

  /** Список подключаемых CSS в массиве с ключами file, media, versioning, if */
  public function getCSS()
  {
    return !empty($this->css) ? $this->css : false;
  }

  /**
   * Добавление скрипта
   * $versioning - версионирование вида script.123.js
   * $if - для формирования условной конструкции <!--[if $if]><script ... /><![endif]-->
   */
  public function addJS($file, $versioning = null, $if = null)
  {
    $this->js[$file] = array(
      'file'       => $file,
      'versioning' => $versioning,
      'if'         => $if
    );

    return $this;
  }

  /** Список подключаемых скриптов в массиве с ключами file, versioning, if */
  public function getJS()
  {
    return !empty($this->js) ? $this->js : false;
  }

  /** Удаление добавленных скриптов */
  public function clearJS()
  {
    $this->js = array();

    return $this;
  }

  /** Удаление добавленных стилей */
  public function clearCSS()
  {
    $this->css = array();

    return $this;
  }

  /** Дополнительная инициализация при наследовании */
  protected function init()
  {

  }
}