<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusRoute
{
    const SLUG_PATHWAY = 'pathways';
    const SLUG_COURSE  = 'courses';
    const SLUG_MODULE  = 'modules';
    const SLUG_LESSON  = 'lessons';

    /**
     * @var static
     */
    protected static $instance = null;

    /**
     * @var array
     */
    protected $items = null;

    /**
     * @var array
     */
    protected $articleItems = null;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Get a raw url for the selected view,layout
     *
     * @param string $view
     * @param string $layout
     *
     * @return string|array
     */
    public function get($view, $layout = null)
    {
        if ($query = $this->getQuery($view, $layout)) {
            return 'index.php?' . http_build_query($query);
        }
        return null;
    }

    /**
     * Find an OSCampus menu item to use as a base for the selected view/layout
     *
     * @param string $view
     * @param string $layout
     *
     * @return object
     */
    public function getMenu($view, $layout = null)
    {
        // Use active menu if it matches what we're looking for
        if ($activeMenu = OscampusFactory::getApplication()->getMenu()->getActive()) {
            $activeQuery = $activeMenu->query;
            if (isset($activeQuery['option']) && $activeQuery['option'] == 'com_oscampus') {
                $activeView   = isset($activeQuery['view']) ? $activeQuery['view'] : '';
                $activeLayout = isset($activeQuery['layout']) ? $activeQuery['layout'] : '';
                if ($activeView == $view && $activeLayout == $layout) {
                    return $activeMenu;
                }
            }
        }

        if ($this->items === null) {
            $menu        = JMenu::getInstance('site');
            $this->items = $menu->getItems(array('component', 'access'), array('com_oscampus', true));
        }

        $viewLevels = OscampusFactory::getUser()->getAuthorisedViewLevels();

        $default = null;
        foreach ($this->items as $item) {
            $mView   = empty($item->query['view']) ? '' : $item->query['view'];
            $mLayout = empty($item->query['layout']) ? '' : $item->query['layout'];
            $access  = in_array($item->access, $viewLevels);

            if ($access && $mView == $view && $mLayout == $layout) {
                // Found an exact match
                return $item;

            } elseif ($access && $mView == 'pathways' && empty($mLayout)) {
                // The pathways view can always be used as a base
                $default = $item;
            }
        }
        return $default;
    }

    /**
     * Build the correct link to a com_content article
     *
     * @param $articleId
     *
     * @return string
     * @throws Exception
     */
    public function fromArticleId($articleId)
    {
        if ($this->articleItems === null) {
            $contentId          = OscampusComponentHelper::getComponent('com_content')->id;
            $this->articleItems = JMenu::getInstance('site')->getItems('component_id', $contentId);
        }

        $link = 'index.php?option=com_content&view=article&id=' . $articleId;
        foreach ($this->articleItems as $item) {
            list(, $query) = explode('?', $item->link);
            parse_str($query, $query);

            if (!empty($query['article']) && !empty($query['id']) && $query['id'] == $articleId) {
                $link = $item->link;
            }
        }

        return $link;
    }

    /**
     * Get the query array for the selected view/layout
     *
     * @param string $view
     * @param string $layout
     *
     * @return array
     */
    public function getQuery($view, $layout = '')
    {
        $query = array(
            'option' => 'com_oscampus',
            'view'   => $view
        );
        if ($layout) {
            $query['layout'] = $layout;
        }

        if ($menuItem = static::getMenu($view, $layout)) {
            $query['Itemid'] = $menuItem->id;

            $mView = empty($menuItem->query['view']) ? '' : $menuItem->query['view'];
            if ($mView == $view) {
                unset($query['view']);
            }

            $mLayout = empty($menuItem->query['layout']) ? '' : $menuItem->query['layout'];
            if ($layout && $mLayout == $layout) {
                unset($query['layout']);
            }
        }

        return $query;
    }

    /**
     * Wrapper function for static::getSlug()
     *
     * @param int $id
     *
     * @return string
     * @throws Exception
     */
    public function getPathwaySlug($id)
    {
        return $this->getSlug(static::SLUG_PATHWAY, $id);
    }

    /**
     * Wrapper function for static::getSlug()
     *
     * @param int $id
     *
     * @return string
     * @throws Exception
     */
    public function getCourseSlug($id)
    {
        return $this->getSlug(static::SLUG_COURSE, $id);
    }

    /**
     * Wrapper function for static::getSlug()
     *
     * @param int $id    The course ID
     * @param int $index The lesson index as ordered within the course
     *
     * @return string
     * @throws Exception
     */
    public function getModuleSlug($id, $index)
    {
        return $this->getSlug(static::SLUG_MODULE, $id, $index);
    }

    /**
     * Wrapper function for static::getSlug()
     *
     * @param int $id    The course ID
     * @param int $index The lesson index as ordered within the course
     *
     * @return string
     * @throws Exception
     */
    public function getLessonSlug($id, $index)
    {
        return $this->getSlug(static::SLUG_LESSON, $id, $index);
    }

    /**
     * Find the url slug for the selected item type and id
     *
     * @param string $type  See static::SLUG_* constants
     * @param int    $id    The id of the target item. For modules/lessons this is the courses_id
     * @param int    $index For modules/lessons, the index of the lesson as ordered in the course.
     *
     * @return string
     * @throws Exception
     */
    public function getSlug($type, $id, $index = 0)
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $select = null;
        $id     = (int)$id;
        $index  = (int)$index;

        switch ($type) {
            case static::SLUG_PATHWAY:
            case static::SLUG_COURSE:
                $index = 0;
                $table = '#__oscampus_' . $type;
                $query->select('title, alias')
                    ->from($table)
                    ->where('id = ' . (int)$id);
                break;

            /** @noinspection PhpMissingBreakStatementInspection */
            case static::SLUG_MODULE:
                $select = 'm.title, m.alias';

            case static::SLUG_LESSON:
                $select = $select ?: 'l.title, l.alias';

                $query->select($select)
                    ->from('#__oscampus_lessons l')
                    ->innerJoin('#__oscampus_modules m ON m.id = l.modules_id')
                    ->where('m.courses_id = ' . (int)$id)
                    ->order('m.ordering, l.ordering');

                break;

            default:
                throw new Exception(__CLASS__ . ": Unknown routing path - {$type}", 404);
        }

        if ($item = $db->setQuery($query, $index, 1)->loadObject()) {
            return $item->alias;
        }

        throw new Exception(__CLASS__ . ": not found - {$id}/{$index} ({$type})", 404);
    }

    /**
     * Get the pathway id from an alias slug
     *
     * @param string $slug
     *
     * @return int
     */
    public function getPathwayFromSlug($slug)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__oscampus_pathways')
            ->where('alias = ' . $db->quote($slug));

        $id = $db->setQuery($query)->loadResult();

        return (int)$id;
    }

    /**
     * Get course ID from a category alias slug
     *
     * @param string $slug
     *
     * @return int
     */
    public function getCourseFromSlug($slug)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__oscampus_courses')
            ->where('alias = ' . $db->quote($slug));

        $id = $db->setQuery($query)->loadResult();

        return (int)$id;
    }

    /**
     * Get the lesson index within the requested course
     *
     * @param string $slug
     * @param int    $courseId
     *
     * @return int
     */
    public function getLessonFromSlug($slug, $courseId)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('l.id, l.alias')
            ->from('#__oscampus_lessons l')
            ->innerJoin('#__oscampus_modules m ON m.id = l.modules_id')
            ->where('m.courses_id = ' . (int)$courseId)
            ->order('m.ordering, l.ordering');

        $lessons = $db->setQuery($query)->loadObjectList();
        foreach ($lessons as $index => $lesson) {
            if ($lesson->alias == $slug) {
                return (int)$index;
            }
        }

        // Default to the first lesson
        return 0;
    }
}
