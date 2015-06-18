<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewLesson extends OscampusViewSite
{
    /**
     * @var OscampusModelLesson
     */
    protected $model = null;

    /**
     * @var object
     */
    protected $lesson = null;

    /**
     * @var array
     */
    protected $files = array();

    public function display($tmpl = null)
    {
        $this->model  = $this->getModel();
        $this->lesson = $this->model->getLesson();
        $this->files  = $this->model->getFiles();

        $pathway = JFactory::getApplication()->getPathway();

        $cid = $this->lesson->courses_id;
        $pid = $this->lesson->pathways_id;

        $link = JHtml::_('osc.pathwaylink', $pid, null, null, true);
        $pathway->addItem($this->lesson->pathway_title, $link);

        $link = JHtml::_('osc.courselink', $pid, $cid, null, null, true);
        $pathway->addItem($this->lesson->course_title, $link);

        $pathway->addItem($this->lesson->title);
        parent::display($tmpl);
    }

    /**
     * Get the default navigation controls
     *
     * @return string
     */
    protected function loadNavigation()
    {
        $oldLayout = $this->setLayout('default');
        $navigation = $this->loadTemplate('navigation');
        $this->setLayout($oldLayout);

        return $navigation;
    }
}
