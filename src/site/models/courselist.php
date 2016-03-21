<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use JRegistry as Registry;
use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

JLoader::import('filtered', __DIR__);

abstract class OscampusModelCourselist extends OscampusModelFiltered
{
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
                    '0 AS progress',
                    'NULL AS last_lesson',
                    'NULL AS certificates_id',
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

        // Pathway Filter
        if ($pathwayId = (int)$this->getState('filter.pathway')) {
            $query->where('pathway.id = ' . $pathwayId);
        }

        // Tag filter
        if ($tagId = (int)$this->getState('filter.tag')) {
            $tagQuery = $db->getQuery(true)
                ->select('courses_id')
                ->from('#__oscampus_courses_tags')
                ->where('tags_id = ' . $tagId)
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
            $query->where($this->getWhereTextSearch($text, $fields));
        }

        // User completion status filter
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

            if (isset($activity[$item->id])) {
                $userActivity = $activity[$item->id];

                $item->progress        = $userActivity->progress;
                $item->last_lesson     = $userActivity->last_lesson;
                $item->certificates_id = $userActivity->certificates_id;
            }
        }

        return $items;
    }
}
