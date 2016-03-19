<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use JRegistry as Registry;

defined('_JEXEC') or die();

abstract class OscampusHelperSite
{
    protected static $googleFonts = 'https://fonts.googleapis.com';

    /**
     * @var Registry
     */
    protected static $params = null;

    /**
     * @return Registry
     */
    public static function getParams()
    {
        if (static::$params === null) {
            static::$params = OscampusComponentHelper::getParams();
        }
        return static::$params;
    }

    /**
     * Load all theming/styling items
     * - Awesome Icon font
     * - Google Font
     * - Selected Theme
     *
     * @param null $theme
     */
    public static function loadTheme($theme = null)
    {
        $params = static::getParams();

        // Load the selected font
        $font = explode('|', $params->get('themes.fontFamily', 'none'));
        if ($font[0] != 'none') {

            /* Load Google fonts files when font-weight exists
            *  Example: "Droid Sans|sans-serif|400,700"
            *  400,700 is the font-weight */
            if (count($font) > 2) {
                $href = static::$googleFonts . '/css?family=' . $font[0] . ':' . $font[2];
                JHtml::_('stylesheet', $href);
            }

            // Assign font-family to specific tags
            $style = array(
                '.osc-container p,',
                '.osc-container h1,',
                '.osc-container h2,',
                '.osc-container h3,',
                '.osc-container div,',
                '.osc-container li,',
                '.osc-container span,',
                '.osc-container label,',
                '.osc-container td,',
                '.osc-container input,',
                '.osc-container button,',
                '.osc-container textarea,',
                '.osc-container select {',
                "   font-family: '" . $font[0] . "', " . $font[1] . ';',
                '}'
            );
            JFactory::getDocument()->addStyleDeclaration(join("\n", $style));
        }

        // Load font Awesome
        if ($params->get('themes.fontAwesome', true)) {
            JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);
        }

        // Load responsive grids
        JHtml::_('stylesheet', 'com_oscampus/grid.css', null, true);
        JHtml::_('stylesheet', 'com_oscampus/grid-responsive.css', null, true);
        JHtml::_('stylesheet', 'com_oscampus/style.css', null, true);

        // Load the selected theme
        if ($theme === null) {
            $theme = $params->get('themes.theme', 'default.css');
        }
        if ($theme != 'none') {
            JHtml::_('stylesheet', 'com_oscampus/themes/' . $theme, null, true);
        }
    }
}
