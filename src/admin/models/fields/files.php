<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusFormFieldFiles extends JFormField
{
    protected function getInput()
    {
        JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);
        $this->addJavascript();


        $html = array(
            sprintf('<div id="%s" class="osc-file-manager">', $this->id),
            '<ul>'
        );

        foreach ((array)$this->value as $file) {
            $html = array_merge(
                $html,
                array(
                    '<li class="osc-file-block">',
                    $this->createButton('osc-file-ordering', 'fa-arrows'),
                    $this->createButton('osc-btn-warning-admin osc-file-delete', 'fa-times'),
                    $this->createTitle($file->title),
                    $this->createDescription($file->description),
                    $this->createUpload($file->path),
                    '</li>'
                )
            );
        }

        $html = array_merge(
            $html,
            array(
                '<li class="osc-file-block osc-file-add">',
                $this->createButton('osc-btn-main-admin', 'fa-plus', 'COM_OSCAMPUS_FILES_ADD'),
                '</li>',
                '</ul>',
                '</div>'
            )
        );

        return join('', $html);
    }

    protected function createTitle($value)
    {
        return sprintf(
            '<input type="text" name="%s[title][]" value="%s" size="40"/><br class="clr"/>',
            $this->name,
            htmlspecialchars($value)
        );
    }

    protected function createUpload($subValue)
    {
        $html = array(
            '<div class="fltlft">',
            sprintf('<input type="file" name="%s[path][]" value=""/>', $this->name),
            '<br class="clr"/>',
            $subValue,
            '</div>'
        );

        return join('', $html);
    }

    protected function createDescription($value)
    {
        return sprintf(
            '<textarea name="%s[description]">%s</textarea>',
            $this->name,
            htmlspecialchars($value)
        );
    }

    /**
     * Create the standard add/delete buttons
     *
     * @param string $class
     * @param string $icon
     * @param string $text
     *
     * @return string
     */
    protected function createButton($class, $icon, $text = null)
    {
        $button = sprintf(
            '<button type="button" class="%s"><i class="fa %s"></i>%s</button>',
            $class,
            $icon,
            ($text ? JText::_($text) : '')
        );

        return $button;
    }

    /**
     * Add all js needed for drag/drop ordering
     *
     * @rfeturn void
     */
    protected function addJavascript()
    {
        JHtml::_('osc.jquery');
        JHtml::_('osc.jui');
        JHtml::_('script', 'com_oscampus/admin/files.js', false, true);

        $options = json_encode(
            array(
                'container' => '#' . $this->id
            )
        );

        JHtml::_('osc.onready', "$.Oscampus.admin.files.init({$options});");

        JHtml::_('osc.onready', "$.Oscampus.admin.lesson.ordering({$options});");
    }
}
