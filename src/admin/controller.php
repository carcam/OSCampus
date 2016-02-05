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
}
