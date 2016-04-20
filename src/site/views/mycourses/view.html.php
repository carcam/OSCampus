<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

class OscampusViewMycourses extends OscampusViewSite
{
    /**
     * @var CourseStatus[]
     */
    protected $items = null;

    public function display($tpl = null)
    {
        /** @var OscampusModelMycourses $model */
        $model = $this->getModel();

        $this->items = $model->getItems();

        parent::display($tpl);
    }
}
