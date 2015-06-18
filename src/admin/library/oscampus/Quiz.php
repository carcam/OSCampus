<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace Oscampus;

use ReflectionClass;
use ReflectionProperty;

defined('_JEXEC') or die();

class Quiz
{
    /**
     * @var int
     */
    public $score = null;

    /**
     * @var int
     */
    public $select = null;

    /**
     * @var int
     */
    public $timelimit = null;

    /**
     * @var int
     */
    public $alert = null;

    /**
     * @var array
     */
    public $questions = array();

    public function __construct($data)
    {
        $ref        = new ReflectionClass($this);
        $properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $property->name;
            if (!empty($data->$name)) {
                if ($name == 'questions') {
                    $this->loadQuestions($data->$name);
                } else {
                    $this->$name = $data->$name;
                }
            }
        }
    }

    public function loadQuestions(array $questions)
    {
        $this->questions = array();
        foreach ($questions as $row) {
            $question                       = new Quiz\Question($row->text, $row->choices);
            $this->questions[$question->id] = $question;
        }

        return $this;
    }

    public function startTimer()
    {
        $displayTime = sprintf('%02d:00', $this->timelimit);

        $body = array(
            '<span class="osc-quiz-time-left">Time left</span>',
            '<span class="osc-quiz-time">' . $displayTime . '</span>',
            '<span class="osc-quiz-time-labels">Minutes Seconds</span>'
        );

        return '<div class="osc-quiz-left">'
        . join('<br/>', $body)
        . '<br/></div>';
    }
}
