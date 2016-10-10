<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerLessons extends OscampusControllerAdmin
{
    protected $text_prefix = 'COM_OSCAMPUS_LESSONS';

    public function getModel($name = 'Lesson', $prefix = 'OscampusModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
