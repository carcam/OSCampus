<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusViewCourses extends OscampusViewAdminList
{
    protected function setup()
    {
        parent::setup();

        $state = $this->getState();

        $enableOrdering = $state->get('list.ordering') == 'cp.ordering' && $state->get('filter.pathway') > 0;
        $this->setOrdering('cp.ordering', 'courses', $enableOrdering);
    }
}
