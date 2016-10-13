<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldType('Url');

class OscampusFormFieldEmbed extends JFormFieldUrl
{
    protected static $javascriptLoaded = false;

    protected function getInput()
    {
        $this->addJavascript();

        $html = '<div class="input-append">'
            . parent::getInput()
            . $this->getPreview()
            . '</div>';

        return $html;
    }

    protected function getPreview()
    {
        $button = sprintf(
            '<a id="%s_btn" class="%s" title="%s"><span class="%s"></span></a>',
            $this->id,
            'btn btn-primary btn-select',
            JText::_('COM_OSCAMPUS_EMBED_PREVIEW_BUTTON_TEXT'),
            'icon-eye-open'
        );

        $previewPane = sprintf(
            '<div id="%s_preview" style="%s"></div>',
            $this->id,
            'clear: both; margin-top: 5px; font-size: 13px;'
        );

        return $button . $previewPane;
    }

    /**
     * Add all js needed for drag/drop ordering
     *
     * @rfeturn void
     */
    protected function addJavascript()
    {
        $classes     = preg_split('/\s+/', $this->class);
        $classes[]   = 'osc-url-embed-field';
        $this->class = join(' ', array_unique($classes));

        if (!static::$javascriptLoaded) {
            JHtml::_('osc.jquery');
            JHtml::_('script', 'com_oscampus/admin/embed.js', false, true);

            JHtml::_('osc.onready', "$.Oscampus.admin.embed.init();");

            static::$javascriptLoaded = true;
        }
    }
}

