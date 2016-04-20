<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelLessons extends OscampusModelAdminList
{
    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'search',
            'course',
            'lessontype',
            'access',
            'lesson.ordering',
            'lesson.published',
            'module.title',
            'lesson.title',
            'lesson.type',
            'lesson.access',
            'lesson.id',
            'course.published',
            'course.title'
        );

        parent::__construct($config);

        $app = OscampusFactory::getApplication();

        if ($context = $app->input->getCmd('context', null)) {
            $this->context .= '.' . $context;
        }
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select(
                array(
                    'lesson.id',
                    'lesson.modules_id',
                    'module.courses_id',
                    'module.ordering AS module_ordering',
                    'lesson.ordering',
                    'lesson.title',
                    'lesson.alias',
                    'lesson.type',
                    'lesson.published',
                    'lesson.checked_out',
                    'lesson_view.title AS viewlevel_title',
                    'module.title AS module_title',
                    'course.title AS course_title',
                    'course.published AS course_published',
                    'course.released AS course_released',
                    'course.difficulty AS course_difficulty',
                    'editor_user.name AS editor'
                )
            )
            ->from('#__oscampus_lessons lesson')
            ->leftJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->leftJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->leftJoin('#__viewlevels AS lesson_view ON lesson_view.id = lesson.access')
            ->leftJoin('#__viewlevels AS course_view ON course_view.id = course.access')
            ->leftJoin('#__users AS editor_user ON editor_user.id = lesson.checked_out');

        if ($search = $this->getState('filter.search')) {
            $fields = array('lesson.title', 'lesson.alias');
            $query->where($this->whereTextSearch($search, $fields, 'lesson.id'));
        }

        $published = $this->getState('filter.published');
        if ($published != '') {
            $query->where('lesson.published = ' . (int)$published);
        }

        if ($course = (int)$this->getState('filter.course')) {
            $query->where('course.id = ' . $course);
        }

        if ($lessonType = $this->getState('filter.lessontype')) {
            $query->where('lesson.type = ' . $db->q($lessonType));
        }

        if ($access = (int)$this->getState('filter.access')) {
            $query->where('lesson.access = ' . $access);
        }

        $primary   = $this->getState('list.ordering', 'course.title');
        $direction = $this->getState('list.direction', 'ASC');
        if ($primary == 'lesson.ordering') {
            $query->order(
                array(
                    'module.ordering ' . $direction,
                    'course.title ' . $direction
                )
            );
        }
        $query->order($primary . ' ' . $direction);

        if (!in_array($primary, array('lesson.title', 'lesson.id', 'lesson.ordering'))) {
            $query->order('lesson.title ' . $direction);
        }

        return $query;
    }

    protected function populateState($ordering = 'lesson.title', $direction = 'ASC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'search', '', 'string');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'published');
        $this->setState('filter.published', $published);

        $course = $this->getUserStateFromRequest($this->context . '.filter.course', 'course', null, 'int');
        $this->setState('filter.course', $course);

        $lessonType = $this->getUserStateFromRequest($this->context . '.filter.lessontype', 'lessontype', '', 'string');
        $this->setState('filter.lessontype', $lessonType);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'access');
        $this->setState('filter.access', $access);

        parent::populateState($ordering, $direction);
    }
}
