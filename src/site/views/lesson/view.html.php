<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Activity\LessonStatus;
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
     * @var LessonStatus
     */
    protected $activity = null;

    public function display($tmpl = null)
    {
        $this->model    = $this->getModel();
        $this->lesson   = $this->model->getItem();
        $this->files    = $this->model->getFiles();
        $this->activity = $this->model->getLessonStatus();

        $pathway = JFactory::getApplication()->getPathway();

        $link = JHtml::_('osc.link.course', $this->lesson->courses_id, null, null, true);
        $pathway->addItem($this->lesson->courseTitle, $link);

        $pathway->addItem($this->lesson->title);

        $this->setLayout($this->lesson->type);

        $this->setMetadata(
            $this->lesson->metadata,
            $this->lesson->title . ' - ' . $this->lesson->courseTitle,
            $this->lesson->description
        );

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
