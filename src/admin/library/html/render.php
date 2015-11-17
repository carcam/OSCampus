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
     * @param bool   $tabbed
     * @param array  $sameLine
     *
     * @return string
     */
    public static function adminfields(JForm $form, $fieldSet, $tabbed = false, array $sameLine = array())
    {
        $html      = array();
        $fieldSets = $form->getFieldsets();

        if (!empty($fieldSets[$fieldSet])) {
            $name  = $fieldSets[$fieldSet]->name;
            $label = $fieldSets[$fieldSet]->label;

            if (version_compare(JVERSION, '3.0', 'lt')) {
                $html = static::adminFieldsetJ2($form, $name, $label, $tabbed, $sameLine);
            } else {
                $html = static::adminFieldsetJ3($form, $name, $label, $tabbed, $sameLine);
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
     * @param bool   $tabbed
     * @param array  $sameLine
     *
     * @return array
     */
    protected static function adminFieldsetJ3(JForm $form, $name, $label, $tabbed, array $sameLine)
    {
        $html = array();
        if ($tabbed) {
            $html[] = JHtml::_('bootstrap.addTab', 'myTab', $name, JText::_($label));
        }
        $html[] = '<div class="row-fluid">';
        $html[] = '<fieldset class="adminform">';

        foreach ($form->getFieldset($name) as $field) {
            if (in_array($field->fieldname, $sameLine)) {
                continue;
            }

            $fieldHtml = array(
                '<div class="control-group">',
                '<div class="control-label">',
                $field->label,
                '</div>',
                '<div class="controls">',
                $field->input
            );
            $html      = array_merge($html, $fieldHtml);

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
     * @param bool   $tabbed
     * @param array  $sameLine
     *
     * @return array
     */
    protected static function adminFieldsetJ2(JForm $form, $name, $label, $tabbed, array $sameLine)
    {
        $html = array();
        if ($tabbed) {
            $html[] = JHtml::_('tabs.panel', JText::_($label), $name . '-page');
        }
        $html[] = '<div class="width-100">';
        $html[] = '<fieldset class="adminform">';
        $html[] = '<ul class="adminformlist">';

        foreach ($form->getFieldset($name) as $field) {
            if (in_array($field->fieldname, $sameLine)) {
                continue;
            }

            $fieldHtml = array(
                '<li>' . $field->label . $field->input . '</li>'
            );
            $html      = array_merge($html, $fieldHtml);

            if (isset($sameLine[$field->fieldname])) {
                $html[] = ' ' . $form->getField($sameLine[$field->fieldname])->input;
            }

        }
        $endHtml = array(
            '</ul>',
            '</fieldset>',
            '</div>',
            '<div class="clr"></div>'
        );

        return array_merge($html, $endHtml);
    }
}
