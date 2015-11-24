<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelCourse extends OscampusModelAdmin
{
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if (!$item->id) {
            $item->released = date('Y-m-d');
            $item->access = 1;
            $item->published = 1;

            $defaultImage = JHtml::_('image', 'com_oscampus/default-course.jpg', null, null, true, true);
            $item->image = ltrim($defaultImage, '/');
            $item->pathways = array();

        } else {
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('pathways_id')
                ->from('#__oscampus_courses_pathways')
                ->where('courses_id = ' . $item->id);

            $item->pathways = $db->setQuery($query)->loadColumn();
        }

        return $item;
    }
}
