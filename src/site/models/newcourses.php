<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die();

JLoader::import('courses', __DIR__);

class OscampusModelNewcourses extends OscampusModelCourses
{
    protected function getListQuery()
    {
        /**
         * @var JDate $cutoff
         */

        $db     = $this->getDbo();
        $cutoff = $this->getState('filter.cutoff');

        $query = parent::getListQuery();

        $query->where('course.released >= ' . $db->quote($cutoff->toSql()));

        return $query;
    }

    protected function populateState($ordering = 'course.released', $direction = 'DESC')
    {
        $app = OscampusFactory::getApplication();

        if (method_exists($app, 'getParams')) {
            $params = $app->getParams();
        } else {
            $params = new Registry();
        }

        $releasePeriod = $params->get('releasePeriod', '1 month');
        $cutoff        = OscampusFactory::getDate('now - ' . $releasePeriod);
        $this->setState('filter.cutoff', $cutoff);

        parent::populateState($ordering, $direction);

        // Ignore pagination for now
        $this->setState('list.start', 0);
        $this->setState('list.limit');
    }
}
