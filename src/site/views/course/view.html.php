<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\File;
use Oscampus\Lesson;

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
     * @var Lesson[]
     */
    protected $lessons = array();

    /**
     * @var File[]
     */
    protected $files = array();

    /**
     * @var object[]
     */
    protected $viewed = array();

    public function display($tpl = null)
    {
        /** @var OscampusModelCourse $model */
        $model = $this->getModel();

        $this->course = $model->getCourse();
        if ($this->course) {
            $this->teacher = $model->getTeacher();
            $this->lessons = $model->getLessons();
            $this->files   = $model->getFiles();
            $this->viewed  = $model->getViewedLessons();
        }

        $pathway = JFactory::getApplication()->getPathway();

        $link = JHtml::_('osc.link.pathway', $this->course->pathways_id, null, null, true);
        $pathway->addItem($this->course->pathway_title, $link);

        $pathway->addItem($this->course->title);

        $doc = OscampusFactory::getDocument();
        $title = $this->course->metadata->get('title') ?: $this->course->title;

        $doc->setTitle($title);
        $doc->setMetaData('descripton', $this->course->metadata->get('description'));

        parent::display($tpl);
    }
}
