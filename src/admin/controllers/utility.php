<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerUtility extends OscampusControllerBase
{
    /**
     * Check all active users without certificates to verify they
     * have not passed the course. If they have passed, optionally
     * create a certificate for them when ?create=1 is specified in the url
     */
    public function checkcerts()
    {
        echo '<h3>Checking/updating certificate records</h3>';

        $app = OscampusFactory::getApplication();
        $db  = OscampusFactory::getDbo();

        $create = $app->input->getInt('create', 0);
        if (!$create) {
            echo '<p>New certificates will not be created</p>';
        }

        $start = microtime(true);

        // Get list of active user IDs
        $query = $db->getQuery(true)
            ->select(
                array(
                    'activity.users_id',
                    'module.courses_id',
                    'user.name',
                    'user.username',
                    'count(*) AS lessonsViewed',
                    'MAX(activity.last_visit) AS last_visit',
                    'GROUP_CONCAT(CONCAT(lesson.type, \'|\' , activity.score)) AS scores'
                )
            )
            ->from('#__oscampus_users_lessons AS activity')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.id = activity.lessons_id')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->where('activity.users_id NOT IN (SELECT DISTINCT users_id FROM #__oscampus_certificates)')
            ->innerJoin('#__users AS user ON user.id = activity.users_id')
            ->group('activity.users_id, module.courses_id')
            ->order('activity.last_visit ASC');

        $activities = $db->setQuery($query)->loadObjectList();
        echo sprintf('<p>Reviewing %s course activities</p>', number_format(count($activities)));

        $query = $db->getQuery(true)
            ->select(
                array(
                    'course.id',
                    'course.title',
                    'count(lesson.id) AS totalLessons'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->group('course.id');

        $courses = $db->setQuery($query)->loadObjectList('id');

        $created = 0;
        echo '<ul>';
        foreach ($activities as $activity) {
            if (empty($courses[$activity->courses_id])) {
                $app->enqueueMessage('ERROR: Missing course - id ' . $activity->courses_id, 'error');
                return;
            }

            $course = $courses[$activity->courses_id];

            if ($activity->lessonsViewed == $course->totalLessons) {
                $passed = true;
                $scores = explode(',', $activity->scores);
                foreach ($scores as $row) {
                    list($type, $score) = explode('|', $row);
                    if ($type == 'quiz' && $score < Oscampus\Lesson\Type\Quiz::PASSING_SCORE) {
                        $passed = false;
                        break;
                    }
                }
                if ($passed) {
                    echo sprintf(
                        '<li>%s: %s (%s) for %s',
                        $activity->last_visit,
                        $activity->name,
                        $activity->username,
                        $course->title
                    );
                    if ($create) {
                        $certificate = (object)array(
                            'users_id'    => $activity->users_id,
                            'courses_id'  => $course->id,
                            'date_earned' => $activity->last_visit
                        );
                        $db->insertObject('#__oscampus_certificates', $certificate);
                        $error = $db->getErrorMsg();
                        echo sprintf(' [%s]', $error ?: 'CREATED');
                        if (!$error) {
                            $created += 1;
                        }
                    }
                    echo '</li>';
                }
            }
        }
        echo '</ul>';

        echo sprintf('<p>Created %s new certificates</p>', number_format($created));
        echo sprintf('<p>Total Runtime: %s</p>', number_format((microtime(true) - $start), 1));
    }

    /**
     * search all text/varchar fields in db for desired string
     */
    public function searchdb()
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
        echo '<p>Total Runtime: ' . number_format((microtime(true) - $start), 1) . '</p>';
    }
}
