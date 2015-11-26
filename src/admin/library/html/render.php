<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscRender
{
    public static function submenu()
    {
        if (version_compare(JVERSION, '3', 'ge')) {
            return JHtmlSidebar::render();
        }

        return '';
    }

    /**
     * Render a form fieldset for the admin UI
     *
     * @param JForm  $form
     * @param string $fieldSet
     * @param string $legend
     * @param string $width
     *
     * @return string
     */
    public static function adminfieldset(JForm $form, $fieldSet, $legend = null, $width = null)
    {
        $html      = array();
        $fieldSets = $form->getFieldsets();

        if (!empty($fieldSets[$fieldSet])) {
            $legend = $legend ?: $fieldSets[$fieldSet]->label;
            $fields = $form->getFieldset($fieldSet);

            if (version_compare(JVERSION, '3.0', 'lt')) {
                $html = static::adminFieldsetJ2($fields, $legend);
            } else {
                $html = static::adminFieldsetJ3($fields, $legend);
            }
        }
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
     * Render a Joomla! 3 fieldset for admin UI
     *
     * @param JFormFIeld[] $fields
     * @param string       $legend
     *
     * @return array
     */
    protected static function adminFieldsetJ3(array $fields, $legend = null)
    {
        $html   = array();
        $html[] = '<div class="row-fluid">';
        $html[] = '<fieldset class="adminform">';
        if ($legend) {
            $html[] = '<legend>' . JText::_($legend) . '</legend>';
        }

        /** @var JFormField $field */
        $html = array_merge($html, static::adminFieldsJ3($fields));

        $html[] = '</fieldset>';
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

        reset($fields);
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
     * Render a fieldset for Joomla! 2 admin UI
     *
     * @param JFormField[] $fields
     * @param string       $legend
     * @param string       $width
     *
     * @return array
     */
    protected static function adminFieldsetJ2(array $fields, $legend = null, $width = null)
    {
        $width = $width ?: '100';

        $html   = array();
        $html[] = '<div class="width-' . $width . '">';
        $html[] = '<fieldset class="adminform">';

        if ($legend) {
            $html[] = '<legend>' . JText::_($legend) . '</legend>';
        }

        $html = array_merge($html, static::adminFieldsJ2($fields));

        $html[] = '</fieldset>';
        $html[] = '</div>';
        $html[] = '<div class="clr"></div>';

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
     * @param string $fieldSet
     *
     * @return string
     */
    public static function hiddenfields(JForm $form, $fieldSet = 'hidden')
    {
        $html = array();
        if ($fields = $form->getFieldset($fieldSet)) {
            foreach ($fields as $field) {
                if (!strcasecmp($field->type, 'hidden')) {
                    $html[] = $field->input;
                }
            }
        }

        return join("\n", $html);
    }
}
