<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('List');

class OscampusFormFieldOptions extends JFormFieldList
{
    protected function getOptions()
    {
        $options = parent::getOptions();

        $optionName = strtolower((string)$this->element['options']);
        try {
            $options = array_merge($options, JHtml::_('osc.options.' . $optionName));

        } catch (Exception $e) {
            OscampusFactory::getApplication()->enqueueMessage(
                __CLASS__ . ': ' . JText::sprintf('COM_OSCAMPUS_ERROR_FIELD_BAD_OPTIONS', $optionName),
                'error'
            );
        }

        return $options;
    }
}
