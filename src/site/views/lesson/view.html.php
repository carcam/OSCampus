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

        parent::display($tmpl);
    }
}
