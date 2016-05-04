<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('List');

class OscampusFormFieldTags extends JFormFieldList
{
    protected function getOptions()
    {
        $options = array_map(
            function ($row) {
                $row->selected = false;
                $row->checked  = false;
                $row->disable  = false;
                return $row;
            },
            JHtml::_('osc.options.tags')
        );

        return array_merge(parent::getOptions(), $options);
    }
}
