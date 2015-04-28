<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewCourse extends OscampusViewSite
{
    protected $course     = null;
    protected $teacher = null;
    protected $lessons    = array();

    public function display($tpl = null)
    {
        /** @var OscampusModelCourse $model */
        $model = $this->getModel();

        $this->course     = $model->getCourse();
        $this->teacher = $model->getTeacher();
        $this->lessons    = $model->getLessons();

        parent::display($tpl);
    }
}
