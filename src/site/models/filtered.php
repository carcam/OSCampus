<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\Registry\Registry as Registry;
use Oscampus\Activity\CourseStatus;

defined('_JEXEC') or die();

abstract class OscampusModelFiltered extends OscampusModelSiteList
{
    protected $filter_fields = array(
        'ordering', 'cp.ordering',
        'released', 'course.released'
    );

    /**
     * @var CourseStatus[]
     */
    protected $activity = null;

    public function __construct(array $config)
    {
        parent::__construct($config);

        // All subclasses will share the same context
        $this->context = 'com_oscampus.filtered';
    }

    /**
     * Get user's course activity
     *
     * @return CourseStatus[]
     */
    protected function getUserActivity()
    {
        if ($this->activity === null) {
            /** @var OscampusModelMycourses $model */
            $model = OscampusModel::getInstance('Mycourses');

            $this->activity = $model->getItems();
        }

        return $this->activity;
    }

    /**
     * Get the current pathway information. Note that this only
     * makes sense if a pathway filter has been supplied
     *
     * @return object
     */
    public function getPathway()
    {
        $pathway = $this->getState('pathway');
        if ($pathway === null) {
            if ($pathwayId = (int)$this->getState('filter.pathway')) {
                $db = $this->getDbo();

                $db->setQuery('Select * From #__oscampus_pathways Where id = ' . $pathwayId);
                $pathway = $db->loadObject();

                $pathway->metadata = new Registry($pathway->metadata);

                $this->setState('pathway', $pathway);
            }
        }
        return $pathway;
    }

    /**
     * Custom method to allow overriding by subclasses
     */
    protected function setFilters()
    {
        $app = JFactory::getApplication();

        $topic = $this->getUserStateFromRequest($this->context . '.filter.topic', 'filter_topic', null, 'int');
        $this->setState('filter.topic', $topic);

        $text = $app->input->getString('filter_text');
        if ($text && strlen($text) < 3) {
            $app->enqueueMessage(JText::_('COM_OSCAMPUS_WARNING_SEARCH_MINTEXT'), 'notice');
            $text = $app->getUserState($this->context . '.filter.text', '');
        } else {
            $text = $this->getUserStateFromRequest($this->context . '.filter.text', 'filter_text', null, 'string');
        }
        $this->setState('filter.text', $text);

        $tagId = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', null, 'int');
        $this->setState('filter.tag', $tagId);

        $teacherId = $this->getUserStateFromRequest($this->context . '.filter_teacher', 'filter_teacher', null, 'int');
        $this->setState('filter.teacher', $teacherId);

        $difficulty = $this->getUserStateFromRequest(
            $this->context . '.filter.difficulty',
            'filter_difficulty',
            null,
            'cmd'
        );
        $this->setState('filter.difficulty', $difficulty);

        $completion = $this->getUserStateFromRequest(
            $this->context . '.filter.completion',
            'filter_completion',
            null,
            'cmd'
        );
        $this->setState('filter.completion', $completion);
    }

    /**
     * Determines if any filters are currently in play.
     *
     * @return bool
     */
    public function activeFilters()
    {
        $states = $this->getState()->getProperties();
        foreach ($states as $name => $state) {
            if (strpos($name, 'filter.') === 0 && $state != '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Although we want all subclasses to inherit the same collection
     * of filters, we also want a regular way to extend or override
     * the standard filters and maintain separate ordering and list limit
     * for the subclasses.
     *
     * @param string $ordering
     * @param string $direction
     *
     * @return void
     */
    protected function populateState($ordering = 'cp.ordering', $direction = 'ASC')
    {
        $this->setFilters();

        $context = $this->context;
        $this->context = $this->option . '.' . $this->getName();

        parent::populateState($ordering, $direction);

        $this->context = $context;
    }
}
