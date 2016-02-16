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

        $query = parent::getListQuery()
            ->select(
                array(
                    'course.id',
                    'course.title',
                    'max(ul.last_visit) AS last_lesson'
                )
            )
            ->from('#__oscampus_users_lessons ul')
            ->innerJoin('#__oscampus_lessons lesson ON lesson.id = ul.lessons_id')
            ->innerJoin('#__oscampus_modules module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses course ON course.id = module.courses_id')
            ->where(
                array(
                    'ul.users_id = ' . $user->id,
                )
            )
            ->group('course.id');

        $ordering = $this->getState('list.ordering', 'course.title');
        $direction = $this->getState('list.direction', 'ASC');
        $query->order($ordering . ' ' . $direction);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $item) {
            if (!$item->last_lesson instanceof DateTime) {
                $item->last_lesson = OscampusFactory::getDate($item->last_lesson);
            }
        }

        return $items;
    }

    protected function populateState($ordering = 'course.title', $direction = 'ASC')
    {
        $this->setState('list.limit', 0);
        $this->setState('list.start', 0);
    }
}
