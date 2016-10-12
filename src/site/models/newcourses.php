<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die();

JLoader::import('courselist', __DIR__);

class OscampusModelNewcourses extends OscampusModelCourselist
{
    protected function getListQuery()
    {
        /**
         * @var JDate $cutoff
         */

        $db     = $this->getDbo();
        $cutoff = $this->getState('filter.cutoff');

        $query = parent::getListQuery();

        $query->where('course.released >= ' . $db->quote($cutoff->toSql()));

        $ordering = $this->getState('list.ordering');
        $direction = $this->getState('list.direction');
        $query->order($ordering . ' ' . $direction);

        return $query;
    }

    protected function populateState($ordering = 'course.released', $direction = 'DESC')
    {
        $app = OscampusFactory::getApplication();

        if (method_exists($app, 'getParams')) {
            $params = $app->getParams();
        } else {
            $params = new Registry();
        }

        $releasePeriod = $params->get('releasePeriod', '1 month');
        $cutoff        = OscampusFactory::getDate('now - ' . $releasePeriod);
        $this->setState('filter.cutoff', $cutoff);

        parent::populateState($ordering, $direction);

        // Ignore pagination for now
        $this->setState('list.start', 0);
        $this->setState('list.limit');
    }
}
