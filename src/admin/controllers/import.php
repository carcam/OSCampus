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
    protected $images       = array();
    protected $tags         = array();
    protected $viewCount    = 0;
    protected $log          = array();

    protected $groupToView = array(
        1         => 1, // Public group == Public View Level
        2         => 2, // Non-member == Registered VL
        14        => 3, // Video Member == Video, Personal, Pro, Admin VL
        'default' => 3 // Use use most restrictive VL if something goes wrong
    );

    protected $courses   = array();
    protected $courseMap = array(
        'id'                     => null,
        'catid'                  => null, // Moved to pathways
        'name'                   => 'title',
        'alias'                  => 'alias',
        'description'            => 'description', // Will be split into introtext using readmore <br/>
        'introtext'              => null,
        'image'                  => 'image',
        'emails'                 => null,
        'published'              => 'published',
        'startpublish'           => 'released',
        'endpublish'             => null,
        'metatitle'              => null,
        'metakwd'                => null,
        'metadesc'               => null,
        'ordering'               => null, // Moved to junction with pathways
        'pre_req'                => null,
        'pre_req_books'          => null,
        'reqmts'                 => null,
        'author'                 => null, // Must be converted when importing teachers
        'level'                  => 'difficulty',
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
        'course_length'          => 'length',
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

    protected $teachers   = array();
    protected $teacherMap = array(
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
        'description'  => null,
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
        'access'       => null
    );

    protected $lessons    = array();
    protected $lessonsMap = array(
        'id'              => null,
        'modules_id'      => 'modules_id', // Derived Guru field in query
        'name'            => 'title',
        'alias'           => 'alias',
        'category'        => null,
        'difficultylevel' => null,
        'points'          => null,
        'image'           => null,
        'published'       => 'published',
        'startpublish'    => 'created',
        'endpublish'      => null,
        'metatitle'       => null,
        'metakwd'         => null,
        'metadesc'        => null,
        'time'            => null,
        'ordering'        => 'ordering',
        'step_access'     => 'access',
        'final_lesson'    => null
    );

    public function import()
    {
        error_reporting(-1);
        ini_set('display_errors', 1);

        echo '<p><a href="index.php?option=com_oscampus">Back to main  screen</a></p>';
        echo '<p><a href="index.php?option=com_oscampus&task=import.setteacher">Set a demo teacher</a> (when referenced users are not available)</p>';

        $this->log['Start'] = microtime(true);

        $this->clearTable('#__oscampus_courses_pathways', false);
        $this->clearTable('#__oscampus_courses_tags', false);
        $this->clearTable('#__oscampus_users_lessons');
        $this->clearTable('#__oscampus_tags');
        $this->clearTable('#__oscampus_lessons');
        $this->clearTable('#__oscampus_modules');
        $this->clearTable('#__oscampus_certificates');
        $this->clearTable('#__oscampus_courses');
        $this->clearTable('#__oscampus_pathways');
        $this->clearTable('#__oscampus_teachers');
        $this->log['Clear Tables'] = microtime(true);

        $this->loadCourses();
        $this->log['Load Courses'] = microtime(true);

        $this->loadTags();
        $this->log['Load Tags'] = microtime(true);

        $this->loadTeachers();
        $this->log['Load Teachers'] = microtime(true);

        $this->loadModules();
        $this->log['Load Modules'] = microtime(true);

        $this->loadLessons();
        $this->log['Load Lessons'] = microtime(true);

        $this->loadCertificates();
        $this->log['Load Certificates'] = microtime(true);

        $this->loadViewed();
        $this->log['Load Viewed'] = microtime(true);

        $this->images['Teacher Images']  = $this->copyImages('#__oscampus_teachers', 'teachers');
        $this->log['Load Teacher Image'] = microtime(true);

        $this->images['Course Images']   = $this->copyImages('#__oscampus_courses', 'courses');
        $this->log['Load Course Images'] = microtime(true);

        $this->images['Pathway Images']   = $this->copyImages('#__oscampus_pathways', 'pathways');
        $this->log['Load Pathway Images'] = microtime(true);

        $this->displayResults();

        error_reporting(0);
        ini_set('display_errors', 0);
    }

    /**
     * Set all teachers to one available user.
     * This will help a little while we are still in testing
     * and none of the users referenced in the db are on this system
     *
     */
    public function setteacher()
    {
        $db = JFactory::getDbo();

        $db->setQuery(
            'ALTER TABLE ' . $db->quoteName('#__oscampus_teachers')
            . ' DROP INDEX ' . $db->quoteName('idx_users_id')
            . ', ADD INDEX ' . $db->quoteName('idx_users_id')
            . ' (' . $db->quoteName('users_id') . ')'
        )
            ->execute();

        $user = JFactory::getUser(747);
        $db->setQuery(
            'UPDATE ' . $db->quoteName('#__oscampus_teachers')
            . ' SET users_id = ' . $user->id . ' where users_id > 0'
        )
            ->execute();


        $this->setRedirect(
            'index.php?option=com_oscampus',
            sprintf('Set testing/demo teacher to %s &lt;%s&gt;', $user->name, $user->username)
        );
    }

    /**
     * Import Viewed lessons
     * MUST be run after all pathways, courses, modules and lessons
     */
    public function loadViewed()
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $viewedQuery = $dbGuru->getQuery(true)
            ->select('*')
            ->from('#__guru_viewed_lesson')
            ->where(
                array(
                    'pid > 0',
                    'user_id > 0',
                    'lesson_id != ' . $dbGuru->quote('')
                )
            )
            ->order('user_id');

        $keys   = array('users_id', 'lessons_id', 'completed', 'last_visit');
        $offset = 0;
        $limit  = 50;
        while ($items = $dbGuru->setQuery($viewedQuery, $offset, $limit)->loadObjectList()) {
            $data = array();
            foreach ($items as $item) {
                $lessons = explode('||', trim($item->lesson_id, '|'));

                foreach ($lessons as $lesson) {
                    if (isset($this->lessons[$lesson])) {
                        $data[] = array(
                            'users_id'   => $item->user_id,
                            'lessons_id' => $this->lessons[$lesson]->id,
                            'completed'  => str_replace('0000-00-00', '', $item->date_completed) ?: null,
                            'last_visit' => str_replace('0000-00-00', '', $item->date_last_visit) ?: null
                        );
                    }
                }
            }

            array_walk($data, function (&$values) use ($dbCampus) {
                $newValues = array_map(array($dbCampus, 'quote'), $values);
                $values    = str_replace($dbCampus->quote(''), 'NULL', join(',', $newValues));
            });

            $insertQuery = $dbCampus->getQuery(true)
                ->insert('#__oscampus_users_lessons')
                ->columns($keys)
                ->values($data);

            $dbCampus->setQuery($insertQuery)->execute();
            if ($error = $dbCampus->getErrorMsg()) {
                $this->errors[] = $error;
                return;
            }

            $this->viewCount += count($data);
            $offset += $limit;
        }
    }

    /**
     * Import Guru tasks as lessons
     * MUST be run after modules import
     */
    protected function loadLessons()
    {
        $dbGuru       = $this->getGuruDbo();
        $queryLessons = $dbGuru->getQuery(true)
            ->select('d.id modules_id, t.*')
            ->from('#__guru_task t')
            ->innerJoin('#__guru_mediarel mr ON mr.media_id = t.id')
            ->innerJoin('#__guru_days d ON d.id = mr.type_id')
            ->where('mr.type=' . $dbGuru->quote('dtask'));

        $modules       = $this->modules;
        $this->lessons = $this->copyTable(
            $queryLessons,
            '#__oscampus_lessons',
            $this->lessonsMap,
            'id',
            function ($guruData, $converted) use ($modules) {
                $oldKey = $converted->modules_id;
                if (isset($modules[$oldKey])) {
                    $converted->modules_id = $modules[$oldKey]->id;
                    $converted->access     =
                        isset($this->groupToView[$converted->access]) ?
                            $this->groupToView[$converted->access] :
                            $this->groupToView['default'];
                    return true;
                }
                return false;
            }
        );

    }

    /**
     * Import cms_type field as tags
     */
    protected function loadTags()
    {
        $dbGuru    = $this->getGuruDbo();
        $queryTags = $dbGuru->getQuery(true)
            ->select('trim(cms_type) title')
            ->from('#__guru_program')
            ->where('cms_type != ' . $dbGuru->quote(''))
            ->order('id asc')
            ->group('1');

        $tags = $dbGuru->setQuery($queryTags)->loadObjectList();

        $db         = JFactory::getDbo();
        $this->tags = array();
        foreach ($tags as $tag) {
            $tag->alias = OscampusApplicationHelper::stringURLSafe($tag->title);
            $db->insertObject('#__oscampus_tags', $tag);
            $tag->id                 = $db->insertid();
            $this->tags[$tag->title] = $tag;
        }

        $queryCourses = $dbGuru->getQuery(true)
            ->select('id, trim(cms_type) cms_type')
            ->from('#__guru_program')
            ->where('cms_type != ' . $dbGuru->quote(''));

        $courses = $dbGuru->setQuery($queryCourses)->loadObjectList();
        foreach ($courses as $course) {
            $tagTitle = trim($course->cms_type);
            if (isset($this->courses[$course->id]) && isset($this->tags[$course->cms_type])) {
                $newRow = (object)array(
                    'courses_id' => $this->courses[$course->id]->id,
                    'tags_id'    => $this->tags[$course->cms_type]->id
                );
                $db->insertObject('#__oscampus_courses_tags', $newRow);
                if ($error = $db->getErrorMsg()) {
                    $this->errors[] = 'Skipped Tag: ' . $error;
                }
            } else {
                $this->errors[] = 'Skipped Tag: ' . $course->id . ' / ' . $course->cms_type;
            }
        }
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
     * MUST be run after courses and teachers are loaded
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
     * Import Teachers
     * Must be called after courses are loaded
     */
    protected function loadTeachers()
    {
        $users = $this->getUsers();

        $this->teachers = $this->copyTable(
            '#__guru_authors',
            '#__oscampus_teachers',
            $this->teacherMap,
            'id',
            function ($guruData, $convertedData) use ($users) {
                if (isset($users[$convertedData->users_id])) {
                    $links                = array(
                        array(
                            'type' => 'website',
                            'link' => $guruData['website'],
                            'show' => (bool)$guruData['show_website'],
                        ),
                        array(
                            'type' => 'email',
                            'link' => null,
                            'show' => (bool)$guruData['show_email']
                        ),
                        array(
                            'type' => 'blog',
                            'link' => $guruData['blog'],
                            'show' => (bool)$guruData['show_blog'],
                        ),
                        array(
                            'type' => 'facebook',
                            'link' => (string)preg_replace(
                                '#^https?://(www\.)?(facebook\.com/)?#',
                                '',
                                $guruData['facebook']
                            ),
                            'show' => (string)(int)$guruData['show_facebook'],
                        ),
                        array(
                            'type' => 'twitter',
                            'link' => (string)preg_replace(
                                '#^https?://(www\.)?(twitter\.com/)?#',
                                '',
                                $guruData['twitter']
                            ),
                            'show' => (string)(int)$guruData['show_twitter']
                        )
                    );
                    $convertedData->links = json_encode($links);
                    return true;
                }
                return false;
            }
        );

        $dbGuru       = $this->getGuruDbo();
        $coursesQuery = $dbGuru->getQuery(true)
            ->select('p.id, a.id authors_id')
            ->from('#__guru_program p')
            ->innerJoin('#__guru_authors a ON a.userid = p.author');
        $courses      = $dbGuru->setQuery($coursesQuery)->loadObjectList();

        $db = JFactory::getDbo();
        foreach ($courses as $course) {
            if (isset($this->courses[$course->id])) {
                $oldKey = $course->authors_id;
                $update = (object)array(
                    'id'          => $this->courses[$course->id]->id,
                    'teachers_id' => null
                );
                if (isset($this->teachers[$oldKey])) {
                    $update->teachers_id = $this->teachers[$oldKey]->id;
                }
                $db->updateObject('#__oscampus_courses', $update, 'id', true);
                if ($error = $db->getErrorMsg()) {
                    $this->errors[] = $error;
                }
            }
        }
    }

    /**
     * Import courses/pathways
     */
    protected function loadCourses()
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $levels = array(
            0 => 'easy',
            1 => 'intermediate',
            2 => 'advanced'
        );

        $this->courses  = $this->copyTable(
            '#__guru_program',
            '#__oscampus_courses',
            $this->courseMap,
            'id',
            function ($guruData, $converted) use ($levels) {
                if (isset($levels[$converted->difficulty])) {
                    $converted->difficulty = $levels[$converted->difficulty];
                } else {
                    $converted->difficulty = 'unknown';
                }
                $converted->access = 0;

                if (preg_match('#(.*?)<hr\s+id="system-readmore"\s*/?>(.*)#ms', $converted->description, $matches)) {
                    $converted->introtext   = trim($matches[1]);
                    $converted->description = trim($matches[2]);
                }
                $converted->created = $guruData['startpublish'];
                return true;
            }
        );
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
     * @param JDatabaseQuery|string $from
     * @param string                $to
     * @param array                 $map
     * @param string                $key
     * @param callable              $callable
     *
     * @return array
     */
    protected function copyTable($from, $to, array $map, $key = 'id', $callable = null)
    {
        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        if ($from instanceof JDatabaseQuery) {
            $guruQuery = $from;

        } else {
            $guruQuery = $dbGuru->getQuery(true)
                ->select('*')
                ->from($from)
                ->order($key);
        }

        $fields   = array_filter($map);
        $guruData = $dbGuru->setQuery($guruQuery)->loadAssocList();
        if ($error = $dbGuru->getErrorMsg()) {
            $this->errors[] = $error;
            return array();
        }

        $campusData = array();
        foreach ($guruData as $row) {
            $converted = (object)array(
                'created'          => JFactory::getDate()->toSql(),
                'created_by_alias' => 'Guru Import'
            );
            foreach ($fields as $guruField => $campusField) {
                if (isset($row[$guruField])) {
                    if (substr($row[$guruField], 0, 10) != '0000-00-00') {
                        $converted->$campusField = $row[$guruField];
                    }
                }
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

                if ($key && isset($row[$key])) {
                    $campusData[$row[$key]] = $converted;
                } else {
                    $campusData[] = $converted;
                }
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

        echo '<div style="float: left; width:40%">';

        echo '<ul>';
        echo '<li>' . number_format(count($this->pathways)) . ' Pathways</li>';
        echo '<li>' . number_format(count($this->courses)) . ' Courses</li>';
        echo '<li>' . number_format(count($this->modules)) . ' Modules</li>';
        echo '<li>' . number_format(count($this->teachers)) . ' Teachers</li>';
        echo '<li>' . number_format(count($this->certificates)) . ' Certificates</li>';
        echo '<li>' . number_format(count($this->lessons)) . ' Lessons</li>';
        echo '<li>' . number_format($this->viewCount) . ' Viewed</li>';
        foreach ($this->images as $imageFolder => $images) {
            echo '<li>' . number_format(count($images)) . ' ' . $imageFolder . '</li>';
        }
        echo '</ul>';

        $courseQuery = $db->getQuery(true)
            ->select(
                array(
                    'cp.*',
                    'l.modules_id',
                    'p.title Pathway',
                    'c.title Course',
                    'm.title Module',
                    'l.title Lesson',
                    'u.username',
                    't.links'
                )
            )
            ->from('#__oscampus_courses c')
            ->leftJoin('#__oscampus_courses_pathways cp ON cp.courses_id = c.id')
            ->leftJoin('#__oscampus_pathways p ON p.id = cp.pathways_id')
            ->leftJoin('#__oscampus_teachers t ON t.id = c.teachers_id')
            ->leftJoin('#__users u ON u.id = t.users_id')
            ->leftJoin('#__oscampus_modules m ON m.courses_id = c.id')
            ->leftJoin('#__oscampus_lessons l ON l.modules_id = m.id')
            ->order('p.ordering, cp.ordering, m.ordering, l.ordering');

        $rows = $db->setQuery($courseQuery)->loadObjectList();
        if ($error = $db->getErrorMsg()) {
            $this->errors[] = $error;
        }

        $display = array();
        foreach ($rows as $row) {
            $pid = $row->pathways_id;
            $cid = $row->courses_id;
            $mid = $row->modules_id;

            if (!isset($display[$pid])) {
                $display[$pid] = (object)array(
                    'title' => $row->Pathway,
                    'items' => array()
                );
            }
            $path = $display[$pid];

            if (!isset($path->items[$cid])) {
                $path->items[$cid] = (object)array(
                    'title' => $row->Course,
                    'items' => array()
                );
            }
            $course = $path->items[$cid];

            if (!isset($course->items[$mid])) {
                $course->items[$mid] = (object)array(
                    'title' => $row->Module,
                    'items' => array()
                );
            }
            $module = $course->items[$mid];

            $module->items[] = $row->Lesson;
        }

        foreach ($display as $path) {
            echo '<h2>' . $path->title . '</h2>';
            echo '<ol>';
            foreach ($path->items as $course) {
                echo '<li>' . $course->title . '<ol>';
                foreach ($course->items as $module) {
                    echo '<li>' . $module->title . '<ol>';
                    foreach ($module->items as $lesson) {
                        echo '<li>' . $lesson . '</li>';
                    }
                    echo '</li></ol>';
                }
                echo '<br/></li></ol>';
            }
            echo '</li></ol>';
        }

        echo '</div>';

        echo '<div style="float: left; padding: 10px;">';
        echo '<h2>Time Log</h2>';
        $start = null;
        $last  = null;
        foreach ($this->log as $text => $time) {
            if ($start === null) {
                $last = $start = $time;
            }

            echo sprintf(
                '%s&gt; %s %s %s<br/>',
                date('Y-m-d H:i:s', $time),
                $this->timeStamp($time - $start),
                $this->timeStamp($time - $last),
                $text
            );

            $last = $time;
        }
        echo '</div>';

        if ($this->errors) {
            echo '<div style="float: left;">';
            echo '<h2>Error Log</h2>';
            echo join('<br/>', $this->errors);
            echo '</div>';
        }
    }

    /**
     * Format a number into H:M:S.U timestamp
     *
     * @param float $timestamp
     *
     * @return string
     */
    protected function timeStamp($timestamp)
    {
        $hours    = 0;
        $minutes  = 0;
        $seconds  = (int)$timestamp;
        $useconds = (int)(($timestamp - $seconds) * 1000);

        if ($seconds > 60) {
            $minutes = (int)$seconds / 60;
            $seconds -= ($minutes * 60);
        }
        if ($minutes > 60) {
            $hours = (int)$minutes[1] / 60;
            $minutes -= ($hours * 60);
        }

        return sprintf('%02d:%02d.%03d', $minutes, $seconds, $useconds);
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

    /**
     * Copy images from original guru source
     *
     * @param string $table
     * @param string $folder
     *
     * @return array
     */
    protected function copyImages($table, $folder)
    {
        $sourceRoot = 'https://www.ostraining.com';
        $targetRoot = '/images/stories/oscampus/' . trim($folder, '\\/');
        if (is_dir(JPATH_SITE . $targetRoot)) {
            JFolder::delete(JPATH_SITE . $targetRoot);
        }
        JFolder::create(JPATH_SITE . $targetRoot);

        $db     = JFactory::getDbo();
        $images = $db->setQuery("Select id,image From {$table}")->loadObjectList();
        foreach ($images as $image) {
            if ($image->image) {
                $path     = $sourceRoot . '/' . str_replace(' ', '%20', trim($image->image, '\\/'));
                $fileName = basename($image->image);
                $newPath  = $targetRoot . '/' . $fileName;
                if (!is_file(JPATH_SITE . $newPath)) {
                    $fileData = file_get_contents($path);
                    JFile::write(JPATH_SITE . $newPath, $fileData);
                }
                $image->image = substr($newPath, 1);
                $db->updateObject($table, $image, 'id');
            }
        }
        return $images;
    }
}
