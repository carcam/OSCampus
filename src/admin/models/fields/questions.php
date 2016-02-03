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

        $html = array(
            '<div class="clr"></div>',
            '<div class="osc-quiz-questions">',
            '<ul>'
        );

        // Begin build questions for current quiz
        foreach ($this->value as $qKey => $question) {
            $qId   = $this->id . '_' . $qKey;
            $qName = $this->name . '[' . $qKey . ']';

            $html[] = '<li class="osc-question">'
                . $this->createInput($qId . '_text', $qName . '[text]', $question['text'])
                . $this->createButton('osc-btn-warning-admin osc-delete-question', 'fa-times');

            // Begin build answers for current question
            $html[] = '<ul>';

            $answers = array();
            foreach ($question['answers'] as $aKey => $answer) {
                $aId   = $qId . '_' . $aKey;
                $aName = $qName . '[answers][' . $aKey . ']';

                $answerTextInput    = $this->createInput($aId, $aName, $answer['text']);
                $answerCorrectInput = '<input'
                    . ' id="' . $qId . '_correct"'
                    . ' name="' . $qName . '[correct]"'
                    . ' type="radio"'
                    . ' value="' . $aKey . '"'
                    . ($answer['correct'] ? ' checked' : '')
                    . '/>';

                $html[] = '<li class="osc-answer">'
                    . $answerCorrectInput
                    . $answerTextInput
                    . $this->createButton('osc-btn-warning-admin osc-delete-answer', 'fa-times')
                    . '</li>';
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
}
