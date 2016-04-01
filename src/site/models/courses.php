<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry;
use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

abstract class OscampusModelCourses extends OscampusModelSiteList
{
    protected $filter_fields = array(
        'ordering',
        'cp.ordering',
        'released',
        'course.released'
    );

    /**
     * @return JDatabaseQuery
     */
    protected function getListQuery()
    {
        $db         = $this->getDbo();
        $user       = OscampusFactory::getUser();
        $viewLevels = join(',', $user->getAuthorisedViewLevels());

        $tags  = sprintf(
            'GROUP_CONCAT(DISTINCT tag.title ORDER BY tag.title ASC SEPARATOR %s) AS tags',
            $db->quote(', ')
        );
        $query = $db->getQuery(true)
            ->select(
                array(
                    'course.*',
                    'COUNT(DISTINCT lesson.id) AS lesson_count',
                    $tags,
                    'user.name AS teacher'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->leftJoin('#__oscampus_teachers AS teacher ON teacher.id = course.teachers_id')
            ->leftJoin('#__oscampus_courses_tags AS ct ON ct.courses_id = course.id')
            ->leftJoin('#__oscampus_tags AS tag ON tag.id = ct.tags_id')
            ->leftJoin('#__users AS user ON user.id = teacher.users_id')
            ->where(
                array(
                    'course.published = 1',
                    'course.access IN (' . $viewLevels . ')',
                    'course.released <= NOW()'
                )
            )
            ->group('course.id');

        // Set user activity fields
        if ($userId = $this->getState('user.id')) {
            $query->select(
                array(
                    'COUNT(DISTINCT activity.id) AS lesson_progress',
                    'certificate.date_earned'
                )
            )
                ->leftJoin('#__oscampus_users_lessons AS activity ON activity.lessons_id = lesson.id and activity.users_id = ' . $userId)
                ->leftJoin('#__oscampus_certificates AS certificate ON certificate.courses_id = course.id AND certificate.users_id = ' . $userId);
        } else {
            $query->select(
                array(
                    '0 AS lesson_progress',
                    'NULL AS date_earned'
                )
            );
        }

        // Set pathway selection
        if ($pathwayId = (int)$this->getState('pathway.id')) {
            $query
                ->select('MIN(cp.pathways_id) AS pathways_id')
                ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
                ->where(
                    array(
                        'pathway.published = 1',
                        'pathway.access IN (' . $viewLevels . ')',
                        'pathway.id = ' . $pathwayId
                    )
                );
        } else {
            $query->select('0 AS pathways_id');
        }

        // Tag & topic filters are both on course tags
        $tagId   = (int)$this->getState('filter.tag');
        $topicId = (int)$this->getState('filter.topic');
        if ($tagId || $topicId) {
            $tags = join(', ', array_filter(array($tagId, $topicId)));

            $tagQuery = $db->getQuery(true)
                ->select('courses_id')
                ->from('#__oscampus_courses_tags')
                ->where(sprintf('tags_id IN (%s)', $tags))
                ->group('courses_id');

            $query->where(sprintf('course.id IN (%s)', $tagQuery));
        }

        // Teacher filter
        if ($teacherId = (int)$this->getState('filter.teacher')) {
            $query->where('teacher.id = ' . $teacherId);
        }

        // Difficulty filter
        if ($difficulty = $this->getState('filter.difficulty')) {
            $query->where('course.difficulty = ' . $db->quote($difficulty));
        }

        // Text filter
        if ($text = $this->getState('filter.text')) {
            $fields = array(
                'course.introtext',
                'course.description',
                'lesson.description'
            );
            $query->where($this->whereTextSearch($text, $fields));
        }

        // User progress status filter
        $progress = $this->getState('filter.progress');
        if ($progress !== null) {
            if ($progress == CourseStatus::NOT_STARTED) {
                $query->having('lesson_progress = 0');

            } elseif ($progress == CourseStatus::COMPLETED) {
                $query->having('date_earned IS NOT NULL');

            } elseif ($progress == CourseStatus::IN_PROGRESS) {
                $query->having('lesson_progress > 0');
            }
        }

        $order     = $this->getState('list.ordering');
        $direction = $this->getState('list.direction', 'ASC');
        if ($order) {
            $query->order($order . ' ' . $direction);
        }
        if ($order !== 'course.title') {
            $query->order('course.title ' . $direction);
        }

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        $tbd = JText::_('COM_OSCAMPUS_TEACHER_UNKNOWN');
        foreach ($items as $idx => $item) {
            if (!$item->teacher) {
                $item->teacher = $tbd;
            }
        }

        return $items;
    }

    /**
     * Get the current pathway information. Note that this only
     * makes sense if a pathway is selected
     *
     * @return object
     */
    public function getPathway()
    {
        $pathway = $this->getState('pathway');
        if ($pathway === null) {
            if ($pathwayId = (int)$this->getState('pathway.id')) {
                $db = $this->getDbo();

                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__oscampus_pathways')
                    ->where('id = ' . $pathwayId);

                $pathway = $db->setQuery($query)->loadObject();

                $pathway->metadata = new Registry($pathway->metadata);

                $this->setState('pathway', $pathway);
            }
        }
        return $pathway;
    }

    /**
     * Set filters that can be shared by all subclasses
     */
    protected function setSharedFilters()
    {
        $app = JFactory::getApplication();

        // Text search filter
        $text = $app->input->getString('filter_text');
        if ($text && strlen($text) < 3) {
            $app->enqueueMessage(JText::_('COM_OSCAMPUS_WARNING_SEARCH_MINTEXT'), 'notice');
            $text = $app->getUserState($this->context . '.filter.text', '');
        } else {
            $text = $this->getUserStateFromRequest($this->context . '.filter.text', 'filter_text', null, 'string');
        }
        $this->setState('filter.text', $text);

        // Tag filter
        $tagId = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', null, 'int');
        $this->setState('filter.tag', $tagId);

        // Teacher filter
        $teacherId = $this->getUserStateFromRequest($this->context . '.filter_teacher', 'filter_teacher', null, 'int');
        $this->setState('filter.teacher', $teacherId);

        // Course difficulty filter
        $difficulty = $this->getUserStateFromRequest(
            $this->context . '.filter.difficulty',
            'filter_difficulty',
            null,
            'cmd'
        );
        $this->setState('filter.difficulty', $difficulty);

        // User progress filter
        $progress = $this->getUserStateFromRequest(
            $this->context . '.filter.progress',
            'filter_progress',
            null,
            'cmd'
        );
        $this->setState('filter.progress', $progress);
    }

    /**
     * Determines if any filters are currently in play.
     *
     * @return bool
     */
    public function activeFilters()
    {
        $states = $this->getState()->getProperties();
        foreach ($states as $name => $state) {
            if (strpos($name, 'filter.') === 0 && $state != '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Although we want all subclasses to inherit the same collection
     * of filters, we also want a regular way to extend or override
     * the standard filters and maintain separate ordering and list limit
     * for the subclasses.
     *
     * @param string $ordering
     * @param string $direction
     *
     * @return void
     */
    protected function populateState($ordering = 'cp.ordering', $direction = 'ASC')
    {
        $userId = OscampusFactory::getUser()->id;
        $this->setState('user.id', $userId);

        $context       = $this->context;
        $this->context = 'com_oscampus.courses';

        $this->setSharedFilters();

        $this->context = $context;

        parent::populateState($ordering, $direction);
    }
}
