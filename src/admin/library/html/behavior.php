<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

abstract class OscBehavior extends JHtmlBehavior
{
    /**
     * Wrapper for standard Joomla tooltips
     *
     * @param string $selector
     * @param array  $params
     *
     * @return void
     */
    public static function tooltip($selector = '.hasTooltip', $params = array())
    {
        if (version_compare(JVERSION, '3', 'lt')) {
            parent::tooltip($selector, $params);

        } else {
            JHtml::_('bootstrap.tooltip', $selector, $params);
        }
    }
}
