<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusModelMycertificates extends OscampusModelList
{
    public function getListQuery()
    {
        $user = OscampusFactory::getUser($this->getState('user.id'));

        $query = parent::getListQuery()
            ->select(
                array(
                    'certificate.*',
                    'course.difficulty',
                    'course.length',
                    'course.title',
                    'course.image'
                )
            )
            ->from('#__oscampus_certificates AS certificate')
            ->innerJoin('#__oscampus_courses AS course ON course.id = certificate.courses_id')
            ->where(
                array(
                    'certificate.users_id = ' . $user->id,
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
