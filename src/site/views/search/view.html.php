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
    protected $items = array();

    /**
     * @var object
     */
    protected $item = null;

    public function display($tpl = null)
    {
        $this->shareViewTemplates('pathway');

        /** @var OscampusModelSearch $model */
        $model = $this->getModel();

        $this->items = $model->getItems();

        parent::display($tpl);
    }
}
