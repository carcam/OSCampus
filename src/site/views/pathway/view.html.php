<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

class OscampusViewPathway extends OscampusViewSite
{
    /**
     * @var OscampusModelPathway
     */
    protected $model = null;

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
    protected $pathway = null;

    public function display($tpl = null)
    {
        $this->model = $this->getModel();

        $this->items      = $this->model->getItems();
        $this->pathway    = $this->model->getPathway();
        $this->pagination = $this->model->getPagination();

        $pathway = JFactory::getApplication()->getPathway();
        $pathway->addItem($this->pathway->title);

        $this->setMetadata(
            $this->pathway->metadata,
            $this->pathway->title,
            $this->pathway->description
        );

        parent::display($tpl);
    }
}
