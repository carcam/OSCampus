<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class OscampusFormFieldLessontype extends JFormFieldList
{
    protected function getOptions()
    {
        $types = JHtml::_('osc.options.lessontypes');

        return array_merge(parent::getOptions(), $types);
    }
}
