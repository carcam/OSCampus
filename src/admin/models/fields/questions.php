<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusFormFieldQuestions extends JFormField
{
    protected function getInput()
    {
        $value = $this->value;
        if ($value && is_string($value)) {
            $value = json_decode($value, true);
        }

        echo '<pre>';
        print_r($value);
        echo '</pre>';

        return '';
    }
}
