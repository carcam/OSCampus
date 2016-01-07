<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
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
            'teacher_name',    'teacher_user.name',
            'cp.ordering',     'ordering'
        );

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $pathwaysQuery = $db->getQuery(true)
            ->select('GROUP_CONCAT(p2.title ORDER BY p2.title)')
            ->from('#__oscampus_pathways AS p2')
            ->innerJoin('#__oscampus_courses_pathways AS cp2 ON cp2.pathways_id = p2.id')
            ->where('cp2.courses_id = course.id');

        $query = $db->getQuery(true);
        $query->select(
            array(
                'course.*',
                'teacher_user.name AS teacher_name',
                'viewlevel.title AS viewlevel_title',
                'GROUP_CONCAT(DISTINCT tag.title ORDER BY tag.title) AS tags',
                "({$pathwaysQuery}) AS pathways",
                'editor_user.name AS editor'
            )
        )
            ->from('#__oscampus_courses course')
            ->leftJoin('#__oscampus_teachers AS teacher ON course.teachers_id = teacher.id')
            ->leftJoin('#__users AS teacher_user ON teacher.users_id = teacher_user.id')
            ->leftJoin('#__viewlevels AS viewlevel ON course.access = viewlevel.id')
            ->leftJoin('#__oscampus_courses_tags AS course_tags ON course.id = course_tags.courses_id')
            ->leftJoin('#__oscampus_tags AS tag ON course_tags.tags_id = tag.id')
            ->leftJoin('#__users AS editor_user ON editor_user.id = course.checked_out');

        $this->whereTextSearch($query, 'course.id', 'course.title', 'course.alias');

        $published = $this->getState('filter.published');
        if ($published != '') {
            $query->where('course.published = ' . (int)$published);
        }

        // Pathways are m:m and the source of ordering, making filtering a bit interesting
        if ($pathway = (int)$this->getState('filter.pathway')) {
            $queryPathway = $db->getQuery(true)
                ->select('courses_id')
                ->from('#__oscampus_courses_pathways')
                ->where('pathways_id = ' . $pathway);

            $query->leftJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id');
            $query->where("course.id IN ({$queryPathway})");
        }
        $ordering = $pathway ? 'cp.ordering' : $db->q('---');
        $query->select($ordering . ' AS ordering');

        $tag = $this->getState('filter.tag');
        if (is_numeric($tag) != '') {
            $queryTag = $db->getQuery(true)
                ->select('courses_id')
                ->from('#__oscampus_courses_tags')
                ->where('tags_id  = ' . (int)$tag);

            $query->where("course.id IN ({$queryTag})");

        } elseif ($tag == 'null') {
            $query->where('tag.id IS NULL');
        }

        if ($difficulty = $this->getState('filter.difficulty')) {
            $query->where('course.difficulty = ' . $db->q($difficulty));
        }

        if ($access = (int)$this->getState('filter.access')) {
            $query->where('course.access = ' . $access);
        }

        if ($teacher = (int)$this->getState('filter.teacher')) {
            $query->where('teacher.id = ' . $teacher);
        }

        $query->group('course.id');

        // Set ordering
        $primary   = $this->getState('list.ordering', 'course.title');
        $direction = $this->getState('list.direction', 'ASC');

        // Unless the pathway filter is in place, ordering makes no sense
        if ($primary == 'cp.ordering' && !$pathway) {
            $primary = 'course.title';
            $this->setState('list.ordering', $primary);
        }

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

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');
        $this->setState('filter.published', $published);

        $pathway = $this->getUserStateFromRequest($this->context . '.filter.pathway', 'filter_pathway');
        $this->setState('filter.pathway', $pathway);

        $tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag');
        $this->setState('filter.tag', $tag);

        $difficulty = $this->getUserStateFromRequest($this->context . '.filter.difficulty', 'filter_difficulty');
        $this->setState('filter.difficulty', $difficulty);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
        $this->setState('filter.access', $access);

        $teacher = $this->getUserStateFromRequest($this->context . '.filter.teacher', 'filter_teacher');
        $this->setState('filter.teacher', $teacher);

        parent::populateState($ordering, $direction);
    }
}
