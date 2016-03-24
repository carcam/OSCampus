<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use OscampusFactory;
use JUri;
use JRoute;

class Joomla extends Twig_Extension
{
    public function getName()
    {
        return 'joomla';
    }

    public function getGlobals()
    {
        $app = OscampusFactory::getApplication();

        return array(
            'joomla_version' => JVERSION,
            'input'          => $app->input,
            'uri'            => JUri::getInstance()->toString()
        );
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('html', '\JHtml::_'),
            new Twig_SimpleFunction('linkto', array($this, 'functionLinkto'))
        );
    }

    /**
     * Link to an oscampus page by specifying urlvars
     *
     * @param array $urlvars
     *
     * @return string
     */
    public function functionLinkto(array $urlvars)
    {
        if (!isset($urlvars['option'])) {
            $urlvars = array_merge(
                array('option' => 'com_oscampus'),
                $urlvars
            );
        }

        return \JRoute::_('index.php?' . http_build_query($urlvars));
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('route', '\JRoute::_'),
            new Twig_SimpleFilter('lang', '\JText::_'),
            new Twig_SimpleFilter('sprintf', '\JText::sprintf'),
            new Twig_SimpleFilter('clean', array($this, 'filterFilter'))
        );
    }

    /**
     * Wrapper to string input filter
     *
     * @param string $string
     * @param string $command
     *
     * @return mixed
     */
    public function filterFilter($string, $command)
    {
        $filter = \OscampusFilterInput::getInstance();

        return $filter->clean($string, $command);
    }
}
