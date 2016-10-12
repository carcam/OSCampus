<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus;

use JDatabaseDriver;
use JUser;
use Mobile_Detect;

defined('_JEXEC') or die();

/**
 * Class Container
 *
 * @package OSCampus
 *
 * @property Certificate        $certificate
 * @property JDatabaseDriver $dbo
 * @property JUser              $user
 * @property Lesson             $lesson
 * @property Mobile_Detect      $device
 * @property UserActivity       $activity
 */
class Container extends \Pimple\Container
{
    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @param $args
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (strpos($name, 'get') === 0 && !$args) {
            $key = strtolower(substr($name, 3));
            if (isset($this[$key])) {
                return $this[$key];
            }
        }
        return null;
    }

    /**
     * Get instance of a class using parameter autodetect
     *
     * @param $className
     *
     * @return object
     */
    public function getInstance($className)
    {
        $class = new \ReflectionClass($className);
        if ($instance = $this->getServiceEntry($class)) {
            return $instance;
        }

        $dependencies = array();
        if (!is_null($class->getConstructor())) {
            $params = $class->getConstructor()->getParameters();
            foreach ($params as $param) {
                $dependentClass = $param->getClass();
                if ($dependentClass) {
                    $dependentClassName  = $dependentClass->name;
                    $dependentReflection = new \ReflectionClass($dependentClassName);
                    if ($dependentReflection->isInstantiable()) {
                        //use recursion to get dependencies
                        $dependencies[] = $this->getInstance($dependentClassName);
                    } elseif ($dependentReflection->isInterface()) {
                        // Interfaces need to be pre-registered in the container
                        if ($concrete = $this->getServiceEntry($dependentReflection, true)) {
                            $dependencies[] = $concrete;
                        }
                    }
                }
            }
        }

        $instance = $class->newInstanceArgs($dependencies);
        return $instance;
    }

    /**
     * Find a service in the container based on class name
     * Classes can be registered either through their short name
     * or full class name. Short name take precedence.
     *
     * @param \ReflectionClass $class
     * @param bool             $require
     *
     * @return object|null
     * @throws \Exception
     */
    protected function getServiceEntry(\ReflectionClass $class, $require = false)
    {
        $key = strtolower($class->getShortName());
        if (isset($this[$key])) {
            return $this[$key];
        }

        $name = $class->getName();
        if (isset($this[$name])) {
            return $this[$name];
        }

        if ($require) {
            throw new \Exception($class->getName() . ' -  is not registered in the container');
        }

        return null;
    }
}
