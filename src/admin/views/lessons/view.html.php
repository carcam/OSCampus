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

        $this->setOrdering('lesson.ordering', 'lessons', $this->enableOrdering());
    }

    /**
     * Determine whether it's okay to enable ordering
     *
     * @return bool
     */
    protected function enableOrdering()
    {
        $state = $this->getState();

        $enabled = false;
        if ($state->get('list.ordering') == 'lesson.ordering') {
            $enabled = true;
            $stateVars = $state->getProperties();
            foreach ($stateVars as $name => $value) {
                if ($value && strpos($name, 'filter.') === 0 && $name != 'filter.course') {
                    return false;
                }
            }
        }

        return $enabled;
    }

    public function getSortGroupId($item)
    {
        return $item->modules_id;
    }
}
