<?php

namespace SQRT\Codeception;

use SQRT\Kernel;
use SQRT\DB\Manager;
use Codeception\TestCase;
use Codeception\Configuration;
use Codeception\Lib\Framework;
use League\Container\Container;
use Symfony\Component\HttpKernel\Client;
use Codeception\Exception\ModuleConfigException;

class SQRT extends Framework
{
  /** @var Container */
  protected $container;

  public function _initialize()
  {
    if (!file_exists(Configuration::projectDir() . $this->config['app'])) {
      throw new ModuleConfigException(__CLASS__, "Bootstrap file {$this->config['app']} not found");
    }
  }

  public function _before(TestCase $test)
  {
    $this->loadApp();

    $this->getManager()->beginTransaction();

    $this->client = new Client(new Kernel($this->container));
  }

  public function _after(TestCase $test)
  {
    $this->getManager()->rollback();

    \Mockery::close();
  }

  /**
   * @return Manager
   */
  public function getManager()
  {
    return $this->getContainer()->get(Manager::class);
  }

  /**
   * @return Container
   */
  public function getContainer()
  {
    return $this->container;
  }

  /**
   * Grab an instance from container
   */
  public function grabService($service, $args)
  {
    return $this->getContainer()->get($service, $args);
  }

  /**
   * Mock service and add to container
   *
   * @return \Mockery\Mock
   */
  public function mockService($service, $singleton = true)
  {
    $mock = \Mockery::mock($service)->makePartial();
    $this->getContainer()->add($service, $mock, $singleton);

    return $mock;
  }

  protected function loadApp()
  {
    $this->container = require Configuration::projectDir() . $this->config['app'];
  }
}