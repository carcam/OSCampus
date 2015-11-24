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
        return array_merge(parent::getOptions(), JHtml::_('osc.options.pathways'));
    }
}
