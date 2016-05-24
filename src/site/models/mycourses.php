<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

class OscampusModelMycourses extends OscampusModelList
{
    protected $filter_fields = array(
        'course.title'
    );

    public function getListQuery()
    {
        $user = OscampusFactory::getUser($this->getState('user.id'));
        $db   = $this->getDbo();

        $lastLessonSubquery = $db->getQuery(true)
            ->select('a2.id')
            ->from('#__oscampus_users_lessons AS a2')
            ->innerJoin('#__oscampus_lessons AS l2 ON l2.id = a2.lessons_id')
            ->innerJoin('#__oscampus_modules AS m2 ON m2.id = l2.modules_id')
            ->where(
                array(
                    'a2.users_id = activity.users_id',
                    'm2.courses_id = module.courses_id',
                    'a2.last_visit > activity.last_visit'
                )
            );

        $lastLessonQuery = $db->getQuery(true)
            ->select(
                array(
                    'activity.lessons_id',
                    'module.courses_id'
                )
            )
            ->from('#__oscampus_users_lessons AS activity')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.id = activity.lessons_id')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->where(
                array(
                    'activity.users_id = ' . $user->id,
                    sprintf('NOT EXISTS(%s)', $lastLessonSubquery)
                )
            )
            ->group('module.courses_id');

        $activityQuery = $db->getQuery(true)
            ->select(
                array(
                    'module.courses_id',
                    'activity.users_id',
                    'MIN(activity.first_visit) AS first_visit',
                    'MAX(activity.last_visit) AS last_visit',
                    'last_lesson.lessons_id AS last_lesson',
                    'certificate.id AS certificates_id',
                    'certificate.date_earned',
                    'count(*) AS lessons_viewed'
                )
            )
            ->from('#__oscampus_users_lessons AS activity')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.id = activity.lessons_id')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin("({$lastLessonQuery}) AS last_lesson ON last_lesson.courses_id = module.courses_id")
            ->leftJoin('#__oscampus_certificates AS certificate ON certificate.courses_id = module.courses_id AND certificate.users_id = activity.users_id')
            ->where(
                array(
                    'activity.users_id = ' . $user->id,
                    'activity.completed'
                )
            )
            ->group('module.courses_id');

        $courseQuery = $db->getQuery(true)
            ->select(
                array(
                    'course.id',
                    'course.title',
                    'count(*) AS lesson_count',
                    'user_activity.lessons_viewed',
                    'user_activity.users_id',
                    'user_activity.first_visit',
                    'user_activity.last_visit',
                    'user_activity.last_lesson',
                    'user_activity.certificates_id',
                    'user_activity.date_earned'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->innerJoin("({$activityQuery}) AS user_activity ON user_activity.courses_id = course.id")
            ->group('course.id');

        $ordering  = $this->getState('list.ordering');
        $direction = $this->getState('list.direction', 'ASC');
        $courseQuery->order($ordering . ' ' . $direction);
        if ($ordering != 'course.title') {
            $courseQuery->order('course.title ' . $direction);
        }

        return $courseQuery;
    }

    /**
     * Very possibly a bad idea, but I really want my prototype class!
     *
     * @param string $query
     * @param int    $limitstart
     * @param int    $limit
     *
     * @return CourseStatus[]
     */
    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        $this->_db->setQuery($query, $limitstart, $limit);
        $result = $this->_db->loadObjectList('id', '\\Oscampus\\Activity\\CourseStatus');

        if (version_compare(phpversion(), '5.6.21', 'ge')) {
            /** @var CourseStatus $item */
            foreach ($result as $item) {
                $item->setProperties($item->toArray());
            }
        }

        return $result;
    }

    protected function populateState($ordering = 'course.title', $direction = 'ASC')
    {
        parent::populateState($ordering, $direction);

        $this->setState('list.limit', 0);
        $this->setState('list.start', 0);
    }
}
