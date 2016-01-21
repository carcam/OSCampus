<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson;

use DateTime;
use ReflectionClass;
use ReflectionProperty;

defined('_JEXEC') or die();

class ActivityStatus
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var int
     */
    public $users_id = null;

    /**
     * @var int
     */
    public $lessons_id = null;

    /**
     * @var DateTime
     */
    public $completed = null;

    /**
     * @var int
     */
    public $score = 0;

    /**
     * @var int
     */
    public $visits = 0;

    /**
     * @var mixed
     */
    public $data = null;

    /**
     * @var DateTime
     */
    public $first_visit = null;

    /**
     * @var DateTime
     */
    public $last_visit = null;

    /**
     * @var ReflectionProperty[]
     */
    protected $defaultValues = null;

    public function __construct(array $data = array())
    {
        $data = $data ?: get_object_vars($this);
        $this->setProperties($data);
    }

    public function __clone()
    {
        $this->setProperties(array(), true);
    }

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

    public function setProperty($name, $value)
    {
        switch ($name) {
            case 'completed':
            case 'last_visit':
            case 'first_visit':
                if (is_string($value)) {
                    $this->$name = new DateTime($value);

                } elseif (is_numeric($value)) {
                    $this->$name = new DateTime();
                    $this->$name->setTimestamp($value);

                } elseif ($value instanceof DateTime) {
                    $this->$name = $value;

                } else {
                    $this->$name = null;
                }
                break;

            default:
                $this->$name = $value;
        }
    }

    protected function getDefaults()
    {
        if ($this->defaultValues === null) {
            $this->defaultValues = array();

            $class         = new ReflectionClass($this);
            $properties    = $class->getProperties(ReflectionProperty::IS_PUBLIC);
            $defaultValues = $class->getDefaultProperties();

            foreach ($properties as $property) {
                $name                       = $property->name;
                $this->defaultValues[$name] = $defaultValues[$name];
            }
        }

        return $this->defaultValues;
    }
}
