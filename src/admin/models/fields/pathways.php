<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('Checkboxes');

class OscampusFormFieldPathways extends JFormFieldCheckboxes
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
            JHtml::_('osc.options.pathways')
        );

        return array_merge(parent::getOptions(), $options);
    }
}
