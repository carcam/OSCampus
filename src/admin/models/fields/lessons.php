<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusFormFieldLessons extends JFormField
{
    protected function getInput()
    {
        $html = array();

        $html[] = '<div id="' . $this->id . '">';
        $html[] = '<ul class="oscampus-module">';

        $modules = $this->getLessons();
        foreach ($modules as $moduleId => $module) {
            $html[] = $this->createModuleItem($module);
        }

        $html[] = '</ul>';
        $html[] = '</div>';

        JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);
        $this->addJavascript();

        return join("\n", $html);
    }

    /**
     * We ignore $this->value and retrieve the modules/lessons based on the form ID field.
     * If the course ID is in a different field, the 'coursefield' attribute can be used for
     * overriding.
     *
     * @return int[int[]]
     */
    protected function getLessons()
    {
        $courseField = (string)$this->element['coursefield'] ?: 'id';
        if ($courseId = (int)$this->form->getfield($courseField)->value) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'lesson.id',
                        'lesson.modules_id',
                        'lesson.title',
                        'lesson.alias',
                        'lesson.published',
                        'viewlevel.title AS viewlevel_title',
                        'module.title AS module_title'
                    )
                )
                ->from('#__oscampus_lessons AS lesson')
                ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                ->innerJoin('#__viewlevels viewlevel ON viewlevel.id = lesson.access')
                ->where('module.courses_id = ' . $courseId)
                ->order('module.ordering ASC, lesson.ordering ASC');

            if ($lessons = $db->setQuery($query)->loadObjectList()) {
                $modules = array();
                foreach ($lessons as $lesson) {
                    if (!isset($modules[$lesson->modules_id])) {
                        $modules[$lesson->modules_id] = (object)array(
                            'id'        => $lesson->modules_id,
                            'title'     => $lesson->module_title,
                            'lessons'   => array()
                        );
                    }
                    $modules[$lesson->modules_id]->lessons[$lesson->id] = $lesson;
                }

                return $modules;
            }
        }

        return array();
    }

    /**
     * Render html for top level list of modules
     *
     * @param object $module
     *
     * @return string
     */
    protected function createModuleItem($module)
    {
        $moduleInput = sprintf(
            '<input type="hidden" name="%1$s[%2$s]" value="%2$s"/>%3$s',
            $this->name,
            $module->id,
            $module->title
        );

        $html = array(
            '<li>',
            '<span class="handle">',
            '<i class="fa fa-caret-right"></i> ',
            $moduleInput,
            '</span>',
            '<ul class="oscampus-lesson">'
        );

        foreach ($module->lessons as $lessonId => $lesson) {
            $html[] = $this->createLessonItem($lesson);
        }
        $html[] = '</ul>';
        $html[] = '</li>';

        return join('', $html);
    }

    /**
     * Render the individual row for a lesson
     *
     * @param object $lesson
     *
     * @return string
     */
    protected function createLessonItem($lesson)
    {
        $lessonInput = '<input type="hidden" name="%1$s[%2$s][]" value="%3$s"/>%4$s';

        $link       = 'index.php?option=com_oscampus&task=lesson.edit&id=' . $lesson->id;
        $lessonLink = JHtml::_('link', $link, $lesson->title, 'target="_blank"');

        $html = array(
            '<li class="handle">',
            '<i class="fa fa-caret-right"></i> ',
            sprintf($lessonInput, $this->name, $lesson->modules_id, $lesson->id, $lessonLink),
            sprintf(' (%s: %s)', JText::_('COM_OSCAMPUS_ALIAS'), $lesson->alias),
            " - {$lesson->viewlevel_title}",
            '</li>'
        );

        return join('', $html);
    }

    /**
     * Add all js needed for drag/drop ordering
     *
     * @rfeturn void
     */
    protected function addJavascript()
    {
        JHtml::_('osc.jquery');
        JHtml::_('osc.jui');
        JHtml::_('script', 'com_oscampus/admin/lesson.js', false, true);

        $options = json_encode(
            array(
                'container' => '#' . $this->id
            )
        );

        JHtml::_('osc.onready', "$.Oscampus.admin.lesson.ordering({$options});");
    }
}
