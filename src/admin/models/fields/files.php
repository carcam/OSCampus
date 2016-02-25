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

        $html = array(
            sprintf('<div id="%s" class="oscampus-files">', $this->id),
        );

        foreach ((array)$this->value as $file) {
            $html[] = '<div class="oscampus-file-block">';
            $html[] = $this->createButton('osc-btn-warning-admin osc-delete-file', 'fa-times');

            $html[] = $this->createTitle($file->title);
            $html[] = $this->createDescription($file->description);
            $html[] = $this->createUpload($file->path);
            $html[] = '</div>';
            $html[] = '<div class="clr"></div>';
        }

        $html[] = '<div class="oscampus-file-block">';
        $html[] = $this->createButton('osc-btn-main-admin', 'fa-plus', 'COM_OSCAMPUS_FILES_ADD');
        $html[] = '</div>';

        $html[] = '</div>';

        return join("\n", $html);
    }

    protected function createTitle($value)
    {
        return sprintf(
            '<input type="text" name="%s[title][]" value="%s" size="40"/><br class="clr"/>',
            $this->name,
            htmlspecialchars($value)
        );
    }

    protected function createUpload($value)
    {
        $html = array(
            '<div class="fltlft">',
            sprintf('<input type="file" name="%s[path][]" value=""/>', $this->name),
            '<br class="clr"/>',
            $value,
            '</div>'
        );

        return join("\n", $html);
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
        $button = '<button'
            . ' type="button"'
            . ' class="' . $class . '">'
            . '<i class="fa ' . $icon . '"></i>'
            . ($text ? ' ' . JText::_($text) : '')
            . '</button>';

        return $button;
    }
}
