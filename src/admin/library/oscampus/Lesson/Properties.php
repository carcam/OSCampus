<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson;

defined('_JEXEC') or die();

class Properties
{
    /**
     * @var int
     */
    public $id = null;

    /**
     * @var int
     */
    public $modules_id = null;

    /**
     * @var int
     */
    public $courses_id = null;

    /**
     * @var int
     */
    public $pathways_id = null;

    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string
     */
    public $title = null;

    /**
     * @var string
     */
    public $alias = null;

    /**
     * @var string
     */
    public $header = null;

    /**
     * @var mixed
     */
    public $content = null;

    /**
     * @var string
     */
    public $footer = null;

    /**
     * @var int
     */
    public $access = null;

    /**
     * @var bool
     */
    public $published = null;

    public function __construct($data = null)
    {
        if ($data) {
            $this->load($data);
        }
    }

    public function __clone()
    {
        $properties = array_keys(get_object_vars($this));
        foreach ($properties as $property) {
            $this->$property = null;
        }
    }

    public function load($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        if ($this->content && is_string($this->content)) {
            $this->content = json_decode($this->content);
        }
    }
}
