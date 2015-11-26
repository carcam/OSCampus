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
            new Twig_SimpleFunction('html', '\JHtml::_')
        );
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('route', '\JRoute::_'),
            new Twig_SimpleFilter('lang', '\JText::_'),
            new Twig_SimpleFilter('sprintf', '\JText::sprintf')
        );
    }
}
