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
    public $quizLength = null;

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
     * @var object[]
     */
    protected $questions = null;

    public function __construct(Lesson $lesson)
    {
        parent::__construct($lesson);

        $properties = (array)json_decode($lesson->content);

        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        $this->questions = (array)$this->questions;
        foreach ($this->questions as $question) {
            $question->answers = (array)$question->answers;
        }
    }

    public function render()
    {
        JHtml::_('osc.jquery');
        JHtml::_('script', 'com_oscampus/quiz.js', false, true);
        JHtml::_('osc.onready', '$.Oscampus.quiz.timer()');

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
            $status->data = json_encode($responses);
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
                $q = $questions[$question];
                if (isset($q->answers[$answer])) {
                    $data[] = (object)array(
                        'text'     => $q->text,
                        'answers'  => $q->answers,
                        'selected' => $answer
                    );
                }
            }
        }

        return $data;
    }
}
