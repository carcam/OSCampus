<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewPathways extends OscampusViewSite
{
    /**
     * @var object[]
     */
    protected $items = array();

    /**
     * @var JPagination
     */
    protected $pagination = null;

    /**
     * @var object
     */
    protected $item = null;

    public function display($tpl = null)
    {
        /** @var OscampusModelPathways $model */
        $model = $this->getModel();

        $this->items = $model->getItems();
        $this->pagination = $model->getPagination();

        parent::display($tpl);
    }
}
