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
     * @param array  $sameLine
     *
     * @return string
     */
    public static function adminfields(
        JForm $form,
        $fieldSet,
        $legend = null,
        array $sameLine = array()
    ) {
        $html      = array();
        $fieldSets = $form->getFieldsets();

        if (!empty($fieldSets[$fieldSet])) {
            $name   = $fieldSets[$fieldSet]->name;
            $legend = $legend ?: $fieldSets[$fieldSet]->label;

            if (version_compare(JVERSION, '3.0', 'lt')) {
                $html = static::adminFieldsetJ2($form, $name, $legend, $sameLine);
            } else {
                $html = static::adminFieldsetJ3($form, $name, $legend, $sameLine);
            }
        }
        return join("\n", $html);
    }

    /**
     * Display selected fieldsets as tabbed areas in standard admin UI form
     *
     * @param JForm    $form
     * @param string[] $chosen
     *
     * @return string
     */
    public function admintabs(JForm $form, array $chosen = array())
    {

        $html = array();

        $html[] = JHtml::_('tabs.start', str_replace('.', '-', $form->getName()));

        $fieldSets = $form->getFieldsets();
        $chosen    = array_map('strtolower', $chosen);
        foreach ($fieldSets as $name => $fieldSet) {
            if (!$chosen || in_array(strtolower($name), $chosen)) {
                $html[] = JHtml::_('tabs.panel', JText::_($fieldSet->label), $name . '-panel');

                if (version_compare(JVERSION, '3', 'lt')) {
                    $html = array_merge($html, static::adminFieldsetJ2($form, $name));
                } else {
                    $html = array_merge($html, static::adminFieldsetJ3($form, $name));
                }

            }
        }

        $html[] = JHtml::_('tabs.end');

        return join('', $html);
    }

    /**
     * Render an admin form for Joomla! 3.x
     *
     * @param JForm  $form
     * @param string $name
     * @param string $legend
     * @param array  $sameLine
     *
     * @return array
     */
    protected static function adminFieldsetJ3(JForm $form, $name, $legend = null, array $sameLine = array())
    {
        $html   = array();
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

        return $html;
    }

    /**
     * Render an admin form for Joomla! 2.5
     *
     * @param JForm  $form
     * @param string $name
     * @param string $legend
     * @param array  $sameLine
     *
     * @return array
     */
    protected static function adminFieldsetJ2(JForm $form, $name, $legend = null, array $sameLine = array())
    {
        $html   = array();
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
