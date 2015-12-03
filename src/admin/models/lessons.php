<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
 class OscampusModelLessons extends OscampusModelList
 {
     public function __construct($config = array())
     {
         $config['filter_fields'] = array(
             'id',                'lesson.id',
             'title',             'lesson.title',
             'type',              'lesson.type',
             'published',         'lesson.published',
             'module_title',      'module.title',
             'module_published',  'module.published',
             'course_title',      'course.title',
             'course_published',  'course.published',
             'course_released',   'course.released',
             'course_defficulty', 'course.difficulty'
         );

         parent::__construct($config);
     }

     protected function getListQuery()
     {
         $db = $this->getDbo();

         $query = $db->getQuery(true)
             ->select(
                 array(
                     'lesson.id',
                     'lesson.modules_id',
                     'module.courses_id',
                     'module.ordering AS module_ordering',
                     'lesson.ordering',
                     'lesson.title',
                     'lesson.alias',
                     'lesson.type',
                     'lesson.published',
                     'module.title AS module_title',
                     'module.published AS module_published',
                     'course.title AS course_title',
                     'course.published AS course_published',
                     'course.released AS course_released',
                     'course.difficulty AS course_difficulty'
                 )
             )
             ->from('#__oscampus_lessons lesson')
             ->leftJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
             ->leftJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
             ->leftJoin('#__viewlevels AS lesson_view ON lesson_view.id = lesson.access')
             ->leftJoin('#__viewlevels AS course_view ON course_view.id = course.access');

         $primary = $this->getState('list.ordering', 'course.title');
         $direction = $this->getState('list.direction', 'ASC');
         $query->order($primary . ' ' . $direction);
         if (!in_array($primary, array('lesson.title', 'lesson.id'))) {
             $query->order('lesson.title ' . $direction);
         }

         $this->whereTextSearch($query, 'lesson.id', 'lesson.title', 'lesson.alias');

         return $query;
     }

     protected function populateState($ordering = 'lesson.title', $direction = 'ASC')
     {
         $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
         $this->setState('filter.search', $search);

         parent::populateState($ordering, $direction);
     }
 }
