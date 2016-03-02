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
        JHtml::_('stylesheet', 'com_oscampus/admin.css', null, true);
        JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);
        $this->addJavascript();


        $html = array(
            sprintf('<div id="%s" class="osc-file-manager">', $this->id),
            '<ul>'
        );

        $files = (array)$this->value ?: array(null);
        foreach ($files as $file) {
            $html[] = $this->createFileBlock($file);
        }

        $html = array_merge(
            $html,
            array(
                '</ul>',
                $this->createButton('osc-btn-main-admin osc-file-add', 'fa-plus', 'COM_OSCAMPUS_FILES_ADD'),
                '</div>'
            )
        );

        return join('', $html);
    }

    protected function createFileBlock($file = null)
    {
        $id = sprintf(
            '<input type="hidden" name="%s[id][]" value="%s"/>',
            $this->name,
            empty($file->id) ? '' : $file->id
        );

        $title = sprintf(
            '<input type="text" name="%s[title][]" value="%s" size="40"/><br class="clr"/>',
            $this->name,
            empty($file->title) ? '' : htmlspecialchars($file->title)
        );

        $description = sprintf(
            '<textarea name="%s[description]">%s</textarea>',
            $this->name,
            empty($file->description) ? '' : htmlspecialchars($file->description)
        );

        $filePath = sprintf(
            '<input type="text" name="%s[path][]" value="%s" readonly="readonly" class="readonly"/>',
            $this->name,
            empty($file->path) ? '' : $file->path
        );

        $upload = '<div class="osc-file-browse">'
            . sprintf('<input type="file" name="%s[path][]" value=""/>', $this->name)
            . '<br/>' . $filePath
            . '</div>';

        $html = '<li class="osc-file-block">'
            . $id
            . $this->createButton('osc-file-ordering', 'fa-arrows')
            . $this->createButton('osc-btn-warning-admin osc-file-delete', 'fa-times')
            . $title
            . $description
            . $upload
            . '</li>';

        return $html;
    }

    /**
     * Create a standard button
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
            '<button type="button" class="%s"><i class="fa %s"></i> %s</button>',
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
    }
}
