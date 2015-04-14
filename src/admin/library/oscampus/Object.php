<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use Oscampus\Exception;

defined('_JEXEC') or die();

class Object
{
    /**
     * Retrieve all public properties and their values
     * Although this duplicates get_object_vars(), it
     * is mostly useful for internal calls when we need
     * to filter out the non-public properties.
     *
     * @param bool $publicOnly
     *
     * @return array
     */
    public function getProperties($publicOnly = true)
    {
        $reflection = new \ReflectionObject($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        if (!$publicOnly) {
            $properties = array_merge(
                $properties,
                $reflection->getProperties(\ReflectionProperty::IS_PROTECTED)
            );
        }

        $data = array();
        foreach ($properties as $property) {
            $name        = $property->name;
            $data[$name] = $this->$name;
        }

        return $data;
    }

    /**
     * Set the public properties from the passed array/object
     *
     * @param array|object $data Values to copy to $this
     * @param array        $map  Use properties from $data translated using a field map
     *
     * @return $this
     * @throws Exception
     */
    public function setProperties($data, array $map = null)
    {
        $properties = $this->getProperties();
        if ($map !== null) {
            $data = $this->map($data, array_keys($properties), $map);
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (!is_array($data)) {
            throw new Exception('Invalid argument given - ' . gettype($data));
        }

        foreach ($data as $k => $v) {
            if (array_key_exists($k, $properties)) {
                $this->$k = $data[$k];
            }
        }

        return $this;
    }

    /**
     * Set all properties to null
     *
     * @param bool $publicOnly Pass false to include protected properties as well
     *
     * @return $this
     */
    public function clearProperties($publicOnly = true)
    {
        $properties = array_keys($this->getProperties($publicOnly));
        foreach ($properties as $property) {
            $this->$property = null;
        }

        return $this;
    }

    /**
     * Safely get a value from an object|array
     *
     * @param object|array $data
     * @param string       $var
     * @param mixed        $default
     *
     * @return mixed
     */
    public function getKeyValue($data, $var, $default = null)
    {
        if (is_object($data)) {
            return isset($data->$var) ? $data->$var : $default;
        }

        return isset($data[$var]) ? $data[$var] : $default;
    }

    /**
     *
     * Default string rendering for the object.
     * Subclasses should override as desired.
     *
     * @return string
     */
    public function asString()
    {
        return get_class($this);
    }

    /**
     * Expose properties with defined getters for direct use
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return null;
    }

    public function __toString()
    {
        return $this->asString();
    }
}
