<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

require_once __DIR__ . '/pathway.php';

class OscampusModelNewcourses extends OscampusModelPathway
{
    protected function getListQuery()
    {
        /**
         * @var JDate $cutoff
         */

        $db         = $this->getDbo();
        $viewLevels = JFactory::getUser()->getAuthorisedViewLevels();
        $cutoff     = $this->getState('cutoff');

        $query = $db->getQuery(true)
            ->select('u.name teacher, c.*')
            ->from('#__oscampus_courses c')
            ->leftJoin('#__oscampus_teachers i ON i.id = c.teachers_id')
            ->leftJoin('#__users u ON u.id = i.users_id')
            ->where(
                array(
                    'c.published = 1',
                    'c.access IN (' . join(',', $viewLevels) . ')',
                    'c.released >= ' . $db->quote($cutoff->toSql())
                )
            )
            ->order('c.released desc');

        return $query;
    }

    /**
     * Get the current pathway information
     *
     * @return object
     */
    public function getPathway()
    {
        return null;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $params = OscampusFactory::getApplication()->getParams();

        $releasePeriod = $params->get('releasePeriod', '1 month');
        $cutoff        = OscampusFactory::getDate('now - ' . $releasePeriod);
        $this->setState('cutoff', $cutoff);
    }
}
