<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use DateTime;
use OscampusFactory;
use ReflectionClass;
use ReflectionProperty;

defined('_JEXEC') or die();

abstract class AbstractPrototype
{
    /**
     * @var ReflectionProperty[]
     */
    protected $defaultValues = null;

    /**
     * @var string[]
     */
    protected $dateProperties = array();

    public function __construct()
    {
        $presets = get_object_vars($this);
        $this->setProperties($presets);
    }

    public function __clone()
    {
        $this->setProperties(array(), true);
    }

    /**
     * Return all public properties as an array using transformations
     * for use in database queries
     *
     * @return array
     */
    public function toArray()
    {
        $properties = $this->getDefaults();
        foreach ($properties as $name => $default) {
            if ($this->$name instanceof DateTime) {
                $properties[$name] = $this->$name->format('Y-m-d H:i:s');
            } else {
                $properties[$name] = $this->$name;
            }
        }

        return $properties;
    }

    /**
     * Wrapper for toArray() to return an object
     *
     * @return object
     */
    public function toObject()
    {
        return (object)$this->toArray();
    }

    /**
     * Wrapper for toArray() to return a JSON string
     *
     * @return string
     */
    public function toString()
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Set public properties using an array. Optionally set all
     * other properties not referenced/
     *
     * @param array $data
     * @param bool  $setAll
     *
     * @return void
     */
    public function setProperties(array $data = array(), $setAll = false)
    {
        $properties = $this->getDefaults();

        foreach ($properties as $name => $default) {
            if (array_key_exists($name, $data)) {
                $this->setProperty($name, $data[$name]);

            } elseif ($setAll) {
                $this->$name = $default;
            }
        }
    }

    /**
     * Set a single property, applying any transformations that may be needed
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setProperty($name, $value)
    {
        if (in_array($name, $this->dateProperties) && property_exists($this, $name)) {
            if (is_string($value) && substr($value, 0, 10) != '0000-00-00') {
                $this->$name = OscampusFactory::getDate($value);

            } elseif (is_numeric($value)) {
                $this->$name = OscampusFactory::getDate();
                $this->$name->setTimestamp($value);

            } elseif ($value instanceof DateTime) {
                $this->$name = $value;

            } else {
                $this->$name = null;
            }
        } else {
            $this->$name = $value;
        }
    }

    /**
     * Find all public properties and their default values
     *
     * @return array
     */
    protected function getDefaults()
    {
        if ($this->defaultValues === null) {
            $this->defaultValues = array();

            $class         = new ReflectionClass($this);
            $properties    = $class->getProperties(ReflectionProperty::IS_PUBLIC);
            $defaultValues = $class->getDefaultProperties();

            foreach ($properties as $property) {
                $name = $property->name;

                $this->defaultValues[$name] = $defaultValues[$name];
            }
        }

        return $this->defaultValues;
    }
}
