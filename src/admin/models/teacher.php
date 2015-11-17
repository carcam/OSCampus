<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelTeacher extends OscampusModelAdmin
{
    protected function loadFormData()
    {
        $data = parent::loadFormData();

        // Convert the value of links to the JRepeatable field
        $links = json_decode($data->links);
        if (is_array($links)) {
            $newValue = new stdClass;
            $newValue->type = array();
            $newValue->link = array();
            $newValue->show = array();

            foreach ($links as $item) {
                $newValue->type[] = $item->type;
                $newValue->link[] = $item->link;
                $newValue->show[] = $item->show;
            }

            $data->links = json_encode($newValue);
        }

        return $data;
    }

    /**
     * @param JTable $table
     *
     * @return void
     */
    protected function prepareTable($table)
    {
        // Convert the value of links to the OSCampus format
        $links = json_decode($table->links);


        if (is_object($links) && isset($links->type)) {
            $newValue = array();

            $totalLinks = count($links->type);
            for ($i = 0; $i < $totalLinks; $i++) {
                $item = new stdClass;
                $item->type = $links->type[$i];
                $item->link = $links->link[$i];
                $item->show = (int)$links->show[$i];

                $newValue[] = $item;
            }

            $table->links = json_encode($newValue);
        }
    }
}