<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

function OscampusBuildRoute(&$query)
{
    $segments = array();

    $view = isset($query['view']) ? $query['view'] : null;
    $layout = isset($query['layout']) ? $query['layout'] : null;

    switch ($view) {
        case 'pathway':
            if (isset($query['pid'])) {
                $segments[] = 'categories';
                $segments[] =
            }
        case 'course':
        case 'lesson':
        case 'pathways':
        default:
    }

    return $segments;
}

function OscampusParseRoute($segments)
{
    $vars = array();

    return $vars;
}
