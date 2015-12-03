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
             ->leftJoin('#__viewlevels AS course_view ON course_view.id = course.access')
             ->order('course.id desc, module.ordering, lesson.ordering');

         return $query;
     }
 }
