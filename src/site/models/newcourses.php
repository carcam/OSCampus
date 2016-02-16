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
        $user       = OscampusFactory::getUser();
        $viewLevels = join(',', $user->getAuthorisedViewLevels());
        $cutoff     = $this->getState('cutoff');

        $query = $db->getQuery(true)
            ->select('user.name AS teacher, course.*, cp.pathways_id')
            ->from('#__oscampus_courses AS course')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
            ->leftJoin('#__oscampus_teachers AS teacher ON teacher.id = course.teachers_id')
            ->leftJoin('#__users user ON user.id = teacher.users_id')
            ->where(
                array(
                    'course.published = 1',
                    'course.access IN (' . $viewLevels . ')',
                    'course.released >= ' . $db->quote($cutoff->toSql()),
                    'pathway.access IN (' . $viewLevels . ')',
                    'pathway.published = 1'
                )
            )
            ->group('course.id')
            ->order('course.released desc');

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
