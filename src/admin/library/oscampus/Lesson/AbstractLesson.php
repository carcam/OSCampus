<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson;

defined('_JEXEC') or die();

abstract class AbstractLesson
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

    /**
     * Prevent direct instantiation
     */
    protected function __construct()
    {

    }

    /**
     * @param array $data
     *
     * @return AbstractLesson
     * @throws \Oscampus\Exception
     */
    public static function getInstance(array $data)
    {
        $type      = ucfirst(strtolower(empty($data['type']) ? '' : $data['type']));
        $className = __NAMESPACE__ . '\\' . $type;
        if (!$type) {
            throw new \Oscampus\Exception(JText::_('COM_OSCAMPUS_ERROR_LESSON_NOTYPE'));

        } elseif (!class_exists($className)) {
            throw new \Oscampus\Exception(JText::sprintf('COM_OSCAMPUS_ERROR_LESSON_CLASS_NOTFOUND', $className));
        }

        $instance = new $className();
        $instance->loadData($data);

        return $instance;
    }

    /**
     * @param array $data
     */
    protected function loadData(array $data)
    {
        $properties = get_object_vars($this);
        foreach ($properties as $property => $value) {
            $this->$property = empty($data[$property]) ? null : $data[$property];
        }
    }
}
