<?php

namespace SQRT\Codeception;

use SQRT\Kernel;
use SQRT\DB\Manager;
use Codeception\TestCase;
use Codeception\Lib\Framework;
use League\Container\Container;
use Symfony\Component\HttpKernel\Client;
use Codeception\Exception\ModuleConfigException;

class SQRT extends Framework
{
  /** @var Container */
  protected $container;

  protected $kernel_class;

  protected $requiredFields = ['container'];

  public function _initialize()
  {
    $this->kernel_class = !empty($this->config['kernel']) ? $this->config['kernel'] : Kernel::class;
    if (!class_exists($this->kernel_class)) {
      throw new ModuleConfigException(__CLASS__, "Kernel class {$this->kernel_class} not exists");
    }

    if (!class_exists($this->config['container'])) {
      throw new ModuleConfigException(__CLASS__, "Container class {$this->config['container']} not exists");
    }
  }

  public function _before(TestCase $test)
  {
    $class = $this->config['container'];
    $this->container = new $class;

    $this->getManager()->beginTransaction();

    $class = $this->kernel_class;
    $this->client = new Client(new $class($this->container));
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
}