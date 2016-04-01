<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JLoader::import('courses', __DIR__);

class OscampusModelPathway extends OscampusModelCourses
{
    protected function populateState($ordering = 'cp.ordering', $direction = 'ASC')
    {
        $app = OscampusFactory::getApplication();
        $pathwayId = $app->input->getInt('pid', 0);
        $this->setState('pathway.id', $pathwayId);

        parent::populateState($ordering, $direction);

        // Ignore pagination for now
        $this->setState('list.start', 0);
        $this->setState('list.limit');
    }
}
