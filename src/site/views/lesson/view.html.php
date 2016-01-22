<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Lesson;

defined('_JEXEC') or die();

class OscampusViewLesson extends OscampusViewSite
{
    /**
     * @var OscampusModelLesson
     */
    protected $model = null;

    /**
     * @var Lesson
     */
    protected $lesson = null;

    /**
     * @var Lesson\ActivityStatus
     */
    protected $activity = null;

    public function display($tmpl = null)
    {
        $this->model    = $this->getModel();
        $this->lesson   = $this->model->getItem();
        $this->activity = $this->model->getActivityStatus();

        if (!$this->lesson->isAuthorised()) {
            parent::display('noauth');
            return;
        }

        $pathway = JFactory::getApplication()->getPathway();

        $link = JHtml::_('osc.link.pathway', $this->lesson->pathways_id, null, null, true);
        $pathway->addItem($this->lesson->pathwayTitle, $link);

        $link = JHtml::_('osc.link.course', $this->lesson->pathways_id, $this->lesson->courses_id, null, null, true);
        $pathway->addItem($this->lesson->courseTitle, $link);

        $pathway->addItem($this->lesson->title);

        $this->setLayout($this->lesson->type);

        parent::display($tmpl);
    }

    /**
     * Get the default navigation controls
     *
     * @return string
     */
    protected function loadNavigation()
    {
        $oldLayout  = $this->setLayout('default');
        $navigation = $this->loadTemplate('navigation');
        $this->setLayout($oldLayout);

        return $navigation;
    }
}
