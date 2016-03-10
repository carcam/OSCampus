<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
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
     * @var object
     */
    protected $pathway = null;

    public function display($tpl = null)
    {
        $this->model = $this->getModel();

        $this->items   = $this->model->getItems();
        $this->pathway = $this->model->getPathway();

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
