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
     * @var array Converts view names into segment names for b/c with guru sef
     */
    protected $viewMap = array(
        'pathway' => 'categories',
        'course'  => 'class',
        'lesson'  => 'session'
    );

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

        $view = '';
        if (!empty($query['view'])) {
            $view = $query['view'];
            unset($query['view']);
        }

        if (empty($view) && !empty($menuQuery['view'])) {
            $view = $menuQuery['view'];
        }

        $pathwayId = empty($query['pid']) ? null : $query['pid'];
        $courseId  = empty($query['cid']) ? null : $query['cid'];

        if ($view && !empty($this->viewMap[$view])) {
            if ($pathwayId && ($pathway = $routing->getPathwaySlug($pathwayId))) {
                $segments[] = $this->viewMap[$view];
                $segments[] = $pathway;
                unset($query['pid']);

                if ($courseId && ($course = $routing->getCourseSlug($courseId))) {
                    $segments[] = $course;
                    unset($query['cid']);

                    if ($view == 'lesson' && $courseId && isset($query['idx'])) {
                        $lessonIndex = (int)$query['idx'];
                        $module      = $routing->getModuleSlug($courseId, $lessonIndex);
                        $segments[]  = $module;

                        $lesson     = $routing->getLessonSlug($courseId, $lessonIndex);
                        $segments[] = $lesson;
                        unset($query['idx']);
                    }
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

        if (count($segments) > 0) {
            $vars['view'] = array_search($segments[0], $this->viewMap);
        }

        if (count($segments) > 1) {
            $vars['pid'] = $routing->getPathwayFromSlug($segments[1]);
        }

        if (count($segments) > 2) {
            $vars['cid'] = $routing->getCourseFromSlug($segments[2]);
        }

        if (count($segments) > 4 && !empty($vars['cid'])) {
            $vars['idx'] = $routing->getLessonFromSlug($segments[4], $vars['cid']);
        }

        return $vars;
    }
}
