<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use JHtml;
use JRegistry;
use JText;
use Oscampus\Lesson;
use Oscampus\Lesson\ActivityStatus;
use OscampusFactory;
use SimpleXMLElement;

defined('_JEXEC') or die();

class Quiz extends AbstractType
{
    const PASSING_SCORE = 70;
    const TIME_LIMIT    = 10;
    const LIMIT_ALERT   = 1;

    /**
     * @var int
     */
    public $passingScore = null;

    /**
     * @var int
     */
    public $timeLimit = null;

    /**
     * @var int
     */
    public $limitAlert = null;

    /**
     * @var int
     */
    public $quizLength = null;

    /**
     * @var object[]
     */
    protected $questions = null;

    public function __construct(Lesson $lesson)
    {
        parent::__construct($lesson);

        $this->passingScore = static::PASSING_SCORE;
        $this->timeLimit    = static::TIME_LIMIT;
        $this->limitAlert   = static::LIMIT_ALERT;

        $content = json_decode($lesson->content);

        $this->questions = isset($content->questions) ? (array)$content->questions : array();
        foreach ($this->questions as $question) {
            $question->answers = (array)$question->answers;
        }
        $this->quizLength = isset($content->quizLength) ? $content->quizLength : count($this->questions);
    }

    public function render()
    {
        JHtml::_('osc.jquery');
        JHtml::_('script', 'com_oscampus/quiz.js', false, true);

        if ($this->lesson->isAuthorised()) {
            $options = json_encode(
                array(
                    'timer' => array(
                        'timeLimit'  => $this->timeLimit * 60,
                        'limitAlert' => $this->limitAlert * 60
                    )
                )
            );

            JText::script('COM_OSCAMPUS_QUIZ_TIMEOUT');

            JHtml::_('osc.onready', "$.Oscampus.quiz.timer({$options})");
        }

        return $this;
    }

    /**
     * @param ActivityStatus $activity
     *
     * @return object[]
     */
    public function getLastAttempt(ActivityStatus $activity)
    {
        if (isset($activity->data) && $activity->data) {
            $questions = json_decode($activity->data);
            foreach ($questions as $question) {
                $question->answers = (array)$question->answers;
            }

            return $questions;
        }

        return array();
    }

    /**
     * Custom method to select randomly ordered questions. Retains the
     * same questions and order while the quiz time is still running
     *
     * @return array
     */
    public function getQuestions()
    {
        $cookieStore = 'quiz_questions_' . $this->lesson->id;

        if (($keys = $this->getUserState($cookieStore)) && $this->getUserState('quiz_time')) {
            $keys = json_decode(base64_decode($keys));

        } else {
            $length = (int)$this->quizLength ?: (int)count($this->questions);
            $length = min(count($this->questions), $length);

            $keys = array_rand($this->questions, $length);
            shuffle($keys);

            $this->setUserState($cookieStore, base64_encode(json_encode($keys)));
        }

        $selection = array();
        foreach ($keys as $key) {
            $selection[$key] = $this->questions[$key];
        }

        return $selection;
    }

    /**
     * Prepare an ActivityStatus for recording user progress.
     *
     * @param ActivityStatus $status
     * @param int            $score
     * @param mixed          $data
     *
     * @return void
     */
    public function prepareActivityProgress(ActivityStatus $status, $score = null, $data = null)
    {
        if (is_array($data)) {
            $status->score = 0;

            $responses = $this->collectResponse($data);
            foreach ($responses as $response) {
                $selected = $response->selected;
                $answer   = $response->answers[$selected];

                $status->score += (int)$answer->correct;
            }
            $status->score = round(($status->score / count($responses)) * 100, 0);
            $status->data  = json_encode($responses);
        }

        $now = OscampusFactory::getDate();
        if ($status->score >= $this->passingScore) {
            $status->completed = $now;
        }
        $status->last_visit = $now;
    }

    /**
     * Process the raw responses from a form. Expects an array in
     * the form
     * array( questionHash => answerHash)
     *
     * Where the hashes are md5 hashes of the associated texts.
     *
     * @param array $responses
     *
     * @return array
     */
    protected function collectResponse(array $responses)
    {
        $questions = $this->getQuestions();

        $data = array();
        foreach ($responses as $question => $answer) {
            if (isset($questions[$question])) {
                $q      = $questions[$question];
                $a      = isset($q->answers[$answer]) ? $answer : null;
                $data[] = (object)array(
                    'text'     => $q->text,
                    'answers'  => $q->answers,
                    'selected' => $a
                );
            }
        }

        return $data;
    }

    /**
     * Prepare data and provide XML for use in lesson admin UI.
     *
     * @param JRegistry $data
     *
     * @return SimpleXMLElement
     */
    public function prepareAdminData(JRegistry $data)
    {
        $content = $data->get('content');
        if ($content && is_string($content)) {
            $data->set('content', json_decode($content, true));
        }

        $path = __DIR__ . '/quiz.xml';

        $xml = simplexml_load_file($path);

        return $xml;
    }

    public function saveAdminChanges(JRegistry $data)
    {
        $quiz = $data->get('content');

        if ($quiz) {
            if (is_string($quiz)) {
                $quiz = json_decode($quiz, true);
            }
        }

        $questions = array();
        foreach ((array)$quiz->questions as $questionId => $question) {
            if ($question['text']) {
                $questionKey = md5($question['text']);
                $correct     = $question['correct'];

                $answers = array();
                foreach ((array)$question['answers'] as $answerId => $answer) {
                    $answerKey = md5($answer);

                    $answers[$answerKey] = array(
                        'text'    => $answer,
                        'correct' => (int)($correct == $answerId)
                    );
                }

                if ($answers = array_filter($answers)) {
                    $questions[$questionKey] = array(
                        'text'    => $question['text'],
                        'answers' => $answers
                    );
                }
            }
        }

        $quiz->questions = $questions;
        $data->set('content', json_encode($quiz));
    }
}
