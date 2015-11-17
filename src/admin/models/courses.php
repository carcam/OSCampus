<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelCourses extends OscampusModelList
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'id', 'course.id',
            'title', 'course.title',
            'difficulty', 'course.difficulty',
            'published', 'course.published',
            'access_level', 'course.access',
            'teachers_name', 'user.name'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select(
            array(
                'course.*',
                'course.checked_out as editor',
                'user.name as teachers_name',
                'viewlevel.title as access_level',
                'GROUP_CONCAT(tag.title) AS tags',
                'editor_user.name editor'
            )
        );
        $query->from('#__oscampus_courses course');
        $query->leftJoin('#__oscampus_teachers teacher ON course.teachers_id = teacher.id');
        $query->leftJoin('#__users user ON teacher.users_id = user.id');
        $query->leftJoin('#__viewlevels viewlevel ON course.access = viewlevel.id');
        $query->leftJoin('#__oscampus_courses_tags course_tags ON course.id = course_tags.courses_id');
        $query->leftJoin('#__oscampus_tags tag ON course_tags.tags_id = tag.id');
        $query->leftJoin('#__users editor_user ON editor_user.id = course.checked_out');

        if ($search = $this->getState('filter.search')) {
            $search = $db->q('%' . $search . '%');
            $ors    = array(
                'course.title like ' . $search,
                'course.id like ' . $search
            );
            $query->where('(' . join(' OR ', $ors) . ')');
        }

        $listOrder = $this->getState('list.ordering', 'course.id');
        $listDir   = $this->getState('list.direction', 'ASC');

        $query->group('course.id');
        $query->order($listOrder . ' ' . $listDir);

        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState('course.id', 'ASC');
    }
}