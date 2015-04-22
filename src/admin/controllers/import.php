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
    protected $users        = null;
    protected $errors       = array();
    protected $certificates = array();

    protected $courses   = array();
    protected $courseMap = array(
        'id'                     => null,
        'catid'                  => null, // Moved to pathways
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
        'ordering'               => null, // Moved to junction with pathways
        'pre_req'                => null,
        'pre_req_books'          => null,
        'reqmts'                 => null,
        'author'                 => null, // instructors_id
        'level'                  => null,
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

    protected $pathways   = array();
    protected $pathwayMap = array(
        'id'          => null,
        'name'        => 'title',
        'alias'       => 'alias',
        'published'   => 'published',
        'description' => 'description',
        'image'       => 'image',
        'ordering'    => 'ordering'
    );

    protected $instructors   = array();
    protected $instructorMap = array(
        'id'            => null,
        'userid'        => 'users_id',
        'full_bio'      => 'bio',
        'images'        => 'image',
        'emaillink'     => null,
        'website'       => null,
        'blog'          => null,
        'facebook'      => null,
        'twitter'       => null,
        'show_email'    => null,
        'show_website'  => null,
        'show_blog'     => null,
        'show_facebook' => null,
        'show_twitter'  => null,
        'author_title'  => null,
        'ordering'      => null
    );

    protected $modules    = array();
    protected $modulesMap = array(
        'id'           => null,
        'pid'          => null, // Filled in via callback
        'title'        => 'title',
        'alias'        => 'alias',
        'description'  => 'description',
        'image'        => null,
        'published'    => 'published',
        'startpublish' => null,
        'endpublish'   => null,
        'metatitle'    => null,
        'metakwd'      => null,
        'metadesc'     => null,
        'afterfinish'  => null,
        'url'          => null,
        'pagetitle'    => null,
        'pagecontent'  => null,
        'ordering'     => 'ordering',
        'locked'       => null,
        'media_id'     => null,
        'access'       => 'access'
    );

    public function import()
    {
        error_reporting(-1);
        ini_set('display_errors', 1);

        echo '<p><a href="index.php?option=com_oscampus">Back to main  screen</a></p>';

        $this->clearTable('#__oscampus_courses_pathways', false);
        $this->clearTable('#__oscampus_modules');
        $this->clearTable('#__oscampus_certificates');
        $this->clearTable('#__oscampus_courses');
        $this->clearTable('#__oscampus_pathways');
        $this->clearTable('#__oscampus_instructors');

        $this->loadCourses();
        $this->loadInstructors();
        $this->loadModules();
        $this->loadCertificates();

        $this->displayResults();

        error_reporting(0);
        ini_set('display_errors', 0);
    }

    /**
     * Load course modules
     * MUST be run after courses/pathway import
     */
    protected function loadModules()
    {
        $courses = $this->courses;

        $this->modules = $this->copyTable(
            '#__guru_days',
            '#__oscampus_modules',
            $this->modulesMap,
            'id',
            function ($guruData, $convertedData) use ($courses) {
                $oldId = $guruData['pid'];
                if (isset($courses[$oldId])) {
                    $convertedData->courses_id = $courses[$oldId]->id;
                    return true;
                }
                return false;
            }
        );
    }

    /**
     * Load certificates earned by users
     * MUST be run after courses and instructors are loaded
     */
    protected function loadCertificates()
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $users        = $this->getUsers();
        $certificates = $dbGuru->setQuery('Select * From #__guru_mycertificates')->loadObjectList();
        foreach ($certificates as $c) {
            if (isset($this->courses[$c->course_id])
                && isset($users[$c->user_id])
            ) {
                $newCertificate = (object)array(
                    'users_id'    => $c->user_id,
                    'courses_id'  => $this->courses[$c->course_id]->id,
                    'date_earned' => $c->datecertificate
                );
                $dbCampus->insertObject('#__oscampus_certificates', $newCertificate, 'id');
                if ($error = $dbCampus->getErrorMsg()) {
                    $this->errors[] = 'Certificate: ' . $error;
                } else {
                    $newCertificate->id         = $dbCampus->insertid();
                    $this->certificates[$c->id] = $newCertificate;
                }
            } else {
                $this->errors[] = sprintf(
                    'Skipped Certificate: %s (%s/%s/%s)',
                    $c->datecertificate,
                    $c->course_id,
                    $c->author_id,
                    $c->user_id
                );
            }
        }
    }

    /**
     * Import Instructors
     * Must be called after courses are loaded
     */
    protected function loadInstructors()
    {
        $users = $this->getUsers();

        $this->instructors = $this->copyTable(
            '#__guru_authors',
            '#__oscampus_instructors',
            $this->instructorMap,
            'id',
            function ($guruData, $convertedData) use ($users) {
                if (isset($users[$convertedData->users_id])) {
                    $parameters                = array(
                        'website'  => (string)preg_replace(
                            '#^https?://#',
                            '',
                            $guruData['website']
                        ),
                        'blog'     => (string)preg_replace(
                            '#^https?://#',
                            '',
                            $guruData['blog']
                        ),
                        'facebook' => (string)preg_replace(
                            '#^https?://(www\.)?(facebook\.com/)?#',
                            '',
                            $guruData['facebook']
                        ),
                        'twitter'  => (string)preg_replace(
                            '#^https?://(www\.)?(twitter\.com/)?#',
                            '',
                            $guruData['twitter']
                        ),
                        'show'     => array(
                            'website'  => (string)(int)$guruData['show_website'],
                            'email'    => (string)(int)$guruData['show_email'],
                            'blog'     => (string)(int)$guruData['show_blog'],
                            'facebook' => (string)(int)$guruData['show_facebook'],
                            'twitter'  => (string)(int)$guruData['show_twitter']
                        )
                    );
                    $convertedData->parameters = json_encode($parameters);
                    return true;
                }
                return false;
            }
        );
    }

    /**
     * Import courses/pathways
     */
    protected function loadCourses()
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $this->courses  = $this->copyTable('#__guru_program', '#__oscampus_courses', $this->courseMap);
        $this->pathways = $this->copyTable('#__guru_category', '#__oscampus_pathways', $this->pathwayMap);

        $categoryQuery = $dbGuru->getQuery(true)
            ->select('id AS courses_id, catid AS pathways_id, ordering')
            ->from('#__guru_program');
        $categories    = $dbGuru->setQuery($categoryQuery)->loadObjectList();
        foreach ($categories as $category) {
            if (isset($this->courses[$category->courses_id]) && isset($this->pathways[$category->pathways_id])) {
                $category->courses_id  = $this->courses[$category->courses_id]->id;
                $category->pathways_id = $this->pathways[$category->pathways_id]->id;
                $dbCampus->insertObject('#__oscampus_courses_pathways', $category);
                if ($error = $dbCampus->getErrorMsg()) {
                    $this->errors[] = $error;
                }
            }
        }
    }

    /**
     * Copy data from a Guru table to an OSCampus table using a field map.
     * The Guru table MUST contain a single primary key field. The OSCampus
     * table is assumed to contain the created/created_by_alias fields.
     *
     * @param string   $from
     * @param string   $to
     * @param array    $map
     * @param string   $key
     * @param callable $callable
     *
     * @return array
     */
    protected function copyTable($from, $to, array $map, $key = 'id', $callable = null)
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $guruQuery = $dbGuru->getQuery(true)
            ->select('*')
            ->from($from)
            ->order($key);

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

            if (is_callable($callable) ? $callable($row, $converted) : true) {
                $dbCampus->insertObject($to, $converted);
                if ($error = $dbCampus->getErrorMsg()) {
                    $this->errors[] = $error;
                    return array();
                }
                if ($newId = $dbCampus->insertid()) {
                    $converted->id = $newId;
                }
                $campusData[$row[$key]] = $converted;
            }
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

    protected function displayResults()
    {
        $db = JFactory::getDbo();

        echo '<div style="float: left; width:50%">';

        echo '<ul>';
        echo '<li>' . number_format(count($this->pathways)) . ' Pathways</li>';
        echo '<li>' . number_format(count($this->courses)) . ' Courses</li>';
        echo '<li>' . number_format(count($this->modules)) . ' Modules</li>';
        echo '<li>' . number_format(count($this->instructors)) . ' Instructors</li>';
        echo '<li>' . number_format(count($this->certificates)) . ' Certificates</li>';
        echo '</ul>';

        $courseQuery = $db->getQuery(true)
            ->select('cp.*, c.title Course, p.title Pathway, m.title Module, u.username, i.parameters')
            ->from('#__oscampus_courses c')
            ->leftJoin('#__oscampus_courses_pathways cp ON cp.courses_id = c.id')
            ->leftJoin('#__oscampus_pathways p ON p.id = cp.pathways_id')
            ->leftJoin('#__oscampus_instructors i ON i.id = c.instructors_id')
            ->leftJoin('#__users u ON u.id = i.users_id')
            ->leftJoin('#__oscampus_modules m ON m.courses_id = c.id')
            ->order('p.title, p.id, cp.ordering, c.title, c.id, m.ordering');

        $courses    = $db->setQuery($courseQuery)->loadObjectList();
        $lastPath   = null;
        $lastCourse = null;

        echo '<ol>';
        foreach ($courses as $course) {
            if ($lastPath != $course->pathways_id) {
                if ($lastCourse !== null) {
                    echo '</ol></li>';
                }
                if ($lastPath !== null) {
                    echo '<hr style="width: 75%;"/></li></ol>';
                }
                echo sprintf('<li>%s<ol>', $course->Pathway);
            }
            if ($lastCourse != $course->courses_id) {
                if ($lastCourse !== null && $lastPath == $course->pathways_id) {
                    echo '<br/></ol></li>';
                }
                echo sprintf('<li>%s (Instructor: %s)<ol>', $course->Course, $course->username);
            }
            echo sprintf('<li>%s</li>', $course->Module);

            $lastPath   = $course->pathways_id;
            $lastCourse = $course->courses_id;
        }
        echo '</ol>';

        echo '</div>';

        if ($this->errors) {
            echo '<div style="float: left;">';
            echo join('<br/>', $this->errors);
            echo '</div>';
        }
    }

    protected function getUsers()
    {
        if ($this->users === null) {
            $this->users = $this->getGuruDbo()
                ->setQuery('Select * From #__users')
                ->loadObjectList('id');
        }
        return $this->users;
    }
}
