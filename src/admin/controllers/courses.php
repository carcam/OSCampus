<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerCourses extends OscampusControllerAdmin
{
    protected $text_prefix = 'COM_OSCAMPUS_COURSES';

    public function getModel($name = 'Course', $prefix = 'OscampusModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
