<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
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
                'enabled' => $state->get('list.ordering') == 'lesson.ordering'
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

        $filters = array(
            'text'  => array(
                'value' => $state->get('filter.search')
            ),
            'items' => array(
                array(
                    $courses,
                    $published
                )
            )
        );

        $this->setVariable('filters', $filters);
    }

    public function getSortGroupId($item)
    {
        return $item->modules_id;
    }
}
