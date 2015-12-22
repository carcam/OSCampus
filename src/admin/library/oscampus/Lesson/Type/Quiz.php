<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Oscampus\Lesson;

defined('_JEXEC') or die();

class Quiz extends AbstractType
{
    /**
     * @var object[]
     */
    protected $questions = null;

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
        return $this;
    }

    public function getQuestions()
    {
        $length = min($this->quizLength, count($this->questions));
        $keys = array_rand($this->questions, $length);
        shuffle($keys);

        $selection = array();
        foreach ($keys as $key) {
            $selection[$key] = $this->questions[$key];
        }

        return $selection;
    }
}
