<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusFormFieldQuestions extends JFormField
{
    protected function getInput()
    {
        JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);


        // Temporary css fixes
        $css = <<<STYLEFIX
.osc-quiz-questions input {
    float: none !important;
}
.osc-quiz-questions li {
    padding-left: 10px !important;
}

.osc-quiz-questions li.osc-question ul {
    padding-bottom: 10px;
}
STYLEFIX;

        OscampusFactory::getDocument()->addStyleDeclaration($css);

        $html = array(
            '<div class="clr"></div>',
            '<div class="osc-quiz-questions">',
            '<ul>'
        );

        // Begin build questions for current quiz
        foreach ($this->value as $qKey => $question) {
            $qId   = $this->id . '_' . $qKey;
            $qName = $this->name . '[' . $qKey . ']';

            $html[] = '<li class="osc-question">Q: '
                . $this->createInput($qId . '_text', $qName . '[text]', $question['text'])
                . '<i class="fa fa-minus-circle osc-delete-question"></i>';


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

                $html[] = '<li class="answer">'
                    . $answerCorrectInput
                    . $answerTextInput
                    . '<i class="fa fa-minus-circle osc-delete-answer"></i>'
                    . '</li>';
            }

            $html[] = '<li class="osc-add-answer"><i class="fa fa-plus-circle"></i> Add Answer</li>';
            $html[] = '</ul>';
            // End build answers for current question

            $html[] = '</li>';
        }

        $html[] = '<li class="osc-add-question"><i class="fa fa-plus-circle"></i> Add Question</li>';
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
