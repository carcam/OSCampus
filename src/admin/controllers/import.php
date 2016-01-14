<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerImport extends OscampusControllerBase
{
    protected $mediaTextScrub = array(
        '#<!-- Start of Brightcove Player -->#',
        '#^\s*<br[\s/]*>\s*$#ims'
    );

    protected $viewedQuizChunk = 100;

    protected $groupToView = array(
        1         => 1, // Public group == Public View Level
        2         => 2, // Non-member == Registered VL
        14        => 3, // Video Member == Video, Personal, Pro, Admin VL
        'default' => 3 // Use use most restrictive VL if something goes wrong
    );

    protected $certificates = array();
    protected $errors       = array();
    protected $files        = array();
    protected $images       = array();
    protected $tags         = array();
    protected $users        = null;

    protected $downloadCount = 0;
    protected $filesSkipped  = 0;
    protected $mediaCount    = 0;
    protected $mediaSkipped  = 0;
    protected $viewCount     = 0;
    protected $viewQuizCount = 0;

    protected $log = array();

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
        'alias'        => null,
        'description'  => null,
        'image'        => null,
        'published'    => null,
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

    protected $customBackup    = '/logs/oscampus.custom.backup';
    protected $customPathways  = array();
    protected $customJunctions = array();

    protected $questionDuplicates    = array();
    protected $questionDuplicateFlag = ' [DUPLICATE]';

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->customBackup = JPATH_SITE . $this->customBackup;
    }

    public function import()
    {
        error_reporting(-1);
        ini_set('display_errors', 1);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        echo '<p><a href="index.php?option=com_oscampus">Back to main  screen</a></p>';

        ob_start();

        echo '<p>Max Execution Time: ' . ini_get('max_execution_time') . '<br/>';
        echo 'Memory Limit: ' . ini_get('memory_limit') . '</p>';

        $this->log['Start'] = microtime(true);

        $this->saveCustomPaths();

        $this->clearTable('#__oscampus_courses_pathways', false);
        $this->clearTable('#__oscampus_courses_tags', false);
        $this->clearTable('#__oscampus_files_links', false);

        $this->clearTable('#__oscampus_users_lessons');
        $this->clearTable('#__oscampus_files');
        $this->clearTable('#__oscampus_tags');
        $this->clearTable('#__oscampus_lessons');
        $this->clearTable('#__oscampus_modules');
        $this->clearTable('#__oscampus_certificates');
        $this->clearTable('#__oscampus_courses');
        $this->clearTable('#__oscampus_pathways');
        $this->clearTable('#__oscampus_teachers');
        $this->clearTable('#__oscampus_wistia_downloads');

        $this->log['Clear Tables'] = microtime(true);

        $this->loadCourses();
        $this->log['Load Courses'] = microtime(true);

        $this->restoreCustomPaths();
        $this->log['Restore additional pathways'] = microtime(true);

        $this->loadTags();
        $this->log['Load Tags'] = microtime(true);

        $this->loadTeachers();
        $this->log['Load Teachers'] = microtime(true);

        $this->loadModules();
        $this->log['Load Modules'] = microtime(true);

        $this->loadLessons();
        $this->log['Load Lessons'] = microtime(true);

        $this->loadExerciseFiles();
        $this->log['Load Files'] = microtime(true);

        $this->loadCertificates();
        $this->log['Load Certificates'] = microtime(true);

        $this->loadViewed();
        $this->log['Load Viewed'] = microtime(true);

        $this->loadViewedQuizzes();
        $this->log['Load Viewed Quizzes'] = microtime(true);

        $this->loadWistiaDownloadLog();
        $this->log['Load Wistia Log'] = microtime(true);

        $this->images['Teacher Images']   = $this->copyImages('#__oscampus_teachers', 'teachers');
        $this->log['Load Teacher Images'] = microtime(true);

        $this->images['Course Images']   = $this->copyImages('#__oscampus_courses', 'courses');
        $this->log['Load Course Images'] = microtime(true);

        $this->images['Pathway Images']   = $this->copyImages('#__oscampus_pathways', 'pathways', false);
        $this->log['Load Pathway Images'] = microtime(true);

        $this->fixOrdering('#__oscampus_pathways');
        $this->log['Fix Ordering Fields'] = microtime(true);

        echo '<p>Memory: ' . number_format(memory_get_usage(true) / 1024 / 1024) . 'M<br/>';
        echo 'Peak Memory: ' . number_format(memory_get_peak_usage(true) / 1024 / 1024) . 'M<br/>';
        echo 'Total Time: ' . number_format((microtime(true) - $this->log['Start'])/60, 2) . ' Minutes</p>';

        $this->displayResults();

        $log = ob_get_contents();
        file_put_contents(JPATH_SITE . '/logs/oscampus.import.log', $log);

        ob_clean();

        error_reporting(0);
        ini_set('display_errors', 0);

        JFactory::getApplication()->redirect('index.php?option=com_oscampus&task=import.showlog');
    }

    public function showlog()
    {
        echo '<p><a href="index.php?option=com_oscampus">Back to main  screen</a></p>';

        $path = JPATH_SITE . '/logs/oscampus.import.log';

        if (is_file($path)) {
            $modified = new DateTime();
            $modified->setTimestamp(filemtime($path));
            echo '<p>Last Import finished at: ' . $modified->format('Y-m-d H:i:s T (P)') . '</p>';

            echo file_get_contents($path);

        } else {
            echo '<p>Log file was not found</p>';
        }
    }

    /**
     * Load and attach Exercise Files. We also look for references to these files
     * within what has been imported into lesson footers to determine if a file
     * should be attached to specific lessons
     *
     * MUST be called after ::loadLessons()
     */
    protected function loadExerciseFiles()
    {
        $targetRoot = 'images/stories/oscampus/files';
        if (is_dir(JPATH_SITE . '/' . $targetRoot)) {
            JFolder::delete(JPATH_SITE . '/' . $targetRoot);
        }
        JFolder::create(JPATH_SITE . '/' . $targetRoot);

        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $filesQuery = $dbGuru->getQuery(true)
            ->select(
                array(
                    'mr.type_id courses_id',
                    'local path',
                    'm.name title',
                    'instructions description',
                    'p.startpublish created',
                    '1 published',
                    $dbCampus->quote('Guru Imported') . ' created_by_alias'
                )
            )
            ->from('#__guru_media as m')
            ->innerJoin('#__guru_mediarel as mr ON m.id=mr.media_id')
            ->innerJoin('#__guru_program as p ON p.id = mr.type_id')
            ->where('mr.type=' . $dbGuru->quote('pmed'));

        // First load all the known files
        $files = $dbGuru->setQuery($filesQuery)->loadObjectList();
        foreach ($files as $file) {
            $path = JPATH_SITE . '/media/files/' . $file->path;
            if (is_file($path)) {
                if (isset($this->courses[$file->courses_id])) {
                    $file->path = $targetRoot . '/' . $file->path;

                    if (!is_file(JPATH_SITE . '/' . $file->path)) {
                        copy($path, JPATH_SITE . '/' . $file->path);
                    }

                    $coursesId = $this->courses[$file->courses_id]->id;
                    unset($file->courses_id);
                    $dbCampus->insertObject('#__oscampus_files', $file);
                    $file->id         = $dbCampus->insertid();
                    $file->courses_id = $coursesId;
                    $this->files[]    = $file;
                } else {
                    $this->filesSkipped++;
                }
            }
        }

        // Then attach them to courses and lessons
        foreach ($this->files as $file) {
            $coursesId   = $file->courses_id;
            $lessonQuery = $dbCampus->getQuery(true)
                ->select('l.id')
                ->from('#__oscampus_courses c')
                ->innerJoin('#__oscampus_modules m ON m.courses_id = c.id')
                ->innerJoin('#__oscampus_lessons l ON l.modules_id = m.id')
                ->where(
                    array(
                        'c.id = ' . $coursesId,
                        'l.footer like ' . $dbCampus->quote('%' . basename($file->path) . '%')
                    )
                );

            $lessonIds = $dbCampus->setQuery($lessonQuery)->loadColumn();

            $insert = (object)array(
                'files_id'   => $file->id,
                'courses_id' => $coursesId
            );
            if ($lessonIds) {
                foreach ($lessonIds as $lessonId) {
                    $insert->lessons_id = $lessonId;
                    $dbCampus->insertObject('#__oscampus_files_links', $insert);
                    if ($error = $dbCampus->getErrorMsg()) {
                        $this->errors[] = $error;
                        return;
                    }
                }
            } else {
                $dbCampus->insertObject('#__oscampus_files_links', $insert);
                if ($error = $dbCampus->getErrorMsg()) {
                    $this->errors[] = $error;
                    return;
                }
            }
        }
    }

    /**
     * Load media into lessons
     * MUST run after ::loadLessons()
     */
    protected function loadLessonMedia()
    {
        $dbGuru = $this->getGuruDbo();

        $mediaQuery = $dbGuru->getQuery(true)
            ->select(
                array(
                    'm.id',
                    't.id lessons_id',
                    'mr.layout',
                    'm.type type',
                    'm.code content',
                    'm.auto_play autoplay',
                    'q.id quiz_id',
                    'q.max_score quiz_passing_score',
                    'q.nb_quiz_select_up quiz_question_count',
                    'q.limit_time quiz_timelimit',
                    'q.limit_time_f quiz_alert_end'
                )
            )
            ->from('#__guru_program p')
            ->innerJoin('#__guru_days d ON d.pid = p.id')
            ->innerJoin('#__guru_mediarel tr ON tr.type_id = d.id AND tr.type=' . $dbGuru->quote('dtask'))
            ->innerJoin('#__guru_task t ON t.id = tr.media_id')
            ->innerJoin('#__guru_mediarel lr ON lr.type_id = t.id AND lr.type=' . $dbGuru->quote('scr_l'))
            ->innerJoin('#__guru_mediarel mr ON mr.type_id = t.id AND mr.layout=lr.media_id')
            ->leftJoin('#__guru_media m ON m.id = mr.media_id And mr.layout != 12')
            ->leftJoin('#__guru_quiz q ON q.id = mr.media_id AND mr.layout = 12');

        $mediaList = $dbGuru->setQuery($mediaQuery)->loadObjectList();

        $questions = $this->getGuruQuestions();

        $this->mediaCount = 0;
        $updateList       = array();
        foreach ($mediaList as $mediaItem) {
            if (isset($this->lessons[$mediaItem->lessons_id])) {
                $guruLessonId = $mediaItem->lessons_id;
                $this->mediaCount++;

                $lessonId = $this->lessons[$guruLessonId]->id;
                if (isset($updateList[$guruLessonId])) {
                    $lesson = $updateList[$guruLessonId];

                } else {
                    $lesson = (object)array(
                        'id'      => $lessonId,
                        'type'    => null,
                        'header'  => null,
                        'content' => null,
                        'footer'  => null
                    );
                }

                $mediaItem->content = preg_replace($this->mediaTextScrub, '', $mediaItem->content);

                switch ($mediaItem->layout) {
                    case 1:
                    case 3:
                    case 6:
                        $lesson->type = 'wistia';

                        switch ($mediaItem->type) {
                            case 'video':
                                $content = array(
                                    'id'       => null,
                                    'autoplay' => $mediaItem->autoplay,
                                );
                                if (preg_match('#{wistia}(.*?){/wistia}#', $mediaItem->content, $matches)) {
                                    $content['id'] = $matches[1];
                                }
                                $lesson->content = json_encode($content);
                                break;

                            case 'text':
                                if ($lesson->footer) {
                                    $lesson->footer .= "\n<br/>\n";
                                }
                                $lesson->footer .= $mediaItem->content;
                                break;
                        }
                        break;

                    case 5:
                        $lesson->type    = 'text';
                        $lesson->content = $mediaItem->content;

                        break;

                    case 12:
                        $lesson->type = 'quiz';

                        $quizQuestions = isset($questions[$mediaItem->quiz_id])
                            ? $questions[$mediaItem->quiz_id] : array();

                        $content = array(
                            'passingScore' => $mediaItem->quiz_passing_score,
                            'quizLength'   => $mediaItem->quiz_question_count,
                            'timeLimit'    => $mediaItem->quiz_timelimit,
                            'limitAlert'   => $mediaItem->quiz_alert_end,
                            'questions'    => $quizQuestions
                        );

                        $lesson->content = json_encode($content);
                        break;

                    default:
                        $lesson->header .= "Unknown Layout {$mediaItem->layout}\n";
                        break;
                }

                $updateList[$guruLessonId] = $lesson;
            }
        }

        $dbCampus = JFactory::getDbo();
        foreach ($updateList as $guruLessonId => $lesson) {
            if ($lesson->type == 'wistia' && $lesson->content == '') {
                // Some classes were set for the incorrect layout
                $lesson->type    = 'text';
                $lesson->content = $lesson->footer;
                $lesson->footer  = '';
            }
            $this->lessons[$guruLessonId]->type    = $lesson->type;
            $this->lessons[$guruLessonId]->content = $lesson->content;

            $dbCampus->updateObject('#__oscampus_lessons', $lesson, 'id');
            if ($error = $dbCampus->getErrorMsg()) {
                $this->errors[] = $error;
                return;
            }
        }

        $this->mediaSkipped = count($mediaList) - $this->mediaCount;
    }

    /**
     * Retrieve all quiz questions from guru
     */
    protected function getGuruQuestions()
    {
        $dbGuru = $this->getGuruDbo();

        $questionsQuery = $dbGuru->getQuery(true)
            ->select('*')
            ->from('#__guru_questions');
        $list           = $dbGuru->setQuery($questionsQuery)->loadObjectList();
        $questions      = array();
        foreach ($list as $question) {
            $answers = array();
            $correct = array_filter(explode('|', $question->answers));
            $correct = array_map('intval', $correct);
            for ($a = 1; $a <= 10; $a++) {
                $f = "a{$a}";
                if ($question->$f != '') {
                    $answer        = array(
                        'correct' => (int)in_array($a, $correct),
                        'text'    => stripslashes($question->$f)
                    );
                    $key           = md5($answer['text']);
                    $answers[$key] = $answer;
                }
            }

            if (!isset($questions[$question->qid])) {
                $questions[$question->qid] = array();
            }

            $text = stripslashes($question->text);
            $key  = md5($text);
            if (isset($questions[$question->qid][$key])) {
                $text .= $this->questionDuplicateFlag;
                $this->questionDuplicates[] = $question->id;

                $key = md5($text);
            }
            $questions[$question->qid][$key] = array(
                'text'    => $text,
                'answers' => $answers
            );
        }

        return $questions;
    }

    /**
     * Import Viewed lessons
     * MUST run after ::loadCourses(), ::loadModules() and ::loadLessons()
     */
    protected function loadViewed()
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
            ->group('pid, user_id')
            ->order('user_id');

        $keys   = array('users_id', 'lessons_id', 'completed', 'score', 'visits', 'first_visit', 'last_visit');
        $offset = 0;
        $limit  = 1000;
        while ($items = $dbGuru->setQuery($viewedQuery, $offset, $limit)->loadObjectList()) {
            $data = array();
            foreach ($items as $item) {
                $lessons = array_unique(explode('||', trim($item->lesson_id, '|')));
                sort($lessons);

                foreach ($lessons as $lesson) {
                    if (isset($this->lessons[$lesson]) && $this->lessons[$lesson]->type != 'quiz') {
                        $activity = array(
                            'users_id'    => $item->user_id,
                            'lessons_id'  => $this->lessons[$lesson]->id,
                            'completed'   => str_replace('0000-00-00', '', $item->date_completed) ?: null,
                            'score' => 0,
                            'visits'      => 1,
                            'first_visit' => str_replace('0000-00-00', '', $item->date_last_visit) ?: null,
                            'last_visit'  => str_replace('0000-00-00', '', $item->date_last_visit) ?: null
                        );
                        if ($activity['completed']) {
                            $activity['score'] = 100;
                        }
                        $data[] = $activity;
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
     * Still a work in progress
     */
    protected function loadViewedQuizzes()
    {
        $this->viewQuizCount = 0;

        $dbGuru   = $this->getGuruDbo();
        $dbCampus = JFactory::getDbo();

        $questionsQuery = $dbGuru->getQuery(true)
            ->select(
                array(
                    'qt.user_id',
                    'mr.type_id lessons_id',
                    't.name task_name',
                    'q.id quiz_id',
                    'qq.id question_id',
                    'q.name quiz_name',
                    'qq.text question',
                    'qt.date_taken_quiz last_visit',
                    'qt.score_quiz',
                    'qq.answers',
                    'qqt.answers_gived answer_given'
                )
            )
            ->from('#__guru_quiz q')
            ->innerJoin('#__guru_questions qq ON qq.qid = q.id')
            ->innerJoin('#__guru_quiz_question_taken qqt ON qqt.question_id = qq.id')
            ->innerJoin('#__guru_quiz_taken qt ON qt.id = qqt.show_result_quiz_id')
            ->innerJoin('#__users u ON u.id = qt.user_id')
            ->innerJoin('#__guru_mediarel mr ON mr.media_id = q.id AND mr.layout = 12')
            ->innerJoin('#__guru_task t ON t.id = mr.type_id')
            ->order('qt.date_taken_quiz desc, q.id desc, qqt.id asc');

        $scoreCalc = function ($score_quiz) {
            if (substr_count($score_quiz, '|') == 1) {
                list($correct, $length) = explode('|', $score_quiz);
                return ($correct / $length) * 100;
            }
            die('bad format');
        };

        $cleanResults = function ($string) {
            $cleaned = array_filter(explode('|', $string));
            array_walk($cleaned, function (&$row) {
                $row = preg_replace('/[^\d]/', '', $row);
            });
            return $cleaned;
        };

        $getAnswer = function ($quiz, $question, $answer, $correct) use ($cleanResults) {
            $pool   = $quiz->questions;
            $result = (object)array(
                'text'     => null,
                'answers'  => array(),
                'selected' => null
            );

            $qkey = md5(stripslashes($question));
            if (isset($pool->$qkey)) {
                $result->text    = $pool->$qkey->text;
                $result->answers = $pool->$qkey->answers;

                $correct = $cleanResults($correct);
                $answer  = $cleanResults($answer);
                $answer  = array_pop($answer);

                $i = 0;
                foreach ((array)$result->answers as $aKey => $a) {
                    $i++;
                    if ($i == $answer) {
                        $result->selected = $aKey;
                    }

                    if (in_array($i, $correct) && !$a->correct) {
                        echo 'SENT: ' . func_get_arg(2) . ' :: ' . func_get_arg(3);
                        echo '<pre>';
                        print_r($correct);
                        print_r($answer);
                        print_r($pool->$qkey);
                        echo '</pre>';
                        die('mismatch on correct answers');
                    }
                }
                return $result;
            }

            echo '<pre>';
            var_dump($question);
            var_dump($answer);
            var_dump($correct);

            print_r($quiz);
            echo '</pre>';

            die('bad question');
        };

        $activity = OscampusFactory::getContainer()->activity;

        $insertKeys = get_object_vars($activity->getStatus(0, 0));
        unset($insertKeys['id']);

        foreach ($this->lessons as $guruLessonId => $lesson) {
            $users = array();
            if ($lesson->type == 'quiz') {
                $quizContent = json_decode($lesson->content);

                $questionsQuery->clear('where')->where('mr.type_id = ' . $guruLessonId);

                $questions = $dbGuru->setQuery($questionsQuery)->loadObjectList();
                foreach ($questions as $question) {
                    $userId = $question->user_id;
                    if (isset($users[$userId])) {
                        $userStatus = $users[$userId];

                    } else {
                        $userStatus = $activity->getStatus($lesson->id, $userId);
                        unset($userStatus->id);

                        $userStatus->score       = $scoreCalc($question->score_quiz);
                        $userStatus->visits      = 1;
                        $userStatus->first_visit = $question->last_visit;
                        $userStatus->last_visit  = $question->last_visit;
                        $userStatus->data        = array();
                        $users[$userId]          = $userStatus;
                    }

                    if (in_array($question->question_id, $this->questionDuplicates)) {
                        $question->question .= $this->questionDuplicateFlag;
                    }
                    $qkey = md5($question->question);

                    $userStatus->data[$qkey] = $getAnswer(
                        $quizContent,
                        $question->question,
                        $question->answer_given,
                        $question->answers
                    );
                }

                $insertValues = array();
                foreach ($users as $userId => $user) {
                    $user->data = array_values($user->data);

                    $correct = 0;
                    foreach ($user->data as $result) {
                        if ($selected = $result->selected) {
                            $correct += (int)$result->answers->$selected->correct;
                        }
                    }
                    $user->score = round(($correct / count($user->data)) * 100, 0);
                    if ($user->score >= $quizContent->passingScore) {
                        $user->completed = $user->last_visit;
                    }
                    $user->data = json_encode($user->data);

                    $quotedValues   = array_map(array($dbCampus, 'quote'), (array)$user);
                    $insertValues[] = str_replace($dbCampus->quote(''), 'NULL', join(',', $quotedValues));
                }

                $segments = array_chunk($insertValues, $this->viewedQuizChunk);

                foreach ($segments as $segment) {
                    $insertQuery = $dbCampus->getQuery(true)
                        ->insert('#__oscampus_users_lessons')
                        ->columns(array_keys($insertKeys))
                        ->values($segment);

                    $dbCampus->setQuery($insertQuery)->execute();
                    if ($error = $dbCampus->getErrorMsg()) {
                        $this->errors[] = $error;
                        return;
                    }
                }

                $this->viewQuizCount += count($insertValues);
            }
        }
    }

    /**
     * Import Guru tasks as lessons
     * MUST run after ::loadModules()
     */
    protected function loadLessons()
    {
        $dbGuru       = $this->getGuruDbo();
        $lessonsQuery = $dbGuru->getQuery(true)
            ->select('d.id modules_id, t.*')
            ->from('#__guru_task t')
            ->innerJoin('#__guru_mediarel mr ON mr.media_id = t.id')
            ->innerJoin('#__guru_days d ON d.id = mr.type_id')
            ->where('mr.type=' . $dbGuru->quote('dtask'));

        $modules       = $this->modules;
        $this->lessons = $this->copyTable(
            $lessonsQuery,
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

        $this->loadLessonMedia();
    }

    /**
     * Import cms_type field as tags
     */
    protected function loadTags()
    {
        $dbGuru    = $this->getGuruDbo();
        $tagsQuery = $dbGuru->getQuery(true)
            ->select('trim(cms_type) title')
            ->from('#__guru_program')
            ->where('cms_type != ' . $dbGuru->quote(''))
            ->order('id asc')
            ->group('1');

        $tags = $dbGuru->setQuery($tagsQuery)->loadObjectList();

        $db         = JFactory::getDbo();
        $this->tags = array();
        foreach ($tags as $tag) {
            $tag->alias = OscampusApplicationHelper::stringURLSafe($tag->title);
            $db->insertObject('#__oscampus_tags', $tag);
            $tag->id                 = $db->insertid();
            $this->tags[$tag->title] = $tag;
        }

        $coursesQuery = $dbGuru->getQuery(true)
            ->select('id, trim(cms_type) cms_type')
            ->from('#__guru_program')
            ->where('cms_type != ' . $dbGuru->quote(''));

        $courses = $dbGuru->setQuery($coursesQuery)->loadObjectList();
        foreach ($courses as $course) {
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
     * MUST run after ::loadCourses()
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
                    unset($convertedData->created, $convertedData->created_by_alias);
                    return true;
                }
                return false;
            }
        );
    }

    /**
     * Load certificates earned by users
     * MUST run after ::loadCourses() and ::loadTeachers()
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
     * MUST run after ::loadCourse()
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
                        'website'  => array(
                            'link' => preg_match('#^https?://.+#',
                                trim($guruData['website'])) ? trim($guruData['website']) : '',
                            'show' => (int)(bool)$guruData['show_website'],
                        ),
                        'email'    => array(
                            'link' => null,
                            'show' => (int)(bool)$guruData['show_email']
                        ),
                        'blog'     => array(
                            'link' => preg_match('#^https?://.+#',
                                trim($guruData['blog'])) ? trim($guruData['blog']) : '',
                            'show' => (int)(bool)$guruData['show_blog'],
                        ),
                        'facebook' => array(
                            'link' => trim(preg_replace(
                                '#^https?://(www\.)?(facebook\.com/)?#',
                                '',
                                trim($guruData['facebook'])
                            )),
                            'show' => (int)$guruData['show_facebook'],
                        ),
                        'twitter'  => array(
                            'link' => trim(preg_replace(
                                '#^https?://(www\.)?(twitter\.com/)?#',
                                '',
                                trim($guruData['twitter'])
                            )),
                            'show' => (int)$guruData['show_twitter']
                        )
                    );
                    $convertedData->links = json_encode($links);
                    $convertedData->image = ltrim($convertedData->image, '/');
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
            0 => 'beginner',
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
                $converted->access = 1;

                if (preg_match('#(.*?)<hr\s+id="system-readmore"\s*/?>(.*)#ms', $converted->description, $matches)) {
                    $converted->introtext   = trim($matches[1]);
                    $converted->description = trim($matches[2]);
                }
                $converted->created = $guruData['startpublish'];
                return true;
            }
        );
        $this->pathways = $this->copyTable(
            '#__guru_category',
            '#__oscampus_pathways',
            $this->pathwayMap,
            'id',
            function ($guruData, $converted) {
                $converted->access = 1;
                return true;
            });

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
     * Move com_oswistia download log into oscampus
     */
    protected function loadWistiaDownloadLog()
    {
        $db = JFactory::getDbo();

        $fields = array(
            'users_id'           => 'downloaded_by users_id',
            'downloaded'         => 'downloaded',
            'ip'                 => 'downloaded_from ip',
            'media_hashed_id'    => 'media_hashed_id',
            'media_project_name' => 'media_project_name',
            'media_name'         => 'media_name'
        );

        $query = $db->getQuery(true)
            ->select($fields)
            ->from('#__oswistia_download_log')
            ->order('id');

        $rows = $db->setQuery($query)->loadAssocList();

        $insertValues = array();
        foreach ($rows as $row) {
            $quotedValues   = array_map(array($db, 'quote'), $row);
            $insertValues[] = str_replace($db->quote(''), 'NULL', join(',', $quotedValues));
        }

        $segments = array_chunk($insertValues, 1000);
        foreach ($segments as $segment) {
            $insertQuery = $db->getQuery(true)
                ->insert('#__oscampus_wistia_downloads')
                ->columns(array_keys($fields))
                ->values($segment);

            $db->setQuery($insertQuery)->execute();
            if ($error = $db->getErrorMsg()) {
                $this->errors[] = $error;
                return;
            }
            $this->downloadCount += count($segment);
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

        $campusData   = array();
        $creationDate = JFactory::getDate()->toSql();
        foreach ($guruData as $row) {
            $converted = (object)array(
                'created'          => $creationDate,
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
        return JFactory::getDbo();
        /*
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
        */
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
        echo '<li>' . number_format($this->mediaCount) . ' Media processed</li>';
        echo '<li>' . number_format($this->mediaSkipped) . ' Media Skipped</li>';
        echo '<li>' . number_format(count($this->files)) . ' Files</li>';
        echo '<li>' . number_format($this->filesSkipped) . ' Files Skipped</li>';
        echo '<li>' . number_format($this->viewCount) . ' Viewed</li>';
        echo '<li>' . number_format($this->viewQuizCount) . ' Quizzes Taken';
        echo '<li>' . number_format($this->downloadCount) . ' Wistia Download Logs';
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
        $targetRoot = '/images/stories/oscampus/' . trim($folder, '\\/');
        if (!is_dir(JPATH_SITE . $targetRoot)) {
            JFolder::create(JPATH_SITE . $targetRoot);
        }

        $db        = JFactory::getDbo();
        $images    = $db->setQuery("Select id,image From {$table}")->loadObjectList();
        $usedFiles = array();
        foreach ($images as $image) {
            if ($image->image) {
                $path     = '/' . trim($image->image, '\\/');
                $fileName = basename($image->image);
                $newPath  = $targetRoot . '/' . $fileName;

                if (!is_file(JPATH_SITE . $newPath)) {
                    if (is_file(JPATH_SITE . $path)) {
                        copy(JPATH_SITE . $path, JPATH_SITE . $newPath);
                    } else {
                        $this->errors[] = 'Image Missing: ' . JPATH_SITE . $path;
                    }
                }
                $image->image = substr($newPath, 1);
                $db->updateObject($table, $image, 'id');
                $usedFiles[] = $fileName;
            }
        }

        $files       = JFolder::files(JPATH_SITE . $targetRoot);
        $unusedFiles = array_diff($files, $usedFiles);
        foreach ($unusedFiles as $file) {
            $path = JPATH_SITE . $targetRoot . '/' . $file;
            unlink($path);
            $this->errors[] = 'Removed unused image file ' . $path;
        }

        return $images;
    }

    /**
     * Save any custom pathways that may have been created since guru imports
     */
    protected function saveCustomPaths()
    {
        if (is_file($this->customBackup)) {
            $backup = json_decode(file_get_contents($this->customBackup));

            $this->customPathways  = get_object_vars($backup->pathways);
            $this->customJunctions = get_object_vars($backup->junctions);

        } else {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('pathway.*, id AS oldId')
                ->from('#__oscampus_pathways pathway')
                ->order('id');

            $this->customPathways = $db->setQuery($query)->loadObjectList('id');

            $query = $db->getQuery(true)
                ->select('cp.*, course.title, course.alias')
                ->from('#__oscampus_courses_pathways cp')
                ->innerJoin('#__oscampus_courses course ON course.id = cp.courses_id')
                ->where('cp.pathways_id IN (SELECT pathways_id FROM #__osteammate_leaders_pathways)');

            $junctions = $db->setQuery($query)->loadObjectList();

            $this->customJunctions = array();
            foreach ($junctions as $junction) {
                if (!isset($customJunctions[$junction->courses_id])) {
                    $this->customJunctions[$junction->courses_id] = (object)array(
                        'title' => $junction->title,
                        'alias' => $junction->alias,
                        'links' => array()
                    );
                }
                $this->customJunctions[$junction->courses_id]->links[] = (object)array(
                    'courses_id'  => $junction->courses_id,
                    'pathways_id' => $junction->pathways_id,
                    'ordering'    => $junction->ordering
                );
            }

            $backup = array(
                'pathways'  => $this->customPathways,
                'junctions' => $this->customJunctions
            );

            file_put_contents($this->customBackup, json_encode($backup));
        }
    }

    /**
     * Restore the saved custom pathways
     */
    protected function restoreCustomPaths()
    {
        $search = function ($title, $alias, array $array) {
            foreach ($array as $key => $row) {
                if ($row->alias == $alias) {
                    return $row->id;
                }
            }
            return false;
        };

        // Make sure we have all the pathways we started out with
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__oscampus_pathways')
            ->where('id < 10');

        $currentPathways = $db->setQuery($query)->loadObjectList('id');

        foreach ($this->customPathways as $oldId => $pathway) {
            if ($id = $search($pathway->title, $pathway->alias, $currentPathways)) {
                $pathway->id = $id;

            } else {
                $insert = clone $pathway;
                unset($insert->oldId, $insert->id);
                $insert->created_by_alias = JFactory::getUser($insert->created_by)->name;

                $db->insertObject('#__oscampus_pathways', $insert, 'id');
                if ($error = $db->getErrorMsg()) {
                    $this->errors[] = $error;
                }
                $pathway->id = $db->insertId();
            }
        }

        // Make sure the OSTeammate leader configs match the new pathway ids
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__osteammate_leaders_pathways');

        $leaderPathways = $db->setQuery($query)->loadObjectList();
        if ($error = $db->getErrorMsg()) {
            $this->errors[] = $error;
        } else {
            $db->setQuery('Delete From #__osteammate_leaders_pathways')->execute();
            foreach ($leaderPathways as $lp) {
                $lp->pathways_id = $this->customPathways[$lp->pathways_id]->id;
                $db->insertObject('#__osteammate_leaders_pathways', $lp);
                if ($error = $db->getErrorMsg()) {
                    $this->errors[] = $error;
                }
            }
        }

        // Re-insert the courses for the custom pathways
        $query = $db->getQuery(true)
            ->select('id, title, alias')
            ->from('#__oscampus_courses')
            ->where('id IN (' . join(',', array_keys($this->customJunctions)) . ')');

        $courses = $db->setQuery($query)->loadObjectList();

        foreach ($this->customJunctions as $oldId => $junctions) {
            $id = $search($junctions->title, $junctions->alias, $courses);

            foreach ($junctions->links as $junction) {
                $junction->courses_id  = $id;
                $junction->pathways_id = $this->customPathways[(int)$junction->pathways_id]->id;
                $db->insertObject('#__oscampus_courses_pathways', $junction);
                if ($error = $db->getErrorMsg()) {
                    $this->errors[] = $error;
                }
            }
        }

        if (is_file($this->customBackup)) {
            unlink($this->customBackup);
        }
    }

    /**
     * Standardize ordering fields for table with ID field
     *
     * @param string $table
     *
     * @return void
     */
    protected function fixOrdering($table)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, ordering')
            ->from($table)
            ->order('ordering');

        if ($rows = $db->setQuery($query)->loadObjectList()) {
            foreach ($rows as $order => $row) {
                $row->ordering = $order + 1;
                $db->updateObject($table, $row, 'id');
            }
        }
    }
}
