<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewCourses extends OscampusViewList
{
    protected function setup()
    {
        parent::setup();

        $state = $this->getState();

        $enableOrdering = $state->get('list.ordering') == 'cp.ordering' && $state->get('filter.pathway') > 0;
        $this->setOrdering('cp.ordering', 'courses.', $enableOrdering);
    }
}
