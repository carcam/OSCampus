<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewLessons extends OscampusViewList
{
    protected function setup()
    {
        parent::setup();

        $ordering = $this->getState()->get('list.ordering');

        $this->setOrdering('lesson.ordering', 'lessons', $ordering == 'lesson.ordering');
    }

    public function getSortGroupId($item)
    {
        return $item->modules_id;
    }
}
