<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Oscampus\Lesson\Type;

use Exception;
use JHtml;
use Joomla\Registry\Registry as Registry;
use JText;
use Oscampus\Activity\LessonStatus;
use Oscampus\Lesson;
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
     * @param LessonStatus $activity
     *
     * @return object[]
     */
    public function getLastAttempt(LessonStatus $activity)
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
     * Prepare a LessonStatus for recording user progress.
     *
     * @param LessonStatus $status
     * @param int          $score
     * @param mixed        $data
     *
     * @return void
     */
    public function prepareActivityProgress(LessonStatus $status, $score = null, $data = null)
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
     * @param Registry $data
     *
     * @return SimpleXMLElement
     */
    public function prepareAdminData(Registry $data)
    {
        $content = $data->get('content');
        if ($content && is_string($content)) {
            $data->set('content', json_decode($content, true));
        }

        $path = __DIR__ . '/quiz.xml';

        $xml = simplexml_load_file($path);

        return $xml;
    }

    public function saveAdminChanges(Registry $data)
    {
        $quiz = $data->get('content');

        if ($quiz) {
            if (is_string($quiz)) {
                $quiz = json_decode($quiz, true);
            }
        }

        $questions = array();
        foreach ((array)$quiz->questions as $questionId => $question) {
            $question = (array)$question;

            $questionText = $question['text'];
            if ($questionText) {
                if (!isset($question['correct'])) {
                    throw new Exception(JText::sprintf('COM_OSCAMPUS_ERROR_QUIZ_CORRECT_ANSWER', $questionText));
                }

                $questionKey    = md5($questionText);
                $correctAnswer  = $question['correct'];
                $enteredAnswers = (array)$question['answers'];

                $answers = array();
                foreach ($enteredAnswers as $answerId => $answerText) {
                    if (!empty($answerText)) {
                        $answerKey = md5($answerText);
                        if (isset($answers[$answerKey])) {
                            throw new Exception(
                                JText::sprintf(
                                    'COM_OSCAMPUS_ERROR_QUIZ_DUPLICATE_ANSWER',
                                    $answerText,
                                    $questionText
                                )
                            );
                        }

                        $answers[$answerKey] = array(
                            'text'    => $answerText,
                            'correct' => (int)($correctAnswer == $answerId)
                        );
                    }
                }

                $minimumAnswers = 2;
                if (count($answers) < $minimumAnswers) {
                    throw new Exception(
                        JText::sprintf(
                            'COM_OSCAMPUS_ERROR_QUIZ_MINIMUM_ANSWERS',
                            $questionText,
                            $minimumAnswers
                        )
                    );
                }

                $questions[$questionKey] = array(
                    'text'    => $question['text'],
                    'answers' => $answers
                );
            }
        }

        $quiz->questions = $questions;
        $data->set('content', json_encode($quiz));
    }
}
