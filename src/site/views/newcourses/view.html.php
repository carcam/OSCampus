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
     * @var object[]
     */
    protected $items = array();

    /**
     * @var object
     */
    protected $item = null;

    /**
     * @var DateTime
     */
    protected $cutoff = null;

    public function display($tpl = null)
    {
        $this->shareViewTemplates('pathway');

        /** @var OscampusModelNewcourses $model */
        $model = $this->getModel();

        $this->items = $model->getItems();

        parent::display($tpl);
    }
}
