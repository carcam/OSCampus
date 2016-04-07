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
     * @var object[]
     */
    protected $courses = array();

    /**
     * @var object[]
     */
    protected $pathways = array();

    /**
     * @var object
     */
    protected $item = null;

    public function display($tpl = null)
    {
        /** @var OscampusModelSearch $model */
        $model = $this->getModel();

        $this->courses  = $model->getCourses();
        $this->pathways = $model->getPathways();

        parent::display($tpl);
    }
}
