<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class OscampusFormFieldCourse extends JFormFieldList
{
    protected function getOptions()
    {
        return array(
            JHtml::_('select.option', '', 'UnderConstruction')
        );
    }
}
