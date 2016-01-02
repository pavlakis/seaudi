<?php
/**
 * A semi-automatic DI resolver retrieving dependencies through a container.
 *
 * @link      https://github.com/pavlakis/seaudi
 * @copyright Copyright © 2015 Antonis Pavlakis
 * @license   https://github.com/pavlakis/seaudi/blob/master/LICENSE (BSD 3-Clause License)
 */

namespace pavlakis\seaudi;

use Interop\Container\ContainerInterface;
use pavlakis\seaudi\Exception\ClassNotFoundException;
use pavlakis\seaudi\Exception\DiKeyNotFoundException;
use pavlakis\seaudi\Exception\TypeHintNotFoundException;

class Injector
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $className
     */
    public function add($className)
    {
        $this->container[$className] = function() use ($className) { return $this->get($className); };
    }

    /**
     * @param $className
     * @return object
     * @throws ClassNotFoundException
     * @throws \Exception
     */
    public function get($className)
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException(sprintf('Class "%s" not found.', $className));
        }

        try {

            $params = $this->getClassParams($className);
            if (empty($params)) {
                return (new \ReflectionClass($className))->newInstance();
            }

            return (new \ReflectionClass($className))->newInstanceArgs($params);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $className
     * @return array
     * @throws DiKeyNotFoundException
     * @throws TypeHintNotFoundException
     */
    protected function getClassParams($className)
    {
        $reflection = new \ReflectionMethod($className, '__construct');
        $parameters = $reflection->getParameters();
        $params = [];
        foreach ($parameters as $parameter) {

            if (is_null($parameter->getClass())) {
                if ($parameter->isOptional()) {
                    continue;
                } else {
                    throw new TypeHintNotFoundException(
                        sprintf(
                            'Could not find a type hint/declaration for the "%s" parameter.',
                            $parameter->getName()
                        )
                    );
                }
            }

            if (!$this->container->has($parameter->getClass()->name)) {
                throw new DiKeyNotFoundException(
                    sprintf(
                        'Could not find the "%s" class in the container',
                        $parameter->getClass()->name
                    )
                );
            }

            $params[] = $this->container[$parameter->getClass()->name];
        }

        return $params;
    }
}