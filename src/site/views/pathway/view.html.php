<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewPathway extends OscampusViewSite
{
    /**
     * @var array
     */
    protected $items = array();

    public function display($tpl = null)
    {
        /** @var OscampusModelPathway $model */
        $model = $this->getModel();

        $this->items = $model->getItems();

        parent::display($tpl);
    }
}
