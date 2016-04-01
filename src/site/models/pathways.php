<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JLoader::import('filtered', __DIR__);

class OscampusModelPathways extends OscampusModelSiteList
{
    protected function getListQuery()
    {
        $query = $this->getDbo()->getQuery(true)
            ->select('*')
            ->from('#__oscampus_pathways AS pathway')
            ->where(
                array(
                    'pathway.published = 1',
                    $this->whereAccess('pathway.access'),
                    'pathway.users_id = 0'
                )
            );

        if ($topic = (int)$this->getState('filter.topic')) {
            $subQuery = $this->getDbo()->getQuery(true)
                ->select('cp.pathways_id')
                ->from('#__oscampus_courses_tags AS ct')
                ->innerJoin('#__oscampus_courses AS course ON course.id = ct.courses_id')
                ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                ->where('ct.tags_id = ' . $topic)
                ->group('cp.pathways_id');

            $query->where(sprintf('pathway.id IN (%s)', $subQuery));
        }

        $ordering  = $this->getState('list.ordering');
        $direction = $this->getState('list.direction');
        $query->order($ordering . ' ' . $direction);
        if ($ordering != 'pathway.title') {
            $query->order('pathway.title ' . $direction);
        }

        return $query;
    }

    protected function populateState($ordering = 'pathway.ordering', $direction = 'ASC')
    {
        parent::populateState($ordering, $direction);

        $this->setState('list.start', 0);
        $this->setState('list.limit', 0);
    }
}
