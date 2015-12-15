<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('Text');

class OscampusFormFieldModule extends JFormFieldText
{
    public function getInput()
    {
        $courseId = $this->getCourseId();
        $options  = array_map(
            function ($row) {
                return $row->text;
            },
            JHtml::_('osc.options.modules', $courseId)
        );
        $options = json_encode($options);

        JHtml::_('script', 'com_oscampus/jquery-ui.js', false, true);
        JHtml::_('stylesheet', 'com_oscampus/jquery-ui.css', null, true);

        JHtml::_('osc.onready', "$('#{$this->id}').autocomplete({source: {$options}, autoFocus: true});");

        return parent::getInput();
    }

    protected function getCourseId()
    {
        $courseId = null;

        $fieldName = (string)$this->element['coursefield'] ?: 'courses_id';
        if ($courseField = $this->form->getField($fieldName)) {
            return $courseField->value;
        }

        return null;
    }
}
