<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewCourse extends OscampusViewForm
{
    /**
     * @var OscampusModelCourse
     */
    protected $model = null;

    protected function setup()
    {
        parent::setup();

        $this->setVariable('lessons', $this->model->getLessons());
    }
}
