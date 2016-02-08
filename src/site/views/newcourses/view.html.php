<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewNewcourses extends OscampusViewSite
{
    /**
     * @var array
     */
    protected $items = array();

    public function display($tpl = null)
    {
        /** @var OscampusModelNewcourses $model */
        $model = $this->getModel();

        try {
            $this->items = $model->getItems();

            parent::display($tpl);

        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}
