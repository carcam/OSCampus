<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
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
        $this->setOrdering('pathway.ordering', 'pathways.', $enableOrdering);

        $filters = array(
            'text' => array(
                'value' => $this->getState()->get('filter.search')
            )
        );

        $this->setVariable('filters', $filters);
    }
}
