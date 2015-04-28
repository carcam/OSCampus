<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelCourse extends OscampusModelSite
{
    public function getCourse()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__oscampus_courses c')
            ->where('c.id = ' . (int)$this->getState('course.id'));

        $course = $db->setQuery($query)->loadObject();

        return $course;
    }

    public function getTeacher()
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('i.*, u.username, u.name, u.email')
            ->from('#__oscampus_teachers i')
            ->innerJoin('#__oscampus_courses c ON c.teachers_id = i.id')
            ->leftJoin(('#__users u ON u.id = i.users_id'))
            ->where('c.id = ' . (int)$this->getState('course.id'));

        $teacher = $db->setQuery($query)->loadObject();

        $teacher->parameters = new JRegistry($teacher->parameters);
        $teacher->links = array();

        $showLinks = $teacher->parameters->get('show');
        foreach ($showLinks as $linkName => $show) {
            $link = $teacher->parameters->get($linkName);
            if ($show && $link) {
                $teacher->links[$linkName] = $link;
            }
        }

        return $teacher;
    }

    public function getLessons()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('m.courses_id, l.modules_id, m.title module_title, l.*')
            ->from('#__oscampus_modules m')
            ->innerJoin('#__oscampus_lessons l ON l.modules_id = m.id')
            ->where(
                array(
                    'm.courses_id = ' . (int)$this->getState('course.id'),
                    'm.published = 1',
                    'l.published = 1'
                )
            )
            ->order('m.ordering ASC, l.ordering ASC');

        $lessons = $db->setQuery($query)->loadObjectList();

        return $lessons;
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        $cid = $app->input->getInt('cid');
        $this->setState('course.id', $cid);

    }
}
