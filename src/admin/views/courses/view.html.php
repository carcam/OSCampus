<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewCourses extends OscampusViewList
{
    protected function setup()
    {
        parent::setup();

        $state = $this->getState();

        $enableOrdering = $state->get('list.ordering') == 'cp.ordering' && $state->get('filter.pathway') > 0;
        $this->setOrdering('cp.ordering', 'courses.', $enableOrdering);

        $published = JHtml::_(
            'osc.select.published',
            'filter_published',
            $state->get('filter.published'),
            'COM_OSCAMPUS_OPTION_SELECT_PUBLISHED'
        );

        $pathway = JHtml::_(
            'osc.select.pathway',
            'filter_pathway',
            $state->get('filter.pathway'),
            'COM_OSCAMPUS_OPTION_SELECT_PATHWAY'
        );

        $tag = JHtml::_(
            'osc.select.tag',
            'filter_tag',
            $state->get('filter.tag'),
            array(
                ''     => 'COM_OSCAMPUS_OPTION_SELECT_TAG',
                'null' => 'COM_OSCAMPUS_OPTION_NOT_TAGGED'
            )
        );

        $difficulty = JHtml::_(
            'osc.select.difficulty',
            'filter_difficulty',
            $state->get('filter.difficulty'),
            'COM_OSCAMPUS_OPTION_SELECT_DIFFICULTY'
        );

        $access = JHtml::_(
            'osc.select.access',
            'filter_access',
            $state->get('filter.access'),
            'COM_OSCAMPUS_OPTION_SELECT_ACCESS'
        );

        $teacher = JHtml::_(
            'osc.select.teacher',
            'filter_teacher',
            $state->get('filter.teacher'),
            'COM_OSCAMPUS_OPTION_SELECT_TEACHER'
        );

        $filters = array(
            'text'  => array(
                'value' => $state->get('filter.search')
            ),
            'items' => array(
                array($published, $tag, $pathway),
                array($difficulty, $teacher, $access)
            )
        );

        $this->setVariable('filters', $filters);
    }
}
