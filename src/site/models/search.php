<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JLoader::import('courselist', __DIR__);

class OscampusModelSearch extends OscampusModelCourselist
{

    public function getItems()
    {
        $query = $this->getCourseQuery();

        $ordering = $this->getState('list.ordering');
        $direction = $this->getState('list.direction');

        $query->order($ordering . ' ' . $direction);

        $start = $this->getState('list.start');
        $limit = $this->getState('list.limit');

        $items = $this->getDbo()->setQuery($query, $start, $limit)->loadObjectList();

        return $items;
    }

    protected function getCourseQuery()
    {
        // We're leveraging the courselist model to get this query
        return parent::getListQuery();
    }

    protected function getPathwayQuery()
    {

    }

    protected function getLessonQuery()
    {

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

    protected function populateState($ordering = 'course.title', $direction = 'ASC')
    {
        $app = JFactory::getApplication();

        // Display result types
        $types = $this->getUserStateFromRequest($this->context . '.types', 'types', array(), 'array');
        $this->setState('show.types', $types);

        // Text search filter
        $minLength = 2;
        $text = $app->input->getString('text');
        if ($text && strlen($text) < $minLength) {
            $app->enqueueMessage(JText::sprintf('COM_OSCAMPUS_WARNING_SEARCH_MINTEXT', $minLength), 'notice');
            $text = $app->getUserState($this->context . '.filter.text', '');
        } else {
            $text = $this->getUserStateFromRequest($this->context . '.filter.text', 'text', null, 'string');
        }
        $this->setState('filter.text', $text);

        // Tag filter
        $tagId = $this->getUserStateFromRequest($this->context . '.filter.tag', 'tag', null, 'int');
        $this->setState('filter.tag', $tagId);

        // Teacher filter
        $teacherId = $this->getUserStateFromRequest($this->context . '.filter.teacher', 'teacher', null, 'int');
        $this->setState('filter.teacher', $teacherId);

        // Course difficulty filter
        $difficulty = $this->getUserStateFromRequest(
            $this->context . '.filter.difficulty',
            'difficulty',
            null,
            'cmd'
        );
        $this->setState('filter.difficulty', $difficulty);

        // User progress filter
        $progress = $this->getUserStateFromRequest(
            $this->context . '.filter.progress',
            'progress',
            null,
            'cmd'
        );
        $this->setState('filter.progress', $progress);

        parent::populateState($ordering, $direction);

        // Ignore pagination for now
        $this->setState('list.start', 0);
        $this->setState('list.limit');
    }
}
