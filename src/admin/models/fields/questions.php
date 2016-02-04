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
        JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);

        JHtml::_('osc.jquery');
        JHtml::_('script', 'com_oscampus/admin/quiz.js', false, true);
        JHtml::_('osc.onready', '$.Oscampus.admin.quiz.init();');

        // Make sure we have at least one question/answer to work with
        if (empty($this->value)) {
            $this->value = array(
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

        $html = array(
            '<div class="clr"></div>',
            '<div class="osc-quiz-questions">',
            '<ul>'
        );

        // Begin build questions for current quiz
        $questionCount = 0;
        foreach ($this->value as $question) {
            $questionId   = $this->id . '_' . $questionCount;
            $questionName = $this->name . '[' . $questionCount . ']';

            $html[] = '<li class="osc-question">'
                . $this->createInput($questionId . '_text', $questionName . '[text]', $question['text'])
                . $this->createButton('osc-btn-warning-admin osc-delete-question', 'fa-times');

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
                . ' class="osc-add-answer">'
                . $this->createButton('osc-btn-main-admin', 'fa-plus', 'COM_OSCAMPUS_QUIZ_ADD_ANSWER')
                . '</li>';
            $html[] = '</ul>';
            // End build answers for current question

            $html[] = '</li>';
        }

        $html[] = '<li'
            . ' class="osc-add-question">'
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
            'value' => $value,
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
            . $this->createButton('osc-btn-warning-admin osc-delete-answer', 'fa-times')
            . '</li>';

        return $html;
    }
}
