<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
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
     * @param mixed[] $query
     *
     * @return string[]
     */
    public function build(&$query)
    {
        $segments = array();
        $route    = OscampusRoute::getInstance();

        if (!empty($query['view'])) {
            $view = $query['view'];
            unset($query['view']);

        } elseif (!empty($query['Itemid'])) {
            $menu = OscampusFactory::getApplication()->getMenu()->getItem($query['Itemid']);

            if ($menu->component == 'com_oscampus') {
                $view = $menu->query['view'];
            }
        }

        if (!empty($view)) {
            if ($view == 'certificate') {
                // Certificates should be rooted on 'mycertificates' view
                $menuQuery = $route->getQuery('mycertificates');

                $id = isset($query['id']) ? (int)$query['id'] : null;
                if ($id) {
                    unset($query['id']);
                    $segments[] = $id;
                }

                if (isset($query['format'])) {
                    unset($query['format']);
                }

                if (empty($query['Itemid'])
                    || (!empty($menuQuery['Itemid']) && $query['Itemid'] != $menuQuery['Itemid'])
                ) {
                    $query['Itemid'] = $menuQuery['Itemid'];
                }

            } elseif (in_array($view, array('course', 'lesson'))) {
                $courseId    = isset($query['cid']) ? (int)$query['cid'] : null;
                $lessonId    = isset($query['lid']) ? (int)$query['lid'] : null;
                $lessonIndex = isset($query['index']) ? (int)$query['index'] : null;

                $menuQuery       = $route->getQuery('course');
                $query['Itemid'] = $menuQuery['Itemid'];

                if ($courseId && ($course = $route->getCourseSlug($courseId))) {
                    $segments[] = $course;
                    unset($query['cid']);

                    if ($view == 'lesson') {
                        try {
                            if ($lessonId) {
                                $lesson = $route->getLessonSlug($lessonId);
                                unset($query['lid']);
                                if (isset($query['index'])) {
                                    unset($query['index']);
                                }

                            } elseif ($courseId) {
                                $lesson = $route->getLessonSlug($courseId, $lessonIndex);
                                unset($query['index']);
                                if (isset($query['lid'])) {
                                    unset($query['lid']);
                                }
                            }
                            if (!empty($lesson)) {
                                $segments[] = $lesson;
                            }

                        } catch (Exception $e) {
                            // The selected lesson probably doesn't exist. This will take us to the course homepage
                            if (isset($query['index'])) {
                                unset($query['index']);
                            }
                            if (isset($query['lid'])) {
                                unset($query['lid']);
                            }
                        }
                    }
                }

            } elseif (in_array($view, array('pathways', 'pathway'))) {
                $pathwayId = isset($query['pid']) ? (int)$query['pid'] : null;

                if ($pathwayId && ($pathway = $route->getPathwaySlug($pathwayId))) {
                    $segments[] = $pathway;
                    unset($query['pid']);
                }

                $menuQuery       = $route->getQuery('pathways');
                $query['Itemid'] = $menuQuery['Itemid'];

            } else {
                $query      = $route->getQuery($view);
                if (!empty($query['view'])) {
                    unset($query['view']);
                }
            }
        }

        return $segments;
    }

    /**
     * @param string[] $segments
     *
     * @return mixed[]
     */
    public function parse($segments)
    {
        $app   = OscampusFactory::getApplication();
        $menu  = $app->getMenu()->getActive();
        $route = OscampusRoute::getInstance();

        $view = $app->input->getCmd('view');
        if (!$view && !empty($menu) && $menu->component == 'com_oscampus') {
            $view = $menu->query['view'];
        }

        $vars = array(
            'option' => 'com_oscampus'
        );

        if (!empty($segments[0])) {
            if ($view == 'mycertificates') {
                $vars['view'] = 'certificate';

                $vars['id']     = (int)$segments[0];
                $vars['format'] = 'pdf';

            } elseif ($view == 'course') {
                $vars['cid'] = $route->getCourseFromSlug($segments[0]);
                if (empty($segments[1])) {
                    $vars['view'] = 'course';
                } else {
                    $vars['lid']  = $route->getLessonFromSlug($segments[1], $vars['cid']);
                    $vars['view'] = 'lesson';
                }

            } elseif ($view == 'pathways') {
                $vars['view'] = 'pathway';
                $vars['pid']  = $route->getPathwayFromSlug($segments[0]);

            } else {
                $vars['view'] = $segments[0];
            }
        }

        return $vars;
    }
}
