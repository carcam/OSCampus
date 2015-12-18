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
    /**
     * @var object
     */
    protected $course = null;

    /**
     * @var object
     */
    protected $teacher = null;

    /**
     * @var array
     */
    protected $lessons = array();

    /**
     * @var array
     */
    protected $files = array();

    /**
     * @var array
     */
    protected $viewed = array();

    public function display($tpl = null)
    {
        /** @var OscampusModelCourse $model */
        $model = $this->getModel();

        $this->course  = $model->getCourse();
        $this->teacher = $model->getTeacher();
        $this->lessons = $model->getLessons();
        $this->files   = $model->getFiles();
        $this->viewed  = $model->getViewedLessons();

        $pathway = JFactory::getApplication()->getPathway();

        $link = JHtml::_('osc.link.pathway', $this->course->pathways_id, null, null, true);
        $pathway->addItem($this->course->pathway_title, $link);

        $pathway->addItem($this->course->title);

        parent::display($tpl);
    }
}
