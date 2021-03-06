<?php
/**
 * @package   OSMap
 * @copyright 2007-2014 XMap - Joomla! Vargas. All rights reserved.
 * @copyright 2016 Open Source Training, LLC. All rights reserved..
 * @author    Guillermo Vargas <guille@vargas.co.cr>
 * @author    Joomlashack <help@joomlashack.com>
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL   GNU General Public License version 2 or later; see
 *            LICENSE.txt
 *
 * This file is part of OSMap.
 *
 * OSMap is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * OSMap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSMap. If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');

class osmap_com_oscampus
{
    /*
    * @var string Value for $_GET param 'option'
    */
    protected static $option = 'com_oscampus';

    /*
    * @var array view types to add links too
    */
    protected static $views = array('pathways', 'course');

    /*
    * @var boolean stores if plugin is enabled
    */
    private static $enabled = null;

    /*
    * @var object stores instance of self
    */
    private static $instance = null;

    /*
    * @return instance of this class
    */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            $instance = new self;

            static::$instance = $instance;
        }

        return static::$instance;
    }

    /*
    * Runs through tree, checks if view is equal with
    * current link then runs method for that view
    *
    * @param OSMapXmlDisplayer $osmap
    * @param stdClass $parent (current osmap link)
    * @param array $params (values form admin settings)
    */
    public function getTree($osmap, $parent, &$params)
    {
        $uri = new JUri($parent->link);

        if (!static::isEnabled()
            || !in_array($uri->getVar('view'), static::$views)
            || $uri->getVar('option') !== static::$option
        ) {
            return;
        }

        $params['groups'] = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

        $params['language_filter'] = JFactory::getApplication()->getLanguageFilter();

        $params['include_links'] = JArrayHelper::getValue($params, 'include_links', 1);
        $params['include_links'] = ($params['include_links'] == 1
            || ($params['include_links'] == 2 && $osmap->view == 'xml')
            || ($params['include_links'] == 3 && $osmap->view == 'html')
        );

        $params['show_unauth'] = JArrayHelper::getValue($params, 'show_unauth', 0);
        $params['show_unauth'] = ($params['show_unauth'] == 1
            || ($params['show_unauth'] == 2 && $osmap->view == 'xml')
            || ($params['show_unauth'] == 3 && $osmap->view == 'html')
        );

        $params['category_priority']   = JArrayHelper::getValue($params, 'category_priority', $parent->priority);
        $params['category_changefreq'] = JArrayHelper::getValue($params, 'category_changefreq', $parent->changefreq);

        if ($params['category_priority'] == -1) {
            $params['category_priority'] = $parent->priority;
        }

        if ($params['category_changefreq'] == -1) {
            $params['category_changefreq'] = $parent->changefreq;
        }

        $params['link_priority']   = JArrayHelper::getValue($params, 'link_priority', $parent->priority);
        $params['link_changefreq'] = JArrayHelper::getValue($params, 'link_changefreq', $parent->changefreq);

        if ($params['link_priority'] == -1) {
            $params['link_priority'] = $parent->priority;
        }

        if ($params['link_changefreq'] == -1) {
            $params['link_changefreq'] = $parent->changefreq;
        }
        switch ($uri->getVar('view')) {
            case 'pathways':
                static::printPathwayLinks($osmap, $parent, $params);
                break;

            case 'course':
                static::getCourselinks($osmap, $parent, $params);
                break;
        }
    }

    /*
    * Prints Links that are associated with pathways to osmap
    *
    * @param OSMapXmlDisplayer $osmap
    * @param stdClass $parent (current osmap link)
    * @param array $params (values form admin settings)
    */
    protected static function printPathwayLinks($osmap, $parent, $params)
    {
        if (!$params['include_links']) {
            return;
        }

        $db            = JFactory::getDbo();
        $viewLevels    = JFactory::getUser()->getAuthorisedViewLevels();
        $pathwaysQuery = $db->getQuery(true)
            ->select(
                array(
                    'id',
                    'title'
                )
            )
            ->from('#__oscampus_pathways')
            ->where(
                array(
                    'published = 1',
                    'access IN (' . join(',', $viewLevels) . ')'
                )
            );

        $pathwayItems = $db->setQuery($pathwaysQuery)->loadObjectList();

        if (empty($pathwayItems)) {
            return;
        }

        foreach ($pathwayItems as $pathwayItem) {
            $osmap->changeLevel(1);
            $node             = new stdclass;
            $node->id         = $parent->id;
            $node->name       = $pathwayItem->title;
            $node->uid        = $parent->uid . '_' . $pathwayItem->id;
            $node->browserNav = $parent->browserNav;
            $node->priority   = $params['link_priority'];
            $node->changefreq = $params['link_changefreq'];
            $node->link       = 'index.php?option=' . static::$option . '&view=pathways&pid=' . $pathwayItem->id;
            $osmap->printNode($node);
            $osmap->changeLevel(-1);
        }

    }

    /*
    * Gets course links (along with lesson links)
    * that need to be printed to osmap
    *
    * @param OSMapXmlDisplayer $osmap
    * @param stdClass $parent (current osmap link)
    * @param array $params (values form admin settings)
    */
    protected static function getCourseLinks($osmap, $parent, $params)
    {
        if (!$params['include_links']) {
            return;
        }

        $db                = JFactory::getDbo();
        $viewLevels        = JFactory::getUser()->getAuthorisedViewLevels();
        $courseLessonQuery = $db->getQuery(true)
            ->select(
                array(
                    'cp.courses_id',
                    'course.title AS courseTitle',
                    'lesson.id',
                    'lesson.title AS lessonTitle',
                    'lesson.published'
                )
            )
            ->from('#__oscampus_pathways AS pathway')
            ->innerJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = cp.courses_id')
            ->innerJoin('#__oscampus_modules AS module ON module.courses_id = course.id')
            ->innerJoin('#__oscampus_lessons AS lesson ON lesson.modules_id = module.id')
            ->where(
                array(
                    'pathway.published = 1',
                    'course.published = 1',
                    'course.access IN (' . join(',', $viewLevels) . ')',
                    'course.released <= NOW()'
                )
            );

        $courseItems = $db->setQuery($courseLessonQuery)->loadObjectList();

        if (empty($courseItems)) {
            return;
        }

        $classItems  = array();
        $lessonItems = array();
        foreach ($courseItems as $courseItem) {
            $cid          = $courseItem->courses_id;
            $lessonPub    = $courseItem->published;
            $lid          = $courseItem->id;
            $classItems[] = array(
                'option' => static::$option,
                'view'   => 'course',
                'cid'    => $cid,
                'cTitle' => $courseItem->courseTitle
            );

            if ($lessonPub === '1') {
                $lessonItems[] = array(
                    'option' => static::$option,
                    'view'   => 'lesson',
                    'cid'    => $cid,
                    'lid'    => $lid,
                    'lTitle' => $courseItem->lessonTitle
                );
            }
        }
        static::printCourseLinks($osmap, $parent, $params, $classItems, $lessonItems);

    }

    /*
    * Prints links that are classes and lessons to osmap
    * Also orders them in class and then lessons associated with that class
    *
    * @param OSMapXmlDisplayer $osmap
    * @param stdClass $parent (current osmap link)
    * @param array $params (values form admin settings)
    * @param array $classItems (values needed to create link node)
    * @param array $lessonItems (values needed to create link node)
    */
    protected static function printCourseLinks($osmap, $parent, $params, $classItems, $lessonItems)
    {
        //making sure there is no duplicate classes and lessons printed
        $classItems  = array_unique($classItems, SORT_REGULAR);
        $lessonItems = array_unique($lessonItems, SORT_REGULAR);

        foreach ($classItems as $classItem) {
            $classQuery = $classItem;
            unset($classQuery['cTitle']);
            $osmap->changeLevel(1);
            $node             = new stdclass;
            $node->id         = $parent->id;
            $node->name       = $classItem['cTitle'];
            $node->uid        = $parent->uid . '_' . $classItem['cid'];
            $node->browserNav = $parent->browserNav;
            $node->priority   = $params['link_priority'];
            $node->changefreq = $params['link_changefreq'];
            $node->link       = 'index.php?' . http_build_query($classQuery);
            $osmap->printNode($node);

            foreach ($lessonItems as $lessonItem) {
                $lessonQuery = $lessonItem;
                unset($lessonQuery['lTitle']);
                $osmap->changeLevel(1);
                if ($classItem['cid'] == $lessonItem['cid']) {
                    $node             = new stdclass;
                    $node->id         = $parent->id;
                    $node->name       = $lessonItem['lTitle'];
                    $node->uid        = $parent->uid . '_' . $lessonItem['lid'];
                    $node->browserNav = $parent->browserNav;
                    $node->priority   = $params['link_priority'];
                    $node->changefreq = $params['link_changefreq'];
                    $node->link       = 'index.php?' . http_build_query($lessonQuery);
                    $osmap->printNode($node);
                }
                $osmap->changeLevel(-1);
            }
            $osmap->changeLevel(-1);
        }
    }

    /*
    * Checks if plugin is enabled
    *
    * @return boolean static::$enabled
    */
    protected static function isEnabled()
    {
        if (null === static::$enabled) {
            $db = JFactory::getDbo();
            $db->setQuery('Select enabled From #__extensions Where name=' . $db->quote('com_oscampus'));
            static::$enabled = (bool)$db->loadResult();
        }

        return static::$enabled;
    }
}
