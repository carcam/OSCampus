<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

abstract class OscampusRoute
{
    /**
     * @var array
     */
    protected static $items = null;

    /**
     * Get a raw url for the selected view,layout
     *
     * @param string $view
     * @param string $layout
     *
     * @return string|array
     */
    public static function get($view, $layout = null)
    {
        if ($query = static::getQuery($view, $layout)) {
            return 'index.php?' . http_build_query($query);
        }
        return null;
    }

    /**
     * Build the correct link to a com_content article
     *
     * @param $articleId
     *
     * @return string
     * @throws Exception
     */
    public static function fromArticleId($articleId)
    {
        $contentId = OscampusComponentHelper::getComponent('com_content')->id;
        $menuItems = JFactory::getApplication()->getMenu()->getItems('component_id', $contentId);

        $link = 'index.php?option=com_content&view=article&id=' . $articleId;
        foreach ($menuItems as $item) {
            list(, $query) = explode('?', $item->link);
            parse_str($query, $query);

            if (!empty($query['article']) && !empty($query['id']) && $query['id'] == $articleId) {
                $link = $item->link;
            }
        }

        return $link;
    }

    /**
     * Get the query array for the selected view,layout
     *
     * @param string $view
     * @param string $layout
     *
     * @return array
     */
    public static function getQuery($view, $layout = '')
    {
        $query = array(
            'option' => 'com_oscampus',
            'view'   => $view
        );
        if ($layout) {
            $query['layout'] = $layout;
        }

        // Stay on active menu if it matches what we're looking for
        if ($activeMenu = OscampusFactory::getApplication()->getMenu()->getActive()) {
            $activeQuery  = $activeMenu->query;
            $activeView   = isset($activeQuery['view']) ? $activeQuery['view'] : '';
            $activeLayout = isset($activeQuery['layout']) ? $activeQuery['layout'] : '';
            if ($activeView == $view && $activeLayout == $layout) {
                $query['Itemid'] = $activeMenu->id;
            }
        }

        // If not active menu, go find one!
        if (empty($query['Itemid'])) {
            if (static::$items === null) {
                $menu          = OscampusHelper::getApplication('site')->getMenu();
                static::$items = $menu->getItems(array('component', 'access'), array('com_oscampus', true));
            }

            $viewLevels = OscampusFactory::getUser()->getAuthorisedViewLevels();

            // Look for an existing menu item that matches the requests
            foreach (static::$items as $item) {
                $mView   = empty($item->query['view']) ? '' : $item->query['view'];
                $mLayout = empty($item->query['layout']) ? '' : $item->query['layout'];
                $access  = in_array($item->access, $viewLevels);

                if ($access && $mView == $view && $mLayout == $layout) {
                    $query['Itemid'] = $item->id;
                    if (!empty($query['view']) && $query['view'] == $view) {
                        unset($query['view']);
                    }
                    if (!empty($query['layout']) && $query['layout'] == $layout) {
                        unset($query['layout']);
                    }
                    break;

                } elseif ($access && $mView == 'account' && empty($mLayout)) {
                    // The account info view can always be used as a base
                    $query['Itemid'] = $item->id;
                }
            }
        }

        return $query;
    }
}
