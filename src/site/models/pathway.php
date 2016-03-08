<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Course;

defined('_JEXEC') or die();

class OscampusModelPathway extends OscampusModelSiteList
{
    protected function getListQuery()
    {
        $db         = $this->getDbo();
        $user       = OscampusFactory::getUser();
        $viewLevels = join(',', $user->getAuthorisedViewLevels());

        $tags  = sprintf(
            'GROUP_CONCAT(tag.title ORDER BY tag.title ASC SEPARATOR %s) AS tags',
            $db->quote(', ')
        );
        $query = $db->getQuery(true)
            ->select(
                array(
                    'cp.*',
                    'pathway.title AS pathway',
                    'user.name AS teacher',
                    $tags,
                    'course.*'
                )
            )
            ->from('#__oscampus_pathways AS pathway')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = cp.courses_id')
            ->leftJoin('#__oscampus_teachers AS teacher ON teacher.id = course.teachers_id')
            ->leftJoin('#__oscampus_courses_tags AS ct ON ct.courses_id = course.id')
            ->leftJoin('#__oscampus_tags AS tag ON tag.id = ct.tags_id')
            ->leftJoin('#__users AS user ON user.id = teacher.users_id')
            ->where(
                array(
                    'pathway.id = ' . $this->getState('pathway.id'),
                    'pathway.published = 1',
                    'course.published = 1',
                    'course.access IN (' . $viewLevels . ')',
                    'course.released <= NOW()'
                )
            )
            ->group('course.id');

        $order     = $this->getState('list.order', 'cp.ordering');
        $direction = $this->getState('list.direction', 'ASC');
        $query->order($order . ' ' . $direction . ', course.title ' . $direction);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        $tbd = JText::_('COM_OSCAMPUS_TEACHER_UNKNOWN');
        foreach ($items as $idx => $item) {
            if (!$item->teacher) {
                $item->teacher = $tbd;
            }
        }

        return $items;
    }

    /**
     * Get the current pathway information
     *
     * @return object
     */
    public function getPathway()
    {
        $pathway = $this->getState('pathway');
        if ($pathway === null) {
            if ($pid = (int)$this->getState('pathway.id')) {
                $db      = $this->getDbo();
                $pathway = $db->setQuery('Select * From #__oscampus_pathways Where id = ' . $pid)->loadObject();

                $pathway->metadata = new JRegistry($pathway->metadata);

                $this->setState('pathway', $pathway);
            }
        }
        return $pathway;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        $pathwayId = $app->input->getInt('pid');
        $this->setState('pathway.id', $pathwayId);
    }
}
