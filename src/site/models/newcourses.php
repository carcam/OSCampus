<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

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

        $query = $this->getBaseQuery();

        $query->where('course.released >= ' . $db->quote($cutoff->toSql()));

        return $query;
    }

    /**
     * Ignore all common filters. DON'T call the parent
     */
    protected function setFilters()
    {
        $params = OscampusFactory::getApplication()->getParams();

        $releasePeriod = $params->get('releasePeriod', '1 month');
        $cutoff        = OscampusFactory::getDate('now - ' . $releasePeriod);
        $this->setState('filter.cutoff', $cutoff);
    }

    protected function populateState($ordering = 'course.released', $direction = 'DESC')
    {
        parent::populateState($ordering, $direction);
    }
}
