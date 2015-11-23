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
     * Render a form fieldset with the ability to compact two fields
     * into a single line
     *
     * @param JForm  $form
     * @param string $fieldSet
     * @param string $legend
     * @param bool   $tabbed
     * @param array  $sameLine
     *
     * @return string
     */
    public static function adminfields(
        JForm $form,
        $fieldSet,
        $legend = null,
        $tabbed = false,
        array $sameLine = array()
    ) {
        $html      = array();
        $fieldSets = $form->getFieldsets();

        if (!empty($fieldSets[$fieldSet])) {
            $name   = $fieldSets[$fieldSet]->name;
            $label  = $fieldSets[$fieldSet]->label;
            $legend = (!$tabbed && !$legend) ? $label : $legend;

            if (version_compare(JVERSION, '3.0', 'lt')) {
                $html = static::adminFieldsetJ2($form, $name, $label, $legend, $tabbed, $sameLine);
            } else {
                $html = static::adminFieldsetJ3($form, $name, $label, $legend, $tabbed, $sameLine);
            }
        }
        return join("\n", $html);
    }

    /**
     * Render an admin form for Joomla! 3.x
     *
     * @param JForm  $form
     * @param string $name
     * @param string $label
     * @param string $legend
     * @param bool   $tabbed
     * @param array  $sameLine
     *
     * @return array
     */
    protected static function adminFieldsetJ3(JForm $form, $name, $label, $legend, $tabbed, array $sameLine)
    {
        $html = array();
        if ($tabbed) {
            $html[] = JHtml::_('bootstrap.addTab', 'myTab', $name, JText::_($label));
        }
        $html[] = '<div class="row-fluid">';
        $html[] = '<fieldset class="adminform">';
        if ($legend) {
            $html[] = '<legend>' . JText::_($legend) . '</legend>';
        }

        /** @var JFormField $field */
        foreach ($form->getFieldset($name) as $field) {
            if (in_array($field->fieldname, $sameLine)) {
                continue;
            }

            $html[] = $field->renderField();
            if (isset($sameLine[$field->fieldname])) {
                $html[] = ' ' . $form->getField($sameLine[$field->fieldname])->input;
            }

            $html[] = '</div>';
            $html[] = '</div>';
        }
        $html[] = '</fieldset>';
        $html[] = '</div>';
        if ($tabbed) {
            $html[] = JHtml::_('bootstrap.endTab');
        }

        return $html;
    }

    /**
     * Render an admin form for Joomla! 2.5
     *
     * @param JForm  $form
     * @param string $name
     * @param string $label
     * @param string $legend
     * @param bool   $tabbed
     * @param array  $sameLine
     *
     * @return array
     */
    protected static function adminFieldsetJ2(JForm $form, $name, $label, $legend, $tabbed, array $sameLine)
    {
        $html = array();
        if ($tabbed) {
            $html[] = JHtml::_('tabs.panel', JText::_($label), $name . '-page');
        }
        $html[] = '<div class="width-100">';
        $html[] = '<fieldset class="adminform">';

        if ($legend) {
            $html[] = '<legend>' . JText::_($legend) . '</legend>';
        }

        $html[] = '<ul class="adminformlist">';

        $fieldSet  = $form->getFieldset($name);
        $remaining = count($fieldSet);
        foreach ($fieldSet as $field) {
            $remaining--;
            if (in_array($field->fieldname, $sameLine)) {
                continue;
            }

            if (strcasecmp($field->type, 'editor')) {
                $html[] = '<li>' . $field->label . $field->input . '</li>';
                if (isset($sameLine[$field->fieldname])) {
                    $html[] = ' ' . $form->getField($sameLine[$field->fieldname])->input;
                }

            } else {
                // Editor fields require special handling in J2
                $html[] = '</ul>';
                $html[] = '<div class="clr"></div>';
                if ($field->label) {
                    $html[] = $field->label;
                    $html[] = '<div class="clr"></div>';
                }
                $html[] = $field->input;
                if ($remaining) {
                    $html[] = '<div class="clr"></div>';
                    $html[] = '<ul>';
                }
            }

        }

        if (substr(end($html), -5) == '</li>') {
            // Close off the list if last thing is a list item
            $html[] = '</ul>';
        }
        $html[] = '</fieldset>';
        $html[] = '</div>';
        $html[] = '<div class="clr"></div>';

        return $html;
    }
}
