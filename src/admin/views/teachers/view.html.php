<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewTeachers extends OscampusViewList
{
    protected function setup()
    {
        parent::setup();

        $filters = array(
            'text'  => array(
                'value' => $this->getState()->get('filter.search'),
                'description' => 'COM_OSCAMPUS_SEARCH_TEXT_TEACHERS_DESC'
            )
        );

        $this->setVariable('filters', $filters);
    }
}
