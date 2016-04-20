<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

class OscampusModelCourselist extends OscampusModelSiteList
{
    protected $filter_fields = array(
        'difficulty',
        'progress',
        'tag',
        'teacher',
        'text'
    );

    /**
     * @return JDatabaseQuery
     */
    protected function getListQuery()
    {
        $db   = $this->getDbo();
        $user = $this->getState('user');

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
                    'teacher_user.name AS teacher'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->leftJoin('#__oscampus_teachers AS teacher ON teacher.id = course.teachers_id')
            ->leftJoin('#__oscampus_courses_tags AS ct ON ct.courses_id = course.id')
            ->leftJoin('#__oscampus_tags AS tag ON tag.id = ct.tags_id')
            ->leftJoin('#__users AS teacher_user ON teacher_user.id = teacher.users_id')
            ->where(
                array(
                    'course.published = 1',
                    $this->whereAccess('course.access', $user),
                    'course.released <= NOW()'
                )
            )
            ->group('course.id');

        // Set user activity fields
        if ($user->id) {
            $query->select(
                array(
                    'activity.users_id',
                    'COUNT(DISTINCT activity.id) AS lessons_viewed',
                    'certificate.id AS certificates_id',
                    'certificate.date_earned'
                )
            )
                ->leftJoin('#__oscampus_users_lessons AS activity ON activity.lessons_id = lesson.id and activity.users_id = ' . $user->id)
                ->leftJoin('#__oscampus_certificates AS certificate ON certificate.courses_id = course.id AND certificate.users_id = ' . $user->id);
        } else {
            $query->select(
                array(
                    '0 AS users_id',
                    '0 AS lessons_viewed',
                    '0 AS certificates_id',
                    'NULL AS date_earned'
                )
            );
        }

        // Tag filter
        if ($tagId = (int)$this->getState('filter.tag')) {
            $tagQuery = $db->getQuery(true)
                ->select('courses_id')
                ->from('#__oscampus_courses_tags')
                ->where(sprintf('tags_id = ' . $tagId))
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
                $query->having('lessons_viewed = 0');

            } elseif ($progress == CourseStatus::COMPLETED) {
                $query->having('certificates.id > 0');

            } elseif ($progress == CourseStatus::IN_PROGRESS) {
                $query->having('lessons_viewed > 0');
            }
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

    protected function populateState($ordering = null, $direction = null)
    {
        $this->setState('user', OscampusFactory::getUser());

        parent::populateState($ordering, $direction);
    }
}
