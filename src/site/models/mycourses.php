<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelMycourses extends OscampusModelList
{
    public function getListQuery()
    {
        $user   = OscampusFactory::getUser($this->getState('user.id'));
        $levels = join(',', $user->getAuthorisedViewLevels());

        $query = parent::getListQuery()
            ->select(
                array(
                    'course.id',
                    'course.title',
                    'min(pathway.id) AS pathways_id',
                    'max(ul.last_visit) AS last_lesson'
                )
            )
            ->from('#__oscampus_users_lessons ul')
            ->innerJoin('#__oscampus_lessons lesson ON lesson.id = ul.lessons_id')
            ->innerJoin('#__oscampus_modules module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses course ON course.id = module.courses_id')
            ->innerJoin('#__oscampus_courses_pathways cp ON cp.courses_id = course.id')
            ->innerJoin('#__oscampus_pathways pathway ON pathway.id = cp.pathways_id')
            ->where(
                array(
                    'ul.users_id = ' . $user->id,
                    'pathway.access IN (' . $levels . ')',
                    'pathway.published = 1'
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
            if (!$item->last_lesson instanceof DateTime) {
                $item->last_lesson = new DateTime($item->last_lesson);
            }
        }

        return $items;
    }
}