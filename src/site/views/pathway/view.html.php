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
     * @var array
     */
    protected $items = array();

    /**
     * @var object
     */
    protected $pathway = null;

    public function display($tpl = null)
    {
        /** @var OscampusModelPathway $model */
        $model = $this->getModel();

        $this->items   = $model->getItems();
        $this->pathway = $model->getPathway();

        $pathway = JFactory::getApplication()->getPathway();
        $pathway->addItem($this->pathway->title);

        $this->setMetadata(
            $this->pathway->metadata,
            $this->pathway->title,
            $this->pathway->description
        );

        parent::display($tpl);
    }

    /**
     * Draw a dynamic start button based on user's progress
     *
     * @param object $item
     *
     * @return string
     */
    protected function getStartButton($item)
    {
        if ($item->progress == 0) {
            $icon = 'fa-play';
            $text = JText::_('COM_OSCAMPUS_START_THIS_CLASS');
        } elseif ($item->progress == 100) {
            $icon = 'fa-play';
            $text = JText::_('COM_OSCAMPUS_WATCH_THIS_CLASS_AGAIN');
        } else {
            $icon = 'fa-play';
            $text = JText::_('COM_OSCAMPUS_CONTINUE_THIS_CLASS');
        }

        $button = sprintf('<i class="fa %s"></i> %s', $icon, $text);

        return JHtml::_('osc.link.lesson', $item->id, 0, $button, 'class="osc-btn"');
    }
}
