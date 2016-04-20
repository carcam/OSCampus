<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Lesson\Type\Quiz;

defined('_JEXEC') or die();

class OscampusControllerUtility extends OscampusControllerBase
{
    /**
     * @var array[object[]]
     */
    protected $lessonScores = array();

    public function display()
    {
        echo $this->heading('Available utilities');

        $link = 'index.php?option=com_oscampus&task=utility.%s';

        echo $this->showList(
            array(
                JHtml::_(
                    'link',
                    sprintf($link, 'checkCerts'),
                    'Check for Missing Certificates'
                ),
                JHtml::_(
                    'link',
                    sprintf($link, 'checkActivity'),
                    'Check for invalid certificates and missing activity entries'
                ),
                JHtml::_(
                    'link',
                    sprintf($link, 'checkDuplicateLog'),
                    'Check for duplicate user log entries'
                ),
                JHtml::_(
                    'link',
                    sprintf($link, 'searchDB'),
                    'Find a string in the database'
                )
            )
        );
    }

    /**
     * Find duplicate log entries
     */
    public function checkDuplicateLog()
    {
        $legacy         = JError::$legacy;
        JError::$legacy = false;

        echo $this->heading('Check for duplicate log entries');

        $app = OscampusFactory::getApplication();
        $db  = OscampusFactory::getDbo();

        $remove = $app->input->getInt('remove', 0);
        if (!$remove) {
            echo '<p>Duplicate entries HAVE NOT been removed (url: \'remove=1\')</p>';
        }

        $start = microtime(true);

        $query = $db->getQuery(true)
            ->select('user.username, activity.users_id, activity.lessons_id, count(*) AS duplicates')
            ->from('#__oscampus_users_lessons AS activity')
            ->innerJoin('#__users AS user ON user.id = activity.users_id')
            ->group('activity.users_id, activity.lessons_id')
            ->having('duplicates > 1');

        $lessons = $db->setQuery($query)->loadObjectList();

        $users = array();
        foreach ($lessons as $lesson) {
            if (!isset($users[$lesson->username])) {
                $users[$lesson->username] = array(
                    'id'      => $lesson->users_id,
                    'lessons' => array()
                );
            }
            $users[$lesson->username]['lessons'][$lesson->lessons_id] = $lesson->duplicates;
        }

        echo $this->runtime($start, 'Query');

        $removalStart = microtime(true);

        $fixed = array();
        foreach ($users as $username => $user) {
            $userId  = $user['id'];
            $lessons = $user['lessons'];

            $status = sprintf('%s: %s Duplicates in %s Lessons', $username, array_sum($lessons), count($lessons));
            if ($remove) {
                $affected = 0;
                foreach ($lessons as $id => $count) {
                    $query = $db->getQuery(true)
                        ->delete('#__oscampus_users_lessons')
                        ->where(
                            array(
                                'users_id = ' . (int)$userId,
                                'lessons_id = ' . (int)$id
                            )
                        )
                        ->order('first_visit DESC, id DESC');
                    $db->setQuery($query . ' LIMIT ' . ($count - 1))->execute();
                    $affected += $db->getAffectedRows();
                }
                $status .= sprintf(' [Removed %s]', $affected);
            }
            $fixed[] = $status;
        }

        if ($remove) {
            echo $this->runtime($removalStart, 'Removal');
        }
        echo $this->runtime($start);

        echo $this->heading(sprintf('Found duplicate activity entries for %s users', count($users)));

        if ($fixed) {
            echo $this->showList($fixed);
        }

        JError::$legacy = $legacy;
    }

    /**
     * Check all active users without certificates to verify they
     * have not passed the course. If they have passed, optionally
     * create a certificate for them when ?create=1 is specified in the url
     */
    public function checkCerts()
    {
        $legacy         = JError::$legacy;
        JError::$legacy = false;

        echo $this->heading('Check for Missing Certificates');

        $app = OscampusFactory::getApplication();
        $db  = OscampusFactory::getDbo();

        $create = $app->input->getInt('create', 0);
        if (!$create) {
            echo '<p>New certificates HAVE NOT been created (url: \'create=1\')</p>';
        }

        $start = microtime(true);

        // Get list of candidates
        $query = $this->getActivityQuery()
            ->where('certificate.id IS NULL')
            ->having('lessonsViewed = totalLessons')
            ->order('last_visit ASC');

        $activities = $db->setQuery($query)->loadObjectList();
        echo $this->runtime($start, 'Query');

        $fixed      = array();
        $inProgress = array();
        $errors     = array();
        foreach ($activities as $activity) {
            $candidate = sprintf(
                '%s: %s [%s] (%s) for %s (%s)',
                $activity->last_visit,
                $activity->name,
                $activity->username,
                $activity->users_id,
                $activity->course_title,
                $activity->courses_id
            );

            if ($this->passedCourse($activity->users_id, $activity->courses_id)) {
                if ($create) {
                    try {
                        $certificate = (object)array(
                            'users_id'    => $activity->users_id,
                            'courses_id'  => $activity->courses_id,
                            'date_earned' => $activity->last_visit
                        );

                        $db->insertObject('#__oscampus_certificates', $certificate);

                        $fixed[] = '[CREATED CERTIFICATE] ' . $candidate;

                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            } else {
                $inProgress[] = $candidate;
            }
        }

        echo $this->runtime($start);

        echo $this->heading(number_format(count($errors)) . ' Database Errors');
        echo $this->showList($errors);

        echo $this->heading(number_format(count($fixed)) . ' Missing Certificates');
        echo $this->showList($fixed);

        echo $this->heading(number_format(count($inProgress)) . ' Classes in Progress');
        echo $this->showList($inProgress);

        JError::$legacy = $legacy;
    }

    /**
     * Look for certificates awarded for incomplete courses
     */
    public function checkActivity()
    {
        $legacy         = JError::$legacy;
        JError::$legacy = false;

        $app = OscampusFactory::getApplication();
        $db  = OscampusFactory::getDbo();

        $fixFailed  = $app->input->getInt('failed', 0);
        $fixMissing = $app->input->getCmd('missing', '');

        echo $this->heading('Check for Invalid Certificates');

        if (!$fixFailed) {
            echo '<p>Certificates for failed classes HAVE NOT been removed (url: \'failed=1\')</p>';
        }
        if (!$fixMissing) {
            echo '<p>Missing activity logs HAVE NOT been created (url: \'missing={lessonType}\')</p>';
        } else {
            echo sprintf('<p>Missing activity logs HAVE been created for \'%s\' lessons</p>', $fixMissing);
        }

        $start = microtime(true);

        // Get list of active user IDs
        $query = $this->getActivityQuery()
            ->where('certificate.id IS NOT NULL')
            ->order('last_visit ASC');

        $activities = $db->setQuery($query)->loadObjectList();
        echo $this->runtime($start, 'Query');

        $valid     = array();
        $notPassed = array();
        $missing   = array();
        $errors    = array();
        foreach ($activities as $activity) {
            $userId   = $activity->users_id;
            $courseId = $activity->courses_id;

            $candidate = sprintf(
                '%s: %s [%s] (%s) for %s (%s)',
                $activity->last_visit,
                $activity->name,
                $activity->username,
                $userId,
                $activity->course_title,
                $courseId
            );
            if ($this->passedCourse($userId, $courseId)) {
                $valid[] = $candidate;
            } else {
                $scores     = $this->getLessonScores($userId, $courseId);
                $emptyTotal = 0;
                $emptyFixed = 0;
                foreach ($scores as $score) {
                    if ($score->id === null) {
                        $emptyTotal++;

                        if ($score->type == $fixMissing) {
                            try {
                                $insertObject = (object)array(
                                    'users_id'    => $userId,
                                    'lessons_id'  => $score->lessons_id,
                                    'completed'   => $activity->last_visit,
                                    'score'       => ($score->type == 'quiz' ? 100 : 0),
                                    'visits'      => 1,
                                    'first_visit' => $activity->last_visit,
                                    'last_visit'  => $activity->last_visit
                                );

                                $db->insertObject('#__oscampus_users_lessons', $insertObject, 'id');
                                $emptyFixed++;

                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }
                        }
                    }
                }

                if ($emptyTotal) {
                    if ($fixMissing) {
                        $candidate = sprintf('[Created %s of %s] ', $emptyFixed, $emptyTotal) . $candidate;
                    }
                    $missing[] = $candidate;

                } else {
                    if ($fixFailed) {
                        try {
                            $query = $db->getQuery(true)
                                ->delete('#__oscampus_certificates')
                                ->where('id = ' . $activity->certificates_id);

                            $db->setQuery($query)->execute();

                            $candidate = sprintf('[REMOVED CERTIFICATE #%s] ', $activity->certificates_id) . $candidate;

                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }

                    }

                    $notPassed[] = $candidate;
                }
            }
        }

        echo $this->runtime($start);

        echo $this->heading(number_format(count($errors)) . ' Database Errors');
        echo $this->showList($errors);

        echo $this->heading(number_format(count($missing)) . ' Missing Log Entries');
        echo $this->showList($missing);

        echo $this->heading(number_format(count($notPassed)) . ' Failed Classes');
        echo $this->showList($notPassed);

        echo $this->heading(number_format(count($valid)) . ' Valid Certificates');

        JError::$legacy = $legacy;
    }

    /**
     * search all text/varchar fields in db for desired string
     */
    public function searchDB()
    {
        // The text we're looking for
        $app = OscampusFactory::getApplication();

        $regex = $app->input->getString('search', null);
        if (!$regex) {
            echo '<h3>DB Search</h3>';
            echo '<p>Use ?search to specify the string to search for</p>';
            return;
        }

        echo '<h3>DB Search: ' . $regex . '</h3>';

        $db = OscampusFactory::getDbo();

        $start = microtime(true);

        $html   = array('<ul>');
        $tables = $db->setQuery('SHOW TABLES')->loadColumn();
        foreach ($tables as $table) {
            // Look for tables that have a single auto increment primary key
            $query   = "SHOW COLUMNS FROM {$table} WHERE `Key` = 'PRI' AND `Extra` = 'auto_increment'";
            $idField = $db->setQuery($query)->loadColumn();
            if ($error = $db->getErrorMsg()) {
                echo 'ERROR: ' . $error;
                return;
            }

            if (count($idField) == 1) {
                $query = "SHOW COLUMNS FROM {$table} WHERE type = 'text' OR type like 'varchar%'";
                if ($fields = $db->setQuery($query)->loadColumn()) {
                    $idField = $idField[0];
                    array_unshift($fields, $idField);

                    $where = array();
                    foreach ($fields as $field) {
                        $where[] = $db->quoteName($field) . ' RLIKE ' . $db->quote($regex);
                    }
                    $query = $db->getQuery(true)
                        ->select($fields)
                        ->from($table)
                        ->where($where, 'OR');

                    if ($rows = $db->setQuery($query)->loadAssocList()) {
                        $html[] = '<li>' . $table . ' (' . number_format(count($rows)) . ')<ul>';
                        foreach ($rows as $row) {
                            // Report
                            $fields = array_keys($row);
                            array_shift($fields);
                            $html[] = sprintf(
                                '<li>%s: %s (%s)</li>',
                                $idField,
                                $row[$idField],
                                join(', ', $fields)
                            );
                        }
                        $html[] = '</ul></li>';
                    }
                }
            }
        }

        $html[] = '</ul>';

        echo join("\n", $html);
        echo $this->runtime($start);
    }

    /**
     * @param string $heading
     *
     * @return string
     */
    protected function heading($heading)
    {
        return '<h3>' . $heading . '</h3>';
    }

    /**
     * @param float  $start
     * @param string $name
     *
     * @return string
     */
    protected function runtime($start, $name = 'Total')
    {
        return sprintf('<p>%s Runtime: %s seconds</p>', $name, number_format((microtime(true) - $start), 1));
    }

    /**
     * Get the base query for finding summary user activities
     *
     * @return JDatabaseQuery
     */
    protected function getActivityQuery()
    {
        $db = OscampusFactory::getDbo();

        $courseQuery = $db->getQuery(true)
            ->select('course.id, course.title, count(*) AS totalLessons')
            ->from('#__oscampus_modules AS module')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->group('courses_id');

        $query = $db->getQuery(true)
            ->select(
                array(
                    'activity.users_id',
                    'module.courses_id',
                    'certificate.id AS certificates_id',
                    'user.name,user.username',
                    'course.title AS course_title',
                    'count(*) AS lessonsViewed',
                    'course.totalLessons',
                    'MIN(activity.first_visit) AS first_visit',
                    'MAX(activity.last_visit) AS last_visit'
                )
            )
            ->from('#__oscampus_users_lessons AS activity')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.id = activity.lessons_id')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin(sprintf('(%s) AS course ON course.id = module.courses_id', $courseQuery))
            ->leftJoin('#__oscampus_certificates AS certificate ON certificate.users_id = activity.users_id AND certificate.courses_id = module.courses_id')
            ->innerJoin('#__users AS user ON user.id = activity.users_id')
            ->group('activity.users_id, module.courses_id');

        return $query;
    }

    /**
     * @param int $userId
     * @param int $courseId
     *
     * @return object[]
     */
    protected function getLessonScores($userId, $courseId)
    {
        if (!isset($this->lessonScores[$userId])) {
            $this->lessonScores[$userId] = array();
        }

        if (!isset($this->lessonScores[$userId][$courseId])) {
            $db = OscampusFactory::getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'activity.id',
                        'lesson.id AS lessons_id',
                        'lesson.type',
                        'activity.score',
                        'activity.completed'
                    )
                )
                ->from('#__oscampus_courses AS course')
                ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
                ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
                ->leftJoin('#__oscampus_users_lessons AS activity ON activity.lessons_id = lesson.id AND activity.users_id = ' . $userId)
                ->where('course.id = ' . $courseId);

            $this->lessonScores[$userId][$courseId] = $db->setQuery($query)->loadObjectList();
        }

        if (!empty($this->lessonScores[$userId][$courseId])) {
            return $this->lessonScores[$userId][$courseId];
        }

        return array();
    }

    /**
     * see if user passed the course
     *
     * @param int $userId
     * @param int $courseId
     *
     * @return bool
     */
    protected function passedCourse($userId, $courseId)
    {
        $activities = $this->getLessonScores($userId, $courseId);
        foreach ($activities as $activity) {
            if ($activity->id === null
                || ($activity->type == 'quiz' && $activity->score < Oscampus\Lesson\Type\Quiz::PASSING_SCORE)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Standard display of a list of items
     *
     * @param string[] $items
     *
     * @return string
     */
    protected function showList(array $items)
    {
        if ($items) {
            return '<ul><li>' . join('</li><li>', $items) . '</li></ul>';
        }

        return '';
    }

    protected function backupTable($source)
    {
        $errorLegacy    = JError::$legacy;
        JError::$legacy = false;

        $backup = $source . '_bak';

        $db = OscampusFactory::getDbo();

        $db->setQuery("DROP TABLE IF EXISTS {$backup}")->execute();
        $db->setQuery("CREATE TABLE {$backup} LIKE {$source}")->execute();
        $db->setQuery("INSERT {$backup} SELECT * FROM {$source}")->execute();

        JError::$legacy = $errorLegacy;
    }

    protected function restoreTable($source)
    {
        $errorLegacy    = JError::$legacy;
        JError::$legacy = false;

        $backup = $source . '_bak';

        $db     = OscampusFactory::getDbo();
        $tables = $db->getTableList();

        $restored = false;
        if (in_array($db->replacePrefix($backup), $tables)) {
            $db->setQuery("DELETE FROM {$source}");
            $db->setQuery("INSERT {$source} SELECT * FROM {$backup}");
            $db->dropTable($backup);
            $restored = true;
        }

        JError::$legacy = $errorLegacy;

        return $restored;
    }
}
