<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use JHtml;
use Oscampus\Lesson;

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

    public function readAttempt($activity)
    {
        if (isset($activity->data) && $activity->data) {
            $attempt = json_decode($activity->data);

            $total   = count($attempt);
            $correct = 0;
            foreach ($attempt as $question) {
                $question->answers = (array)$question->answers;
                $selected = $question->selected;
                if (isset($question->answers[$selected])) {
                    $correct += (int)$question->answers[$selected]->correct;
                } else {
                    $question->selected = null;
                }
            }
            return (object)array(
                'score'     => round(($correct / $total) * 100, 0),
                'correct'   => $correct,
                'questions' => $attempt
            );
        }

        return null;
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
}
