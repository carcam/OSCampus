<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscRender
{
    /**
     * @var int[] Provide conversion from grid spans to J!2 widths
     */
    protected static $spanWidths = array(20, 20, 30, 40, 40, 50, 60, 60, 70, 80, 80, 100);

    /**
     * When in J!3 display the submenu as a sidebar. J!2 has no equivalent.
     *
     * @return string
     */
    public static function submenu()
    {
        if (version_compare(JVERSION, '3', 'ge')) {
            return JHtmlSidebar::render();
        }

        return '';
    }

    /**
     * Convert a grid span to a J!2 width class number
     *
     * @param int|string $span
     *
     * @return string
     */
    protected static function spanToWidth($span)
    {
        $idx = $span - 1;
        if (isset(static::$spanWidths[$idx])) {
            return (string)static::$spanWidths[$idx];
        }

        return '100';
    }

    /**
     * Write a J! version agnostic fieldset block for the admin UI
     *
     * @param JForm       $form
     * @param string      $fieldSet
     * @param bool|string $legend
     * @param int|string  $span
     *
     * @return string
     */
    public static function adminfieldset(JForm $form, $fieldSet, $legend = false, $span = 12)
    {
        $html      = array();
        $fieldSets = $form->getFieldsets();

        if (!empty($fieldSets[$fieldSet])) {
            if ($legend === true) {
                $legend = $fieldSets[$fieldSet]->label;
            }
            $description = $fieldSets[$fieldSet]->description;

            $fields = $form->getFieldset($fieldSet);

            if (version_compare(JVERSION, '3.0', 'lt')) {
                $html = static::adminFieldsetJ2($fields, $legend, $description, $span);
            } else {
                $html = static::adminFieldsetJ3($fields, $legend, $description, $span);
            }
        }
        return join("\n", $html);
    }

    /**
     * Write a J! version agnostic field block for the admin UI
     *
     * @param JFormField  $field
     * @param bool|string $legend
     * @param int|string  $span
     *
     * @return string
     */
    public static function adminfield(JFormField $field, $legend = false, $span = 12)
    {
        if ($legend === true) {
            if (version_compare(JVERSION, '3', 'lt')) {
                // The only way to get what we want in J!2
                $elementProperty = new ReflectionProperty($field, 'element');
                $elementProperty->setAccessible(true);

                $element = $elementProperty->getValue($field);
                $legend  = JText::_($element['label']);

            } else {
                $legend = JText::_($field->getAttribute('label'));
            }
        }

        $joomla2 = version_compare(JVERSION, '3', 'lt');

        $html = array();
        if ($joomla2) {
            $html[] = '<div class="fltlft width-' . static::spanToWidth($span) . '">';
        } else {
            $html[] = "<div class=\"span{$span}\">";
        }

        $html[] = '<fieldset class="adminform">';
        if ($legend) {
            $html[] = '<legend>' . JText::_($legend) . '</legend>';
        }
        $html[] = $field->input;
        $html[] = '</fieldset>';
        $html[] = '</div>';

        return join("\n", $html);
    }

    /**
     * Render an array of fields for Joomla! 3 admin UI
     *
     * @param JFormField[] $fields
     *
     * @return string[]
     */
    protected static function adminFieldsJ3(array $fields)
    {
        $html = array();

        foreach ($fields as $field) {
            $html[] = $field->renderField();
        }

        return $html;
    }

    /**
     * Render a fieldset block for Joomla! 3 admin UI
     *
     * @param JFormFIeld[] $fields
     * @param string       $legend
     * @param string       $description
     * @param int|string   $span
     *
     * @return string[]
     */
    protected static function adminFieldsetJ3(array $fields, $legend, $description, $span)
    {
        $html   = array();
        $html[] = "<div class=\"span{$span}\">";
        if ($legend) {
            $html[] = '<fieldset class="adminform">';
            $html[] = '<legend>' . JText::_($legend) . '</legend>';
        }
        if ($description) {
            $html[] = '<div>' . JText::_($description) . '</div>';
        }

        /** @var JFormField $field */
        $html = array_merge($html, static::adminFieldsJ3($fields));

        if ($legend) {
            $html[] = '</fieldset>';
        }
        $html[] = '</div>';

        return $html;
    }

    /**
     * Render an array of fields for Joomla! 2 admin UI
     *
     * @param JFormField[] $fields
     *
     * @return string[]
     */
    protected static function adminFieldsJ2(array $fields)
    {
        $html = array();

        $html[] = '<ul class="adminformlist">';

        $count = 0;
        foreach ($fields as $name => $field) {
            $count++;

            if (strcasecmp($field->type, 'editor')) {
                if ($count == 1 || substr(end($html), -5) != '</li>') {
                    $html[] = '<ul>';
                }
                $html[] = '<li>' . $field->label . $field->input . '</li>';

            } else {
                // Editor fields require special handling in J2
                if (substr(end($html), -5) == '</li>') {
                    $html[] = '</ul>';
                }
                $html[] = '<div class="clr"></div>';
                if ($field->label) {
                    $html[] = $field->label;
                    $html[] = '<div class="clr"></div>';
                }
                $html[] = $field->input;
                if ($count < count($fields)) {
                    $html[] = '<div class="clr"></div>';
                }
            }
        }

        if (substr(end($html), -5) == '</li>') {
            // Close off the list if last thing is a list item
            $html[] = '</ul>';
        }

        return $html;
    }

    /**
     * Render a fieldset block for Joomla! 2 admin UI
     *
     * @param JFormField[] $fields
     * @param string       $legend
     * @param string       $description
     * @param int|string   $span
     *
     * @return string[]
     */
    protected static function adminFieldsetJ2(array $fields, $legend, $description, $span)
    {
        $html   = array();
        $html[] = '<div class="fltlft width-' . static::spanToWidth($span) . '">';
        $html[] = '<fieldset class="adminform">';

        if ($legend) {
            $html[] = '<legend>' . JText::_($legend) . '</legend>';
        }
        if ($description) {
            $html[] = '<div>' . JText::_($description) . '</div>';
        }

        $html = array_merge($html, static::adminFieldsJ2($fields));

        $html[] = '</fieldset>';
        $html[] = '</div>';

        return $html;
    }

    /**
     * Simple form field rendering for use in twig
     *
     * @param JFormField $field
     *
     * @return string
     */
    public static function formfield(JFormField $field)
    {
        return $field->label . ' ' . $field->input;
    }

    /**
     * Output all hidden fields in a fieldset
     *
     * @param JForm  $form
     * @param string $fieldSetName
     *
     * @return string
     */
    public static function hiddenfields(JForm $form, $fieldSetName = 'hidden')
    {
        $html = array();

        if ($fieldSetName) {
            $fields = $form->getFieldset($fieldSetName);
        } else {
            $fields    = array();
            $fieldSets = $form->getFieldsets();
            foreach ($fieldSets as $fieldSetName => $fieldSet) {
                $fieldSet = $form->getFieldset($fieldSetName);
                $fields   = array_merge($fields, array_values($fieldSet));
            }
        }
        foreach ($fields as $field) {
            if (!strcasecmp($field->type, 'hidden')) {
                $html[] = $field->input;
            }
        }

        return join("\n", $html);
    }
}
