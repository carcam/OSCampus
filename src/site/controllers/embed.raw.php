<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerEmbed extends OscampusControllerBase
{
    public function display()
    {
        throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
    }

    public function content()
    {
        if (!JSession::checkToken()) {
            $referer = new JUri($_SERVER['HTTP_REFERER']);
            $uri     = JUri::getInstance();

            if ($uri->getHost() != $referer->getHost() || !preg_match('#^/administrator#', $referer->getPath())) {
                throw new Exception($this->getMessage(JText::_('JINVALID_TOKEN'), 'error', 'warning'), 403);
            }
        }

        $app = JFactory::getApplication();

        $embeddableUrl = $app->input->getString('url');
        $preview       = JHtml::_('content.prepare', $embeddableUrl);

        if ($embeddableUrl == $preview) {
            // No plugins converted the url
            $lang = JFactory::getLanguage()->load('com_oscampus', JPATH_COMPONENT_ADMINISTRATOR);
            echo $this->getMessage(JText::sprintf('COM_OSCAMPUS_EMBED_ADMIN_UNRECOGNIZED', $embeddableUrl), 'warning');
        } else {
            echo $preview;
        }
    }

    protected function getMessage($message, $class = 'info', $icon = null)
    {
        $icon = $icon ?: 'info';
        return sprintf(
            '<div class="alert alert-%s"><span class="icon-%s"></span> %s</div>',
            $class,
            $icon,
            $message
        );
    }
}
