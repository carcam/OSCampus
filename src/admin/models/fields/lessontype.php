<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('List');

class OscampusFormFieldLessontype extends JFormFieldList
{
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        if (empty($value) && $element['readonly']) {
            $element['readonly'] = 'false';
        }

        return parent::setup($element, $value, $group);
    }

    protected function getOptions()
    {
        $types = JHtml::_('osc.options.lessontypes');
        return array_merge(parent::getOptions(), $types);
    }
}
