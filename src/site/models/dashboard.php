<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusModelDashboard extends OscampusModelSite
{
    /**
     * @var array
     */
    protected $courses = null;

    /**
     * @var array
     */
    protected $certificates = null;

    public function getCourses()
    {
        if ($this->courses === null) {
            $uid = (int)$this->getState('user.id');

            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('c.id, c.title, max(ul.last_visit)')
                ->from('#__oscampus_users_lessons ul')
                ->innerJoin('#__oscampus_lessons l ON l.id = ul.lessons_id')
                ->innerJoin('#__oscampus_modules m ON m.id = l.modules_id')
                ->innerJoin('#__oscampus_courses c ON c.id = m.courses_id')
                ->where('ul.users_id = ' . $uid)
                ->group('c.id, c.title')
                ->order('ul.last_visit desc');

            $this->courses = $db->setQuery($query)->loadObjectList();
        }
        return $this->courses;
    }

    public function getCertificates()
    {
        if ($this->certificates === null) {
            $uid = (int)$this->getState('user.id');

            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('crt.date_earned, c.*')
                ->from('#__oscampus_certificates crt')
                ->innerJoin('#__oscampus_courses c ON c.id = crt.courses_id')
                ->where('crt.users_id = ' . $uid)
                ->order('crt.date_earned desc');

            $this->certificates = $db->setQuery($query)->loadObjectList();
        }

        return $this->certificates;
    }

    protected function populateState()
    {
        $app = JFactory::getApplication();

        $uid = $app->input->getInt('uid', JFactory::getUser()->id);
        $this->setState('user.id', $uid);

        // @TODO: Turn this into a parameter someplace
        $this->setState('list.size', 5);
    }
}
