<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusController extends OscampusControllerBase
{
    protected $default_view = 'courses';

    /**
     * Temporary task to update old guru URLs to new OSCampus URLs
     */
    public function dbfix()
    {
        $db = OscampusFactory::getDbo();

        $start = microtime(true);

        $tables = $db->setQuery('show tables')->loadColumn();
        foreach ($tables as $table) {
            $idField = $db->setQuery("show columns from {$table} where `Key` = 'PRI' AND `Extra` = 'auto_increment'")
                ->loadColumn();
            if ($error = $db->getErrorMsg()) {
                echo 'ERROR: ' . $error;
                return;
            }

            if (count($idField) == 1) {
                $fields = $db->setQuery("show columns from {$table} where type = 'text'")->loadColumn();

                $idField = $idField[0];
                array_unshift($fields, $idField);


                $where = array();
                foreach ($fields as $field) {
                    $where[] = $db->quoteName($field) . ' RLIKE ' . $db->quote('/?courses/(categories|class|session)/');
                }
                $query = $db->getQuery(true)
                    ->select($fields)
                    ->from($table)
                    ->where($where, 'OR');

                if ($rows = $db->setQuery($query)->loadAssocList()) {
                    echo '<br/>FIXING: ' . $table . ' - ' . count($rows) . ' Rows';
                    foreach ($rows as $row) {
                        foreach ($row as $fieldName => $value) {
                            if ($fieldName != $idField && $value) {
                                $row[$fieldName] = preg_replace('#/?(courses/)(categories|class|session)/#ms', '/classes/',
                                    $value);
                            }
                            $update = (object)$row;

                            $db->updateObject($table, $update, $idField);
                            if ($error = $db->getErrorMsg()) {
                                echo 'ERROR: ' . $error;
                                return;
                            }
                        }
                    }

                }
            }
        }

        echo '<p>Total Runtime: ' . number_format((microtime(true) - $start), 1) . '</p>';
    }

    /**
     * Check for duplicate lesson aliases within a course. Since we don't include the lesson ID
     * in SEF URLs (and we don't want to do that for SEO!) lessons require unique aliases within
     * a course.
     */
    public function aliasdups()
    {
        $db = OscampusFactory::getDbo();

        $query = $db->getQuery(true)
            ->select(
                array(
                    'lesson.alias',
                    'course.title AS course_title',
                    'count(*) AS count'
                )
            )
            ->from('#__oscampus_lessons AS lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->group('lesson.alias, course.id')
            ->having('count > 1');

        if ($aliasDuplicates = $db->setQuery($query)->loadObjectList()) {
            echo '<p>The following lessons have duplicated aliases within the same course. There will be problems viewing these lessons</p>';

            echo '<ol>';
            foreach ($aliasDuplicates as $duplicate) {
                echo '<li>' . $duplicate->course_title . '/'. $duplicate->alias . ' (' . $duplicate->count . ')</li>';
            }
            echo '</ol>';
        } else {
            echo '<h3>YAY! No duplicate aliases were found!</h3>';
        }
    }
}
