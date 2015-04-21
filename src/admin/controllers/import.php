<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerImport extends OscampusControllerBase
{
    protected $courses  = array();
    protected $pathways = array();

    protected $courseMap = array(
        'id'                     => null,
        'catid'                  => null, // Moving to pathways
        'name'                   => 'title',
        'alias'                  => 'alias',
        'description'            => null,
        'introtext'              => null,
        'image'                  => 'image',
        'emails'                 => null,
        'published'              => 'published',
        'startpublish'           => 'publish_up',
        'endpublish'             => 'publish_down',
        'metatitle'              => null,
        'metakwd'                => null,
        'metadesc'               => null,
        'ordering'               => 'ordering',
        'pre_req'                => null,
        'pre_req_books'          => null,
        'reqmts'                 => null,
        'author'                 => null, // instructors_id
        'level'                  => null, // Do we want to specify levels for courses? (Nick says yes - here only)
        'priceformat'            => null,
        'skip_module'            => null,
        'chb_free_courses'       => null,
        'step_access_courses'    => null,
        'selected_course'        => null,
        'course_type'            => null,
        'lesson_release'         => null,
        'lessons_show'           => null,
        'start_release'          => null,
        'id_final_exam'          => null,
        'certificate_term'       => null,
        'hasquiz'                => null,
        'updated'                => null,
        'certificate_course_msg' => null,
        'avg_certc'              => null,
        'course_length'          => null,
        'cms_type'               => null
    );

    protected $pathwayMap = array(
        'id'          => null,
        'name'        => 'title',
        'alias'       => 'alias',
        'published'   => 'published',
        'description' => 'description',
        'image'       => 'image',
        'ordering'    => 'ordering'
    );

    public function import()
    {
        echo '<p><a href="index.php?option=com_oscampus">Back to main  screen</a></p>';

        $this->loadCourses();

        $db = JFactory::getDbo();
        $courseQuery = $db->getQuery(true)
            ->select('cp.*, c.title Course, p.title Pathway')
            ->from('#__oscampus_courses c')
            ->leftJoin('#__oscampus_courses_pathways cp ON cp.courses_id = c.id')
            ->leftJoin('#__oscampus_pathways p ON p.id = cp.pathways_id')
            ->order('p.title, p.id, c.title');

        $courses = $db->setQuery($courseQuery)->loadObjectList();
        echo '<ol>';
        $lastPath = null;
        foreach ($courses as $course) {
            if ($lastPath != $course->pathways_id) {
                if ($lastPath !== null) {
                    echo '<br/></ol>';
                }
                echo sprintf('%s (%s)', $course->Pathway, $course->pathways_id);
                echo '<ol>';
            }
            echo sprintf('<li>%s (%s)</li>', $course->Course, $course->courses_id);
            $lastPath = $course->pathways_id;
        }
        echo '</ol>';
    }

    /**
     * Import courses/pathways
     */
    protected function loadCourses()
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $this->clearTable('#__oscampus_courses_pathways', false);
        $this->courses  = $this->copyTable('#__guru_program', '#__oscampus_courses', $this->courseMap);
        $this->pathways = $this->copyTable('#__guru_category', '#__oscampus_pathways', $this->pathwayMap);

        $categoryQuery = $dbGuru->getQuery(true)
            ->select('id AS courses_id, catid AS pathways_id')
            ->from('#__guru_program');
        $categories    = $dbGuru->setQuery($categoryQuery)->loadObjectList();
        foreach ($categories as $category) {
            if (isset($this->courses[$category->courses_id]) && isset($this->pathways[$category->pathways_id])) {
                $category->courses_id  = $this->courses[$category->courses_id]->id;
                $category->pathways_id = $this->pathways[$category->pathways_id]->id;
                $dbCampus->insertObject('#__oscampus_courses_pathways', $category);
                if ($error = $dbCampus->getErrorMsg()) {
                    echo $error;
                    return;
                }
            }
        }
    }

    protected function copyTable($from, $to, array $map)
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $this->clearTable($to);

        $guruQuery = $dbGuru->getQuery(true)
            ->select('*')
            ->from($from)
            ->order('id');

        $fields     = array_filter($map);
        $guruData   = $dbGuru->setQuery($guruQuery)->loadAssocList();
        $campusData = array();
        foreach ($guruData as $row) {
            $converted = (object)array(
                'created'          => JFactory::getDate()->toSql(),
                'created_by_alias' => 'Guru Import'
            );
            foreach ($fields as $guruField => $campusField) {
                $converted->$campusField = $row[$guruField];
            }
            $dbCampus->insertObject($to, $converted);
            if ($error = $dbCampus->getErrorMsg()) {
                echo 'ERROR: ' . $error;
                die;

            }
            if ($newId = $dbCampus->insertid()) {
                $converted->id = $newId;
            }
            $campusData[$row['id']] = $converted;
        }

        return $campusData;
    }

    /**
     * @return JDatabase
     */
    protected function getGuruDbo()
    {
        $conf = JFactory::getConfig();

        $options = array(
            'driver'   => $conf->get('dbtype'),
            'host'     => $conf->get('host'),
            'user'     => $conf->get('user'),
            'password' => $conf->get('password'),
            'database' => 'guru',
            'prefix'   => 'hfps_'
        );

        $dbo = JDatabase::getInstance($options);
        return $dbo;
    }

    /**
     * Delete all records from selected table and optionally reset auto_increment
     *
     * @param string $table
     * @param bool   $autoinc
     */
    protected function clearTable($table, $autoinc = true)
    {
        $db = JFactory::getDbo();

        $db->setQuery('Delete From ' . $db->quoteName($table))->execute();
        if ($error = $db->getErrorMsg()) {
            echo $error;
            die;
        }
        if ($autoinc) {
            $db->setQuery('Alter Table ' . $db->quoteName($table) . ' AUTO_INCREMENT=1')->execute();
            if ($error = $db->getErrorMsg()) {
                echo $error;
                die;
            }
        }
    }
}
