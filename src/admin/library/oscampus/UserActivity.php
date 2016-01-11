<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabase;
use JUser;
use OscampusFactory;

defined('_JEXEC') or die();

class UserActivity extends AbstractBase
{
    /**
     * @var JUser
     */
    protected $user = null;

    /**
     * @var array[]
     */
    protected $lessons = array();

    public function __construct(JDatabase $dbo, JUser $user = null)
    {
        parent::__construct($dbo);

        $this->user = $user ?: OscampusFactory::getUser();
    }

    /**
     * Change to the selected user's tracking data
     *
     * @param int $id
     *
     * @return JUser
     */
    public function setUser($id)
    {
        if ($id != $this->user->id) {
            $this->user    = OscampusFactory::getUser($id);
            $this->lessons = array();
        }

        return $this->user;
    }

    /**
     * Internal method for loading activity records for the currently set user
     *
     * @param $courseId
     *
     * @return array
     */
    protected function get($courseId)
    {
        if ($this->user->id) {
            if (!isset($this->lessons[$courseId])) {
                $query = $this->dbo->getQuery(true)
                    ->select('ul.*, module.courses_id')
                    ->from('#__oscampus_lessons lesson')
                    ->innerJoin('#__oscampus_modules module ON module.id = lesson.modules_id')
                    ->leftJoin('#__oscampus_users_lessons ul ON ul.lessons_id = lesson.id')
                    ->where(
                        array(
                            'ul.users_id = ' . $this->user->id,
                            'module.courses_id = ' . (int)$courseId
                        )
                    )
                    ->order('module.ordering ASC, lesson.ordering ASC');

                $this->lessons[$courseId] = $this->dbo->setQuery($query)->loadObjectList('lessons_id');
            }
        }

        return $this->lessons[$courseId];
    }

    /**
     * Get the course ID from the lesson ID since lessons doesn't
     * provide this information directly
     *
     * @param int $lessonId
     *
     * @return int
     */
    protected function getCourseIdFromLessonId($lessonId)
    {

        $query = $this->dbo->getQuery(true)
            ->select('courses_id')
            ->from('#__oscampus_modules m1')
            ->innerJoin('#__oscampus_lessons l1 ON l1.modules_id = m1.id')
            ->where('l1.id = ' . (int)$lessonId);

        return $this->dbo->setQuery($query)->loadColumn();
    }

    /**
     * Get all activity records for this user for the selected course
     *
     * @param int $courseId
     *
     * @return object[]
     */
    public function getCourse($courseId)
    {
        $lessons = $this->get($courseId);
        return $lessons;
    }

    /**
     * Get tracking data for the current user in the selected lesson.
     * If the course ID is already known, there is a small performance
     * improvement if it is provided also.
     *
     * @param int $lessonId
     * @param int $courseId
     *
     * @return object|null
     */
    public function getLesson($lessonId, $courseId = null)
    {
        $courseId = $courseId ?: $this->getCourseIdFromLessonId($lessonId);
        $lessons  = $this->get($courseId);
        if (empty($lessons[$lessonId])) {
            return null;
        }

        return $lessons[$lessonId];
    }

    /**
     * Update last visit date and number of visits
     *
     * @param int $lessonId
     */
    public function visitLesson($lessonId)
    {
        if ($this->user->id) {
            $app = OscampusFactory::getApplication();

            $visited = $app->getUserState('oscampus.lesson.visited');
            if ($visited != $lessonId) {
                $activity = $this->getStatus($lessonId);
                if ($activity->id) {
                    $activity->last_visit = OscampusFactory::getDate()->toSql();
                    $activity->visits++;
                }

                $this->setStatus($activity);

                $app->setUserState('oscampus.lesson.visited', $lessonId);
            }
        }
    }

    /**
     * Get an activity status record
     *
     * @param int $lessonId
     * @param int $userId
     *
     * @return object|null
     */
    public function getStatus($lessonId, $userId = null)
    {
        $userId = $userId ?: $this->user->id;
        if ($userId) {
            $query    = $this->dbo->getQuery(true)
                ->select('*')
                ->from('#__oscampus_users_lessons')
                ->where(
                    array(
                        'users_id = ' . (int)$userId,
                        'lessons_id = ' . (int)$lessonId
                    )
                );
            $activity = $this->dbo->setQuery($query)->loadObject();
        }

        if (empty($activity)) {
            $activity = $this->dbo->getTableColumns('#__oscampus_users_lessons');
            $activity = array_fill_keys(array_keys($activity), null);
        }

        return $activity;
    }

    /**
     * insert/update an activity status record
     *
     * @param array|object $activity
     *
     * @return bool
     */
    public function setStatus($activity)
    {
        if (is_array($activity)) {
            $activity = (object)$activity;
        }

        if (is_object($activity) && !empty($activity->users_id) && !empty($activity->lessons_id)) {
            $thisVisit = OscampusFactory::getDate()->toSql();

            if (empty($activity->id)) {
                $activity->first_visit = $thisVisit;
                $activity->last_visit  = $thisVisit;
                $activity->visits      = 1;

                $success = $this->dbo->insertObject('#__oscampus_users_lessons', $activity);

            } else {
                $success = $this->dbo->updateObject('#__oscampus_users_lessons', $activity, 'id');
            }
            return $success;
        }

        return false;
    }
}
