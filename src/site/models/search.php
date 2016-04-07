<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JLoader::import('courselist', __DIR__);

class OscampusModelSearch extends OscampusModelCourselist
{

    public function getItems()
    {
        $results = (object)array(
            'pathways' => $this->getPathways(),
            'courses'  => $this->getCourses(),
            'lessons' => $this->getLessons()
        );

        return $results;
    }

    public function getCourses()
    {
        $types = (array)$this->getState('show.types');

        if (!$types || in_array('C', $types)) {
            return parent::getItems();
        }

        return array();
    }

    public function getPathways()
    {
        $types = (array)$this->getState('show.types');

        if (!$types || in_array('P', $types)) {
            /** @var OscampusModelPathways $model */
            $model = OscampusModel::getInstance('Pathways');

            $model->setState('filter.text', $this->getState('filter.text'));
            $model->setState('filter.tag', $this->getState('filter.tag'));

            return $model->getItems();
        }

        return array();
    }

    public function getLessons()
    {
        if (array_filter($this->getActiveFilters())) {
            $types = (array)$this->getState('show.types');
            if (!$types || in_array('L', $types)) {
                $db = $this->getDbo();

                $query = $db->getQuery(true)
                    ->select(
                        array(
                            'lesson.id',
                            'module.courses_id',
                            'lesson.title',
                            'lesson.description'
                        )
                    )
                    ->from('#__oscampus_lessons AS lesson')
                    ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                    ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
                    ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                    ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
                    ->where(
                        array(
                            'lesson.type = ' . $db->quote('wistia'),
                            'lesson.published = 1',
                            'course.published = 1',
                            $this->whereAccess('course.access'),
                            'pathway.published = 1',
                            $this->whereAccess('pathway.access')
                        )
                    )
                    ->group('lesson.id');

                if ($text = $this->getState('filter.text')) {
                    $query->where($this->whereTextSearch($text, array('lesson.title', 'lesson.description')));
                }

                if ($tagId = (int)$this->getState('filter.tag')) {
                    $query
                        ->leftJoin('#__oscampus_courses_tags AS ct ON ct.courses_id = course.id')
                        ->where('ct.tags_id = ' . $tagId);
                }

                $query->order('lesson.title ASC');

                $start   = $this->getState('list.start', 0);
                $limit   = $this->getState('list.limit', 0);
                $lessons = $db->setQuery($query, $start, $limit)->loadObjectList();

                return $lessons;
            }
        }

        return array();
    }

    protected function populateState($ordering = 'course.title', $direction = 'ASC')
    {
        $app = JFactory::getApplication();

        // Display result types
        $types = (array)$this->getUserStateFromRequest($this->context . '.types', 'types', array(), 'array');
        $this->setState('show.types', array_filter($types));

        // Text search filter
        $minLength = 2;
        $text      = $app->input->getString('text');
        if ($text && strlen($text) < $minLength) {
            $app->enqueueMessage(JText::sprintf('COM_OSCAMPUS_WARNING_SEARCH_MINTEXT', $minLength), 'notice');
            $text = $app->getUserState($this->context . '.filter.text', '');
        } else {
            $text = $this->getUserStateFromRequest($this->context . '.filter.text', 'text', null, 'string');
        }
        $this->setState('filter.text', $text);

        // Tag filter
        $tagId = $this->getUserStateFromRequest($this->context . '.filter.tag', 'tag', null, 'int');
        $this->setState('filter.tag', $tagId);

        // Teacher filter
        $teacherId = $this->getUserStateFromRequest($this->context . '.filter.teacher', 'teacher', null, 'int');
        $this->setState('filter.teacher', $teacherId);

        // Course difficulty filter
        $difficulty = $this->getUserStateFromRequest(
            $this->context . '.filter.difficulty',
            'difficulty',
            null,
            'cmd'
        );
        $this->setState('filter.difficulty', $difficulty);

        // User progress filter
        $progress = $this->getUserStateFromRequest(
            $this->context . '.filter.progress',
            'progress',
            null,
            'cmd'
        );
        $this->setState('filter.progress', $progress);

        parent::populateState($ordering, $direction);

        // Ignore pagination for now
        $this->setState('list.start', 0);
        $this->setState('list.limit');
    }
}
