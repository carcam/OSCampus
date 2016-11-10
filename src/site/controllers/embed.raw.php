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

    /**
     * Ajax endpoint for converting URLs to embedded content
     *
     * @throws Exception
     */
    public function content()
    {
        if (!JSession::checkToken()) {
            /*
             * @TODO: This is a bit of punt.
             * This is a frontend controller and form tokens between
             * admin and front are not shared. So when the token check fails, we check to
             * see if this came from our own admin site to validate. Hopefully there is a better
             * way to do this. If we'll implement when we find it.
             */
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

    /**
     * Generate standard alert html. Specifically designed for use in admin.
     * Should it ever be used in the frontend may work as is without modification.
     *
     * @param string $message
     * @param string $class
     * @param string $icon
     *
     * @return string
     */
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
