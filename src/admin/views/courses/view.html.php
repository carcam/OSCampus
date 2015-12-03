<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewCourses extends OscampusViewList
{
    protected function setup()
    {
        parent::setup();

        $filters = array(
            'text' => array(
                'value' => $this->state->get('filter.search')
            )
        );

        $this->setVariable('filters', $filters);
    }

}
