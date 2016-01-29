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
                . '<button class="osc-btn-warning-admin"><i class="fa fa-times osc-delete-question"></i></button>';


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
                    . '<button class="osc-btn-warning-admin"><i class="fa fa-times osc-delete-answer"></i></button>'
                    . '</li>';
            }

            $html[] = '<li class="osc-add-answer"><button class="osc-btn-main-admin"><i class="fa fa-plus"></i> Add Answer</li></button>';
            $html[] = '</ul>';
            // End build answers for current question

            $html[] = '</li>';
        }

        $html[] = '<li class="osc-add-question"><button class="osc-btn-main-admin"><i class="fa fa-plus"></i> Add Question</button></li>';
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
}
