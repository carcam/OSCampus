<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
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
