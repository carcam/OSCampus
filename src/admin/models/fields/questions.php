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
        echo '<h4>Please don\'t mind the mess - this is a work in progress</h4>';

        $html = array('<div>');
        foreach ($this->value as $qkey => $question) {
            $attribs = array(
                'id'       => $this->id . '_' . $qkey,
                'name'     => $this->name . '[' . $qkey . ']',
                'value'    => $question['text'],
                'disabled' => 'disabled',
                'size'     => 75
            );
            $html[]  = '<div class="clr"></div>';
            $html[]  = '<input ' . OscampusUtilitiesArray::toString($attribs) . '/>';
        }

        return join('', $html);
    }
}
