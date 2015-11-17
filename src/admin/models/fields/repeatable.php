<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('Repeatable');

if (class_exists('JFormFieldRepeatable')) {
    class OscampusFormFieldRepeatable extends JFormFieldRepeatable
    {
    }

} else {
    class OscampusFormFieldRepeatable extends JFormField
    {
        /**
         * Method to get the field input markup.
         *
         * @return  string  The field input markup.
         *
         * @since   11.1
         */
        protected function getInput()
        {
            return '<input type="text" readonly disabled value="Under Construction"/>';
        }
    }
}

