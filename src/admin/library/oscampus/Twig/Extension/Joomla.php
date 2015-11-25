<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use OscampusFactory;
use OscampusTable;
use JUri;
use JRoute;
use JText;


class Joomla extends Twig_Extension
{
    public function getName()
    {
        return 'joomla';
    }

    public function getGlobals()
    {
        $app = OscampusFactory::getApplication();

        $joomla2 = version_compare(JVERSION, '3', 'lt') && version_compare(JVERSION, '2', 'ge');
        $joomla3 = version_compare(JVERSION, '4', 'lt') && version_compare(JVERSION, '3', 'ge');

        return array(
            'joomla_version' => JVERSION,
            'joomla2'        => $joomla2,
            'joomla3'        => $joomla3,
            'input'          => $app->input,
            'uri'            => JUri::getInstance()->toString()
        );
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('html', '\Oscampus\Twig\Extension\Joomla::function_html'),
            new Twig_SimpleFunction('sprintf', '\JText::sprintf')
        );
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('route', '\Oscampus\Twig\Extension\Joomla::filter_route'),
            new Twig_SimpleFilter('lang', '\Oscampus\Twig\Extension\Joomla::filter_lang')
        );
    }

    public static function function_html()
    {
        $args = func_get_args();

        return call_user_func_array('\JHtml::_', $args);
    }

    public static function filter_route($string)
    {
        return JRoute::_($string);
    }

    public static function filter_lang($string)
    {
        return JText::_($string);
    }
}
