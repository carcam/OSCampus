<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewSearch extends OscampusViewSite
{
    /**
     * @var OscampusModelSearch
     */
    protected $model = null;

    /**
     * @var object[]
     */
    protected $pathways = array();

    /**
     * @var object[]
     */
    protected $courses = array();

    /**
     * @var object[]
     */
    protected $lessons = array();

    /**
     * @var object
     */
    protected $item = null;

    public function display($tpl = null)
    {
        /** @var OscampusModelSearch $model */
        $this->model = $this->getModel();

        $this->pathways = $this->model->getPathways();
        $this->courses  = $this->model->getCourses();
        $this->lessons = $this->model->getLessons();

        parent::display($tpl);
    }
}
