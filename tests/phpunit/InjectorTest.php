<?php

namespace pavlakis\seaudi\tests;

use Interop\Container\ContainerInterface;
use pavlakis\seaudi\Injector;
use pavlakis\seaudi\tests\DummyContainer;

class InjectorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setUp()
    {
        $this->container = new DummyContainer();

        $this->injector = new Injector($this->container);
    }

    /**
     * @expectedException pavlakis\seaudi\Exception\ClassNotFoundException
     */
    public function testClassNotFoundThrowsException()
    {
        $this->injector->get('class/does/not/exist');
    }

    /**
     * @expectedException pavlakis\seaudi\Exception\DiKeyNotFoundException
     */
    public function testKeyNotFoundThrowsException()
    {
        $this->injector->get('pavlakis\seaudi\Injector');
    }

    /**
     * @expectedException pavlakis\seaudi\Exception\TypeHintNotFoundException
     */
    public function testTypeHintNotFoundThrowsException()
    {
        $this->injector->get('pavlakis\seaudi\tests\DummyNoTypeHintClass');
    }

    public function testDependencyExistsCanGetInstance()
    {
        $this->container['Interop\Container\ContainerInterface'] = new DummyContainer();

        $this->assertInstanceOf('pavlakis\seaudi\Injector', $this->injector->get('pavlakis\seaudi\Injector'));
    }
}