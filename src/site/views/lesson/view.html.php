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
     * @var \Oscampus\Lesson
     */
    protected $lesson = null;

    /**
     * @var array
     */
    protected $files = array();

    public function display($tmpl = null)
    {
        error_reporting(-1);
        ini_set('display_errors', 1);

        /** @var OscampusModelLesson $model */
        $model = $this->getModel();

        $this->lesson = $model->getLesson();
        $this->files = $model->getFiles();

        parent::display($tmpl);

        error_reporting(0);
        ini_set('display_errors', 0);

    }
}
