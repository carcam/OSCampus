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

    /**
     * Teacher information for currently selected course
     *
     * @return object
     */
    public function getTeacher()
    {
        $db  = JFactory::getDbo();
        $cid = (int)$this->getState('course.id');

        $query = $db->getQuery(true)
            ->select('i.*, u.username, u.name, u.email')
            ->from('#__oscampus_teachers i')
            ->innerJoin('#__oscampus_courses c ON c.teachers_id = i.id')
            ->leftJoin(('#__users u ON u.id = i.users_id'))
            ->where('c.id = ' . $cid);
        if ($teacher = $db->setQuery($query)->loadObject()) {
            $teacher->links = json_decode($teacher->links);

            // Get other courses for this teacher
            $queryCourses = $db->getQuery(true)
                ->select('p.title pathway_title, c.*')
                ->from('#__oscampus_teachers t')
                ->innerJoin('#__oscampus_courses c ON c.teachers_id = t.id')
                ->innerJoin('#__oscampus_courses_pathways cp ON cp.courses_id = c.id')
                ->innerJoin('#__oscampus_pathways p ON p.id = cp.pathways_id')
                ->where(
                    array(
                        'c.id != ' . $cid,
                        't.id = ' . $teacher->id
                    )
                )
                ->order('p.ordering ASC, cp.ordering ASC, c.title ASC');

            $teacher->courses = $db->setQuery($queryCourses)->loadObjectList();
        }

        return $teacher;
    }

    /**
     * Get lessons for the currently selected course
     *
     * @return array
     */
    public function getLessons()
    {
        $db      = $this->getDbo();
        $cid     = (int)$this->getState('course.id');
        $lessons = array();

        if ($cid > 0) {
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

            $list = $db->setQuery($query)->loadObjectList();

            $module = (object)array(
                'id'      => null,
                'title'   => null,
                'lessons' => array()
            );
            foreach ($list as $lesson) {
                if ($lesson->modules_id != $module->id) {
                    if ($module->lessons) {
                        $lessons[] = clone $module;
                    }
                    $module->id      = $lesson->modules_id;
                    $module->title   = $lesson->module_title;
                    $module->lessons = array();
                }
                $module->lessons[] = $lesson;
            }
            if ($module->lessons) {
                $lessons[] = clone $module;
            }
        }

        return $lessons;
    }

    /**
     * Get lesson viewed info for the selected user in the currently selected course
     *
     * @return array
     */
    public function getViewedLessons()
    {
        $uid = (int)$this->getState('user.id');
        $cid = (int)$this->getState('course.id');

        if ($uid > 0 && $cid > 0) {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('ul.*')
                ->from('#__oscampus_users_lessons ul')
                ->innerJoin('#__oscampus_lessons l ON l.id = ul.lessons_id')
                ->innerJoin('#__oscampus_modules m ON m.id = l.modules_id')
                ->innerJoin('#__oscampus_courses c ON c.id = m.courses_id')
                ->where(
                    array(
                        'c.id = ' . $cid,
                        'ul.users_id = ' . $uid
                    )
                );

            $viewed = $db->setQuery($query)->loadObjectList('lessons_id');
            return $viewed;
        }

        return array();
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        $cid = $app->input->getInt('cid');
        $this->setState('course.id', $cid);

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);
    }
}
