<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

if (!defined('OSCAMPUS_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_oscampus/include.php';
}

/**
 * Proxy for the class based router coming in Joomla 3
 *
 * @param $query
 *
 * @return array
 */
function OscampusBuildRoute(&$query)
{
    $router   = new OscampusRouter();
    $segments = $router->build($query);

    return $segments;
}

/**
 * Proxy for the class based router coming in Joomla 3
 *
 * @param $segments
 *
 * @return array
 */
function OscampusParseRoute($segments)
{
    $router = new OscampusRouter();
    $vars   = $router->parse($segments);

    return $vars;
}

/**
 * Class OscampusRouter
 *
 * As long as we support Joomla 2.5, this class will not inherit from
 * the new class-based Joomla 3 router. But this makes us ready!
 */
class OscampusRouter
{
    /**
     * @var array Sequence of segments mapped to corresponding view
     */
    protected $viewMap = array('pathway', 'course', 'lesson');

    /**
     * @param $query
     *
     * @return array
     */
    public function build(&$query)
    {
        $segments  = array();
        $routing   = OscampusRoute::getInstance();
        $menuQuery = $routing->getQuery('pathways');

        if (empty($query['Itemid'])) {
            if (!empty($menuQuery['Itemid'])) {
                $query['Itemid'] = $menuQuery['Itemid'];
            }
        }

        if (!empty($query['view'])) {
            $view = $query['view'];
            unset($query['view']);

        } elseif (!empty($menuQuery['view'])) {
            $view = $menuQuery['view'];

        } else {
            $view = 'pathways';
        }

        $pathwayId = empty($query['pid']) ? null : $query['pid'];
        $courseId  = empty($query['cid']) ? null : $query['cid'];

        if ($pathwayId && ($pathway = $routing->getPathwaySlug($pathwayId))) {
            $segments[] = $pathway;
            unset($query['pid']);

            if ($courseId && ($course = $routing->getCourseSlug($courseId))) {
                $segments[] = $course;
                unset($query['cid']);

                if ($view == 'lesson' && $courseId && isset($query['idx'])) {
                    $lessonIndex = (int)$query['idx'];
                    $lesson      = $routing->getLessonSlug($courseId, $lessonIndex);
                    $segments[]  = $lesson;
                    unset($query['idx']);
                }
            }
        }

        return $segments;
    }

    /**
     * @param $segments
     *
     * @return array
     */
    public function parse($segments)
    {
        $routing = OscampusRoute::getInstance();
        $vars    = array();

        if (!empty($segments[0])) {
            $viewIndex    = min(2, count($segments) - 1);
            $vars['view'] = $this->viewMap[$viewIndex];

            $vars['pid'] = $routing->getPathwayFromSlug($segments[0]);

            if (!empty($segments[1])) {
                $vars['cid'] = $routing->getCourseFromSlug($segments[1]);

                if (!empty($segments[2])) {
                    $vars['idx'] = $routing->getLessonFromSlug($segments[2], $vars['cid']);
                }
            }
        }
        return $vars;
    }
}
