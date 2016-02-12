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
     * @var File[]
     */
    protected $files = array();

    /**
     * @var Lesson\ActivityStatus
     */
    protected $activity = null;

    public function display($tmpl = null)
    {
        $this->model    = $this->getModel();
        $this->lesson   = $this->model->getItem();
        $this->files    = $this->model->getFiles();
        $this->activity = $this->model->getActivityStatus();

        $pathway = JFactory::getApplication()->getPathway();

        $link = JHtml::_('osc.link.pathway', $this->lesson->pathways_id, null, null, true);
        $pathway->addItem($this->lesson->pathwayTitle, $link);

        $link = JHtml::_('osc.link.course', $this->lesson->pathways_id, $this->lesson->courses_id, null, null, true);
        $pathway->addItem($this->lesson->courseTitle, $link);

        $pathway->addItem($this->lesson->title);

        $this->setLayout($this->lesson->type);

        // Set title and meta description
        $doc      = OscampusFactory::getDocument();
        $metadata = $this->lesson->metadata;

        $title = $metadata->get('title') ?: $this->lesson->courseTitle . ' - ' . $this->lesson->title;
        $doc->setTitle($title);

        // @TODO: default to course data?
        if ($description = $metadata->get('description')) {
            $doc->setMetaData('description', $description);
        }

        parent::display($tmpl);

        if (!$this->lesson->isAuthorised()) {
            echo $this->loadDefaultTemplate('noauth');
        }
    }

    protected function loadDefaultTemplate($name)
    {
        $oldLayout = $this->setLayout('default');
        $template  = $this->loadTemplate($name);
        $this->setLayout($oldLayout);

        return $template;
    }
}
