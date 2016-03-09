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
    public function getListQuery()
    {
        $user = OscampusFactory::getUser($this->getState('user.id'));
        $db   = $this->getDbo();

        $activityQuery = $db->getQuery(true)
            ->select(
                array(
                    'module.courses_id',
                    'activity.users_id',
                    'MIN(activity.first_visit) AS first_visit',
                    'MAX(activity.last_visit) AS last_visit',
                    sprintf(
                        'GROUP_CONCAT(CONCAT_WS(%s, lesson.type, activity.score, activity.lessons_id)) AS scores',
                        $db->quote(':')
                    ),
                    'certificate.id AS certificates_id',
                    'certificate.date_earned',
                    'count(*) AS lessons_taken'
                )
            )
            ->from('#__oscampus_users_lessons AS activity')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.id = activity.lessons_id')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
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
                    'count(*) AS lessons',
                    'user_activity.lessons_taken',
                    'user_activity.users_id',
                    'user_activity.first_visit',
                    'user_activity.last_visit',
                    'user_activity.scores',
                    'user_activity.certificates_id',
                    'user_activity.date_earned'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->innerJoin("({$activityQuery}) AS user_activity ON user_activity.courses_id = course.id")
            ->group('course.id');

        if ($pid = (int)$this->getState('filter.pathway')) {
            $courseQuery
                ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                ->where('cp.pathways_id = ' . $pid);
        }

        $ordering  = $this->getState('list.ordering', 'course.title');
        $direction = $this->getState('list.direction', 'ASC');
        $courseQuery->order($ordering . ' ' . $direction);

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

        return $result;
    }

    protected function populateState($ordering = 'course.title', $direction = 'ASC')
    {
        $app = OscampusFactory::getApplication();

        $pathwayId = $app->input->getInt('pid');
        $this->setState('filter.pathway', $pathwayId);

        $this->setState('list.limit', 0);
        $this->setState('list.start', 0);
    }
}
