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
