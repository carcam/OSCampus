<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
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

    public function display($tpl = null)
    {
        /** @var OscampusModelPathways $model */
        $model = $this->getModel();

        $this->items      = $model->getItems();
        $this->pagination = $model->getPagination();

        parent::display($tpl);
    }
}
