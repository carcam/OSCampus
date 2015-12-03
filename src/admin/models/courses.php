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
            'id',              'course.id',
            'title',           'course.title',
            'tags',            'tag.title',
            'pathways',        'pathway.title',
            'difficulty',      'course.difficulty',
            'published',       'course.published',
            'viewlevel_title', 'viewlevel.title',
            'teacher_name',    'teacher_user.name'
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
                'teacher_user.name as teacher_name',
                'viewlevel.title as viewlevel_title',
                'GROUP_CONCAT(DISTINCT tag.title) AS tags',
                'GROUP_CONCAT(DISTINCT pathway.title) AS pathways',
                'editor_user.name editor'
            )
        )
            ->from('#__oscampus_courses course')
            ->leftJoin('#__oscampus_teachers teacher ON course.teachers_id = teacher.id')
            ->leftJoin('#__users teacher_user ON teacher.users_id = teacher_user.id')
            ->leftJoin('#__viewlevels viewlevel ON course.access = viewlevel.id')
            ->leftJoin('#__oscampus_courses_tags course_tags ON course.id = course_tags.courses_id')
            ->leftJoin('#__oscampus_tags tag ON course_tags.tags_id = tag.id')
            ->leftJoin('#__oscampus_courses_pathways cp ON cp.courses_id = course.id')
            ->leftJoin('#__oscampus_pathways pathway ON pathway.id = cp.pathways_id')
            ->leftJoin('#__users editor_user ON editor_user.id = course.checked_out');

        $this->whereTextSearch($query, 'course.id', 'course.title', 'course.alias');
        $query->group('course.id');

        $primary = $this->getState('list.ordering', 'course.title');
        $direction = $this->getState('list.direction', 'ASC');
        $query->order($primary . ' ' . $direction);
        if (!in_array($primary, array('course.title', 'course.id'))) {
            $query->order('course.title ' . $direction);
        }

        return $query;
    }

    protected function populateState($ordering = 'course.title', $direction = 'ASC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState($ordering, $direction);
    }
}
