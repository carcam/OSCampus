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

class OscampusModelPathway extends OscampusModelCourselist
{
    protected function getListQuery()
    {
        $user  = $this->getState('user');
        $query = parent::getListQuery();

        // Set pathway selection
        if ($pathwayId = (int)$this->getState('pathway.id')) {
            $query
                ->select('MIN(cp.pathways_id) AS pathways_id')
                ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                ->innerJoin('#__oscampus_pathways AS pathway ON pathway.id = cp.pathways_id')
                ->where(
                    array(
                        'pathway.published = 1',
                        $this->whereAccess('pathway.access', $user),
                        'pathway.id = ' . $pathwayId
                    )
                );
        } else {
            $query->select('0 AS pathways_id');
        }

        $ordering  = $this->getState('list.ordering');
        $direction = $this->getState('list.direction');
        $query->order($ordering . ' ' . $direction);

        return $query;
    }

    /**
     * Get the current pathway information. Note that this only
     * makes sense if a pathway is selected
     *
     * @return object
     */
    public function getPathway()
    {
        $pathway = $this->getState('pathway');
        if ($pathway === null) {
            if ($pathwayId = (int)$this->getState('pathway.id')) {
                $db = $this->getDbo();

                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__oscampus_pathways')
                    ->where('id = ' . $pathwayId);

                $pathway = $db->setQuery($query)->loadObject();

                $pathway->metadata = new Registry($pathway->metadata);

                $this->setState('pathway', $pathway);
            }
        }
        return $pathway;
    }

    protected function populateState($ordering = 'cp.ordering', $direction = 'ASC')
    {
        $app       = OscampusFactory::getApplication();
        $pathwayId = $app->input->getInt('pid', 0);
        $this->setState('pathway.id', $pathwayId);

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ($id ? '' : ':') . $this->getState('pathway.id');

        return parent::getStoreId($id);
    }
}
