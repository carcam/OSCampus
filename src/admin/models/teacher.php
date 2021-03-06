<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();


class OscampusModelTeacher extends OscampusModelAdmin
{
    protected function loadFormData()
    {
        $data = parent::loadFormData();

        $data->links = json_decode($data->links);

        return $data;
    }

    /**
     * @param JTable $table
     *
     * @return void
     */
    protected function prepareTable($table)
    {
        if (!empty($table->links) && !is_string($table->links)) {
            $table->links = json_encode($table->links);
        }
    }

    public function validate($form, $data, $group = null)
    {
        $fixedData = $data;
        if (isset($fixedData['links'])) {
            foreach ($fixedData['links'] as $type => $value) {
                $fixedData['links'][$type] = isset($value['link']) ? $value['link'] : '';
            }
        }
        if (parent::validate($form, $fixedData, $group)) {
            return $data;
        }
        return false;
    }
}
