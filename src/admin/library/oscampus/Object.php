<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

defined('_JEXEC') or die();

/**
 * Class Object
 *
 * @package Oscampus
 */
abstract class Object
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
     *
     * @return $this
     * @throws \Exception
     */
    public function setProperties($data)
    {
        $properties = $this->getProperties();
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (!is_array($data)) {
            throw new \Exception('Invalid argument given - ' . gettype($data));
        }

        foreach ($data as $k => $v) {
            if (array_key_exists($k, $properties)) {
                $this->$k = $data[$k];
            }
        }

        return $this;
    }

    /**
     * Clear all the public properties
     *
     * @return void
     * @throws \Exception
     */
    public function reset()
    {
        $properties = $this->getProperties();
        $this->setProperties(array_fill_keys($properties, null));
    }
}
