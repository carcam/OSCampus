<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelLesson extends OscampusModelAdmin
{
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('module.courses_id')
                ->from('#__oscampus_modules AS module')
                ->where('module.id = ' . $item->modules_id);

            $item->courses_id = $db->setQuery($query)->loadResult();
        } else {
            $item->courses_id = null;
        }

        return $item;
    }

    protected function getReorderConditions($table)
    {
        $conditions = array(
            'modules_id = ' . (int)$table->modules_id
        );

        return $conditions;
    }
}
