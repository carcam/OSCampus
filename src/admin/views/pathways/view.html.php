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

        $filters = array(
            'text'  => array(
                'value' => $this->state->get('filter.search'),
                'description' => 'COM_OSCAMPUS_SEARCH_TEXT_DESC'
            )
        );

        $this->setVariable('filters', $filters);
    }
}
