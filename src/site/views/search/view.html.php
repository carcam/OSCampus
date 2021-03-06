<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusViewSearch extends OscampusViewSite
{
    /**
     * @var OscampusModelSearch
     */
    protected $model = null;

    /**
     * @var object[]
     */
    protected $items = null;

    /**
     * @var JPagination
     */
    protected $pagination = null;

    public function display($tpl = null)
    {
        /** @var OscampusModelSearch $model */
        $this->model = $this->getModel();

        $this->items      = $this->model->getItems();
        $this->pagination = $this->model->getPagination();

        parent::display($tpl);
    }
}
