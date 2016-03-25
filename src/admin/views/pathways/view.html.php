<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewPathways extends OscampusViewList
{
    protected function setup()
    {
        parent::setup();

        $state = $this->getState();

        $enableOrdering = $state->get('list.ordering') == 'pathway.ordering';
        $this->setOrdering('pathway.ordering', 'pathways', $enableOrdering);

        $published = JHtml::_(
            'osc.select.published',
            'filter_published',
            $state->get('filter.published'),
            'COM_OSCAMPUS_OPTION_SELECT_PUBLISHED'
        );

        $owners = JHtml::_(
            'osc.select.pathwayowner',
            'filter_owner',
            $state->get('filter.owner'),
            array(
                ''  => 'COM_OSCAMPUS_OPTION_SELECT_PATHWAY_OWNER',
                '0' => 'COM_OSCAMPUS_OPTION_CORE_PATHWAY'
            )
        );

        $access = JHtml::_(
            'osc.select.access',
            'filter_access',
            $state->get('filter.access'),
            'COM_OSCAMPUS_OPTION_SELECT_ACCESS'
        );

        $filters = array(
            'text'  => array(
                'value' => $this->getState()->get('filter.search')
            ),
            'items' => array(
                array($published, $owners, $access)
            )
        );

        $this->setVariable('filters', $filters);
    }
}
