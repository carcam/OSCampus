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

    /**
     * @var string[]
     */
    protected $filters = null;

    public function display($tpl = null)
    {
        $this->model = $this->getModel();

        $this->items   = $this->model->getItems();
        $this->pathway = $this->model->getPathway();
        $this->filters = $this->getFilters();

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

    /**
     * Returns array of form fields for use in filtering classes
     *
     * @return string[]
     */
    protected function getFilters()
    {
        $db  = OscampusFactory::getDbo();

        $filters = array();

        // Create Pathway/topic selector
        $pathwayQuery = $db->getQuery(true)
            ->select(
                array(
                    'id AS ' . $db->quote('value'),
                    'title AS ' . $db->quote('text')
                )
            )
            ->from('#__oscampus_pathways')
            ->where(
                array(
                    'users_id = 0',
                    'published = 1'
                )
            )
            ->order('ordering ASC');

        $pathways = $db->setQuery($pathwayQuery)->loadObjectlist();

        $filters[] = JHtml::_(
            'select.genericlist',
            $pathways,
            'pid',
            array('list.select' => $this->pathway->id)
        );

        // Create difficulty selector
        $difficulty = JHtml::_('osc.options.difficulties');
        array_unshift(
            $difficulty,
            JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_DIFFICULTY'))
        );
        $filters[] = JHtml::_(
            'select.genericlist',
            $difficulty,
            'difficulty',
            array(
                'list.select' => $this->model->getState('filter.difficulty')
            )
        );

        // Add discrete JS
        JHtml::_('osc.jquery');
        $js = <<<JSCRIPT
jQuery(document).ready(function() {
    jQuery('#formFilter select').on('change', function(evt) {
        this.form.submit();
    });
});
JSCRIPT;

        OscampusFactory::getDocument()->addScriptDeclaration($js);

        return $filters;
    }
}
