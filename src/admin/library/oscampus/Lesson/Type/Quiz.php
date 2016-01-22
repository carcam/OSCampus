<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use JHtml;
use Oscampus\DateTime;
use Oscampus\Lesson;
use Oscampus\Lesson\ActivityStatus;

defined('_JEXEC') or die();

class Quiz extends AbstractType
{
    /**
     * @var int
     */
    public $passingScore = 70;

    /**
     * @var int
     */
    public $timeLimit = 10;

    /**
     * @var int
     */
    public $limitAlert = 1;

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

        $options = json_encode(
            array(
                'timeLimit'  => 5, //$this->timeLimit * 60,
                'limitAlert' => $this->limitAlert * 60
            )
        );

        JHtml::_('osc.onready', "$.Oscampus.quiz.timer({$options})");

        if ($this->getUserState('quiz_time') === null) {
            $this->setUserState('quiz_time', $this->timeLimit * 60);
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
            $length = min($this->quizLength, count($this->questions));
            $keys   = array_rand($this->questions, $length);
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
     * Override the default behavior to review and grade a quiz
     *
     * @param ActivityStatus $status
     * @param int            $score
     * @param mixed          $data
     * @param bool           $updateLastVisitTime
     */
    public function prepareActivityProgress(ActivityStatus $status, $score, $data, $updateLastVisitTime = true)
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

        $now = new DateTime();
        if ($status->score >= $this->passingScore) {
            $status->completed = $now;
        }
        if ($updateLastVisitTime) {
            $status->last_visit = $now;
        }
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
}
