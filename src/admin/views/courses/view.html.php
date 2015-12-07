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

        $published = JHtml::_(
            'osc.select.published',
            'filter_published',
            $this->state->get('filter.published'),
            'COM_OSCAMPUS_OPTION_SELECT_PUBLISHED'
        );

        $pathway = JHtml::_(
            'osc.select.pathway',
            'filter_pathway',
            $this->state->get('filter.pathway'),
            'COM_OSCAMPUS_OPTION_SELECT_PATHWAY'
        );

        $tag = JHtml::_(
            'osc.select.tag',
            'filter_tag',
            $this->state->get('filter.tag'),
            array(
                ''     => 'COM_OSCAMPUS_OPTION_SELECT_TAG',
                'null' => 'COM_OSCAMPUS_OPTION_NOT_TAGGED'
            )
        );

        $difficulty = JHtml::_(
            'osc.select.difficulty',
            'filter_difficulty',
            $this->state->get('filter.difficulty'),
            'COM_OSCAMPUS_OPTION_SELECT_DIFFICULTY'
        );

        $filters = array(
            'text'  => array(
                'value' => $this->state->get('filter.search')
            ),
            'items' => array(
                array($published, $pathway, $tag, $difficulty)
            )
        );

        $this->setVariable('filters', $filters);
    }

}
