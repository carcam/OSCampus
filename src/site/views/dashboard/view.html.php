<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewDashboard extends OscampusViewSite
{
    /**
     * @var OscampusModelDashboard
     */
    protected $model = null;

    /**
     * @var array
     */
    protected $courses = null;

    protected $certificates = null;

    public function display($tpl = null)
    {
        $this->model = $this->getModel();

        $this->courses      = $this->model->getCourses();
        $this->certificates = $this->model->getCertificates();

        parent::display($tpl);
    }
}
