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

    public function display($tmpl = null)
    {
        $this->model  = $this->getModel();
        $this->lesson = $this->model->getItem();

        if (!$this->lesson->isAuthorised()) {
            echo '<p>' . $this->lesson->title . ' is not accessible. We haven\'t determined what to do here yet</p>';
            return;
        }

        $pathway = JFactory::getApplication()->getPathway();

        $cid = $this->lesson->courses_id;
        $pid = $this->lesson->pathways_id;

        $link = JHtml::_('osc.link.pathway', $pid, null, null, true);
        $pathway->addItem($this->lesson->pathway_title, $link);

        $link = JHtml::_('osc.link.course', $pid, $cid, null, null, true);
        $pathway->addItem($this->lesson->course_title, $link);

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
        $oldLayout = $this->setLayout('default');
        $navigation = $this->loadTemplate('navigation');
        $this->setLayout($oldLayout);

        return $navigation;
    }
}
