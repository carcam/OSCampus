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

        $state = $this->getState();

        $ordering = array_merge(
            $this->getVariable('ordering', array()),
            array(
                'field'   => 'lesson.ordering',
                'prefix'  => 'lessons.',
                'enabled' => $this->enableOrdering()
            )
        );
        $this->setVariable('ordering', $ordering);


        $courseOptions = JHtml::_('osc.options.courses');
        array_unshift($courseOptions, JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_COURSE')));
        $courses = JHtml::_(
            'select.genericlist',
            $courseOptions,
            'filter_course',
            null,
            'value',
            'text',
            $state->get('filter.course')
        );

        $published = JHtml::_(
            'osc.select.published',
            'filter_published',
            $state->get('filter.published'),
            'COM_OSCAMPUS_OPTION_SELECT_PUBLISHED'
        );

        $types = JHtml::_(
            'osc.select.lessontype',
            'filter_lessontype',
            $state->get('filter.lessontype'),
            'COM_OSCAMPUS_OPTION_SELECT_LESSONTYPE'
        );

        $access = JHtml::_(
            'osc.select.access',
            'filter_access',
            $state->get('filter.access'),
            'COM_OSCAMPUS_OPTION_SELECT_ACCESS'
        );

        $filters = array(
            'text'  => array(
                'value' => $state->get('filter.search')
            ),
            'items' => array(
                array($published, $courses, $types, $access)
            )
        );

        $this->setVariable('filters', $filters);
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
