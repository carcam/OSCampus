<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();


class OscampusModelPathways extends OscampusModelSiteList
{
    protected $filter_fields = array(
        'owner',
        'pathways',
        'tag',
        'text'
    );

    protected function getListQuery()
    {
        $user = $this->getState('user');

        $query = $this->getDbo()->getQuery(true)
            ->select('*')
            ->from('#__oscampus_pathways AS pathway')
            ->where(
                array(
                    'pathway.published = 1',
                    $this->whereAccess('pathway.access', $user)
                )
            );

        // Various ways the pathways can be selected
        if ($pathways = $this->getState('filter.pathways')) {
            $pathways = array_filter(
                array_map('intval', (array)$pathways)
            );
            $query->where(sprintf('pathway.id IN (%s)', join(',', $pathways)));

        } elseif ($pathwayOwner = (int)$this->getState('filter.owner')) {
            $query->where('pathway.users_id = ' . $pathwayOwner);

        } else {
            $query->where('pathway.users_id = 0');
        }

        if ($tagId = (int)$this->getState('filter.tag')) {
            $subQuery = $this->getDbo()->getQuery(true)
                ->select('cp.pathways_id')
                ->from('#__oscampus_courses_tags AS ct')
                ->innerJoin('#__oscampus_courses AS course ON course.id = ct.courses_id')
                ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.courses_id = course.id')
                ->where('ct.tags_id = ' . $tagId)
                ->group('cp.pathways_id');

            $query->where(sprintf('pathway.id IN (%s)', $subQuery));
        }

        if ($text = $this->getState('filter.text')) {
            $fields = array(
                'pathway.title',
                'pathway.description'
            );
            $query->where($this->whereTextSearch($text, $fields));
        }

        $ordering  = $this->getState('list.ordering');
        $direction = $this->getState('list.direction');
        $query->order($ordering . ' ' . $direction);
        if ($ordering != 'pathway.title') {
            $query->order('pathway.title ' . $direction);
        }

        return $query;
    }

    protected function populateState($ordering = 'pathway.ordering', $direction = 'ASC')
    {
        $this->setState('user', OscampusFactory::getUser());

        parent::populateState($ordering, $direction);
    }
}
