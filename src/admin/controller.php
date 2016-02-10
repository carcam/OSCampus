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
     * Task for seeking and changing text in any text blob in the database
     */
    protected function textUpdate()
    {
        // The text we're looking for and how to do the replacement
        $regex   = '{info.*{/info}';
        $replace = array(
            'regex'   => '#{info.*{/info}#ms',
            'replace' => '{oscampus videos}'
        );

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
                $query  = "SHOW COLUMNS FROM {$table} WHERE type = 'text'";
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

                            if (true) {
                                // Apply changes
                                foreach ($row as $fieldName => $value) {
                                    // apply fix
                                    if ($fieldName != $idField && $value) {
                                        $row[$fieldName] = preg_replace($replace['regex'], $replace['replace'], $value);
                                    }
                                }
                                $update = (object)$row;

                                $db->updateObject($table, $update, $idField);
                                if ($error = $db->getErrorMsg()) {
                                    echo 'ERROR: ' . $error;
                                    return;
                                }
                            }
                        }
                        $html[] = '</ul></li>';
                    }
                }
            }
        }

        $html[] = '</ul>';

        echo join("\n", $html);
        echo '<p>Total Runtime: ' . number_format((microtime(true) - $start), 1) . '</p>';

        error_reporting(0);
        ini_set('display_errors', 0);

    }

    /**
     * Check for duplicate lesson aliases within a course. Since we don't include the lesson ID
     * in SEF URLs (and we don't want to do that for SEO!) lessons require unique aliases within
     * a course.
     */
    protected function aliasdups()
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
                echo '<li>' . $duplicate->course_title . '/' . $duplicate->alias . ' (' . $duplicate->count . ')</li>';
            }
            echo '</ol>';
        } else {
            echo '<h3>YAY! No duplicate aliases were found!</h3>';
        }
    }
}
