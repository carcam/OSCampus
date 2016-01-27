<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('Radio');

class OscampusFormFieldLessontype extends JFormFieldRadio
{
    public function getInput()
    {
        $readonly = (string)$this->element['readonly'];
        if ($readonly == 'true' || $readonly == '1') {
            $options = $this->getOptions();

            $attribs = array(
                'readonly' => 'readonly',
                'disabled' => 'disabled',
                'class'    => (string)$this->element['class']
            );
            return JHtml::_(
                'select.genericlist',
                $options,
                $this->name,
                $attribs,
                'value',
                'text',
                $this->value,
                $this->id
            );
        }

        return parent::getInput();
    }

    protected function getOptions()
    {
        $types = JHtml::_('osc.options.lessontypes');
        return array_merge(parent::getOptions(), $types);
    }
}
