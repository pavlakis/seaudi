<?php

namespace pavlakis\seaudi\tests;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;

class DummyContainer implements ContainerInterface, \ArrayAccess
{
    protected $container = [];

    public function get($id)
    {
        return $this->container[$id];
    }

    public function has($id)
    {
        return array_key_exists($id, $this->container);
    }

    public function offsetExists($id)
    {
        return $this->has($id);
    }

    public function offsetGet($id)
    {
        return $this->container[$id];
    }

    public function offsetSet($id, $value)
    {
        $this->container[$id] = $value;
    }

    public function offsetUnset($id)
    {
        if ($this->has($id)) {
            unset($this->container[$id]);
        }
    }
}