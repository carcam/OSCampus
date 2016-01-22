<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use OscampusFactory;

defined('_JEXEC') or die();

class OscampusModelMycertificates extends OscampusModelList
{
    public function getListQuery()
    {
        $user   = OscampusFactory::getUser($this->getState('user.id'));
        $levels = join(',', $user->getAuthorisedViewLevels());

        $query = parent::getListQuery()
            ->select(
                array(
                    'certificate.*',
                    'MIN(cp.pathways_id) AS pathways_id',
                    'course.difficulty',
                    'course.length',
                    'course.title',
                    'course.image'
                )
            )
            ->from('#__oscampus_certificates AS certificate')
            ->innerJoin('#__oscampus_courses AS course ON course.id = certificate.courses_id')
            ->innerJoin('#__oscampus_courses_pathways cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways pathway ON pathway.id = cp.pathways_id')
            ->where(
                array(
                    'certificate.users_id = ' . $user->id,
                    'pathway.access IN (' . $levels . ')'
                )
            )
            ->group('course.id')
            ->order('course.title ASC');

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $item) {
            if (!$item->date_earned instanceof DateTime) {
                $item->date_earned = OscampusFactory::getDate($item->date_earned);
            }
        }

        return $items;
    }
}
