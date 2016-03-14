<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

class OscampusModelCourses extends OscampusModelSiteList
{
    protected $filter_fields = array(
        'ordering', 'cp.ordering',
        'released', 'course.released'
    );

    /**
     * @var CourseStatus[]
     */
    protected $activity = null;

    protected function getListQuery()
    {
        return $this->getBaseQuery();
    }

    /**
     * A custom method to provide the base query. This will allow subclasses
     * to do their own modifications in $this->getListQuery(). Note that it
     * is a grouped query (on course id). Any modifications must take this
     * into account. Use of pathway data will be inconsistent and should not
     * be used unless a pathway filter has been applied. $this->setFilters()
     * can be overridden to provide custom or additional filtering in subclasses
     * and includes acceptance of the input variable 'pid' for standard pathway
     * filtering.
     *
     * @return JDatabaseQuery
     */
    protected function getBaseQuery()
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
                    'cp.*',
                    'pathway.title AS pathway',
                    'user.name AS teacher',
                    $tags,
                    'course.*',
                    'COUNT(DISTINCT lesson.id) AS lesson_count'
                )
            )
            ->from('#__oscampus_pathways AS pathway')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = cp.courses_id')
            ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->leftJoin('#__oscampus_teachers AS teacher ON teacher.id = course.teachers_id')
            ->leftJoin('#__oscampus_courses_tags AS ct ON ct.courses_id = course.id')
            ->leftJoin('#__oscampus_tags AS tag ON tag.id = ct.tags_id')
            ->leftJoin('#__users AS user ON user.id = teacher.users_id')
            ->where(
                array(
                    'pathway.published = 1',
                    'course.published = 1',
                    'course.access IN (' . $viewLevels . ')',
                    'course.released <= NOW()'
                )
            )
            ->group('course.id');

        if ($pathwayId = (int)$this->getState('filter.pathway')) {
            $query->where('pathway.id = ' . $pathwayId);
        }

        if ($difficulty = $this->getState('filter.difficulty')) {
            $query->where('course.difficulty = ' . $db->quote($difficulty));
        }

        $completion = $this->getState('filter.completion');
        if ($completion !== null) {
            $activities = $this->getUserActivity();

            if ($completion == CourseStatus::NOT_STARTED) {
                $query->where(
                    sprintf(
                        'course.id NOT IN (%s)',
                        join(', ', array_keys($activities))
                    )
                );

            } else {
                // Use existence of a certificate to test if course has been completed
                $ids = array();
                foreach ($activities as $courseId => $activity) {
                    if ($completion == CourseStatus::COMPLETED) {
                        if ($activity->certificates_id) {
                            $ids[] = $courseId;
                        }

                    } elseif ($completion == CourseStatus::IN_PROGRESS) {
                        if (!$activity->certificates_id && $activity->lessons_taken > 0) {
                            $ids[] = $courseId;
                        }
                    }
                }
                if ($ids) {
                    $query->where(
                        sprintf(
                            'course.id IN (%s)',
                            join(', ', $ids)
                        )
                    );
                }
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
        $items    = parent::getItems();
        $activity = $this->getUserActivity();

        $tbd = JText::_('COM_OSCAMPUS_TEACHER_UNKNOWN');
        foreach ($items as $idx => $item) {
            if (!$item->teacher) {
                $item->teacher = $tbd;
            }
            $item->progress = isset($activity[$item->id]) ? $activity[$item->id]->progress : 0;
        }

        return $items;
    }

    /**
     * Get user's course activity
     *
     * @return CourseStatus[]
     */
    protected function getUserActivity()
    {
        if ($this->activity === null) {
            /** @var OscampusModelMycourses $model */
            $model = OscampusModel::getInstance('Mycourses');

            $this->activity = $model->getItems();
        }

        return $this->activity;
    }

    /**
     * Get the current pathway information. Note that this only
     * makes sense if a pathway filter has been supplied
     *
     * @return object
     */
    public function getPathway()
    {
        $pathway = $this->getState('pathway');
        if ($pathway === null) {
            if ($pathwayId = (int)$this->getState('filter.pathway')) {
                $db = $this->getDbo();

                $db->setQuery('Select * From #__oscampus_pathways Where id = ' . $pathwayId);
                $pathway = $db->loadObject();

                $pathway->metadata = new JRegistry($pathway->metadata);

                $this->setState('pathway', $pathway);
            }
        }
        return $pathway;
    }

    /**
     * Custom method to allow overriding by subclasses
     */
    protected function setFilters()
    {
        $app = JFactory::getApplication();

        $pathwayId = $app->input->getInt('pid');
        $this->setState('filter.pathway', $pathwayId);

        $difficulty = $this->getUserStateFromRequest($this->context . '.filter.difficulty', 'difficulty', null, 'cmd');
        $this->setState('filter.difficulty', $difficulty);

        $completion = $this->getUserStateFromRequest($this->context . '.filter.completion', 'completion', null, 'cmd');
        $this->setState('filter.completion', $completion);
    }

    protected function populateState($ordering = 'cp.ordering', $direction = 'ASC')
    {
        $this->setFilters();

        parent::populateState($ordering, $direction);

        // Ignore pagination for now
        $this->setState('list.start', 0);
        $this->setState('list.limit');
    }
}
