<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class OscampusFormFieldCourse extends JFormFieldList
{
    protected function getOptions()
    {
        $courses = JHtml::_('osc.options.courses');

        return array_merge(parent::getOptions(), $courses);
    }
}
