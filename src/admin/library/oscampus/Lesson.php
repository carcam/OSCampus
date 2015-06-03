<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

defined('_JEXEC') or die();

class Lesson
{
    public $id         = null;
    public $modules_id = null;
    public $title      = null;
    public $alias      = null;
    public $type       = null;
    public $header     = null;
    public $content    = null;
    public $footer     = null;
    public $published  = null;
    public $ordering   = null;

    public function __construct($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        $properties = get_object_vars($this);

        foreach ($properties as $property => $value) {
            $this->$property = empty($data[$property]) ? null : $data[$property];
        }

        switch ($this->type) {
            case 'oswistia':
            case 'quiz':
                $this->content = json_decode($this->content);
                break;
        }
    }
}
