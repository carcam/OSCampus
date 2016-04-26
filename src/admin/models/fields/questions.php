<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

if (!defined('OSCAMPUS_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_oscampus/include.php';
}

class OscampusFormFieldQuestions extends JFormField
{
    protected function getInput()
    {
        JHtml::_('stylesheet', 'com_oscampus/admin.css', null, true);
        JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);

        JHtml::_('osc.jquery');
        JHtml::_('script', 'com_oscampus/admin/quiz.js', false, true);
        JHtml::_('osc.onready', '$.Oscampus.admin.quiz.init();');

        $html = array(
            '<div class="clr"></div>',
            '<div class="osc-quiz-questions">',
            '<ul>'
        );

        // Begin build questions for current quiz
        $questions = $this->getQuestions();

        $questionCount = 0;
        foreach ($questions as $question) {
            $questionId   = $this->id . '_' . $questionCount;
            $questionName = $this->name . '[' . $questionCount . ']';

            $html[] = '<li class="osc-question">'
                . $this->createInput($questionId . '_text', $questionName . '[text]', $question['text'])
                . $this->createButton('osc-btn-warning-admin osc-quiz-delete-question', 'fa-times');

            $questionCount++;

            // Begin build answers for current question
            $html[] = '<ul>';

            $answerCount = 0;
            foreach ($question['answers'] as $answer) {
                $html[] = $this->createAnswer(
                    $questionId,
                    $questionName,
                    $answerCount++,
                    $answer['text'],
                    $answer['correct']
                );
            }

            $html[] = '<li'
                . ' class="osc-quiz-add-answer">'
                . $this->createButton('osc-btn-main-admin', 'fa-plus', 'COM_OSCAMPUS_QUIZ_ADD_ANSWER')
                . '</li>';
            $html[] = '</ul>';
            // End build answers for current question

            $html[] = '</li>';
        }

        $html[] = '<li'
            . ' class="osc-quiz-add-question">'
            . $this->createButton('osc-btn-main-admin', 'fa-plus', 'COM_OSCAMPUS_QUIZ_ADD_QUESTION')
            . '</li>';
        $html[] = '</ul>';
        // End build questions for current quiz

        $html[] = '</div>';

        return join('', $html);
    }

    protected function createInput($id, $name, $value)
    {
        $attribs = array(
            'id'    => $id,
            'type'  => 'text',
            'name'  => $name,
            'value' => htmlspecialchars($value),
            'size'  => 75
        );

        return '<input ' . OscampusUtilitiesArray::toString($attribs) . '/>';
    }

    /**
     * Create the standard add/delete buttons
     *
     * @param string $class
     * @param string $icon
     * @param string $text
     *
     * @return string
     */
    protected function createButton($class, $icon, $text = null)
    {
        $button = '<button'
            . ' type="button"'
            . ' class="' . $class . '">'
            . '<i class="fa ' . $icon . '"></i>'
            . ($text ? ' ' . JText::_($text) : '')
            . '</button>';

        return $button;
    }

    /**
     * Create standard answer input
     *
     * @param string $questionId
     * @param string $questionName
     * @param string $key
     * @param string $text
     * @param bool   $correct
     *
     * @return string
     */
    protected function createAnswer($questionId, $questionName, $key, $text, $correct)
    {
        $id   = $questionId . '_' . $key;
        $name = $questionName . '[answers][' . $key . ']';

        $answerTextInput    = $this->createInput($id, $name, $text);
        $answerCorrectInput = '<input'
            . ' id="' . $questionId . '_correct"'
            . ' name="' . $questionName . '[correct]"'
            . ' type="radio"'
            . ' value="' . $key . '"'
            . ($correct ? ' checked' : '')
            . '/>';

        $html = '<li class="osc-answer">'
            . $answerCorrectInput
            . $answerTextInput
            . $this->createButton('osc-btn-warning-admin osc-quiz-delete-answer', 'fa-times')
            . '</li>';

        return $html;
    }

    /**
     * Normalize the question/answer blocks since they will be different
     * on form failure
     *
     * @return array[]
     */
    protected function getQuestions()
    {
        if (empty($this->value)) {
            return array(
                array(
                    'text'    => '',
                    'answers' => array(
                        array(
                            'text'    => '',
                            'correct' => 0
                        )
                    )
                )
            );
        }

        $questions = (array)$this->value;
        foreach ($questions as &$question) {
            $answers = (array)$question['answers'];
            $correct = isset($question['correct']) ? $question['correct'] : false;

            foreach ($answers as $answerId => $answer) {
                if ($correct !== false && is_string($answer)) {
                    $answers[$answerId] = array(
                        'text' => $answer,
                        'correct' => ($correct == $answerId)
                    );
                }
            }
            $question['answers'] = $answers;
        }

        return $questions;
    }
}
