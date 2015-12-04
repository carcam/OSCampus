<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
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

        JHtml::stylesheet('com_oscampus/awesome/css/font-awesome.min.css', null, true);
        $this->addJavascript();

        return join("\n", $html);
    }

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
                        'module.title AS module_title',
                        'module.alias AS module_alias',
                        'module.published AS module_published'
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
                            'title'     => $lesson->module_title,
                            'alias'     => $lesson->module_alias,
                            'published' => $lesson->module_published,
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

    protected function createModuleItem($module)
    {
        $moduleInput = '<input type="hidden" name="%s[]" value="%s"/>%s';
        $moduleLink  = JHtml::_('link', "javascript:alert('under construction');", $module->title);

        $html = array(
            '<li>',
            '<i class="handle fa fa-caret-right"></i> ',
            sprintf($moduleInput, $this->name, $module->id, $moduleLink),
            sprintf(' (%s: %s)', JText::_('COM_OSCAMPUS_ALIAS'), $module->alias),
            '<ul class="oscampus-lesson">'
        );

        foreach ($module->lessons as $lessonId => $lesson) {
            $html[] = $this->createLessonItem($lesson);
        }
        $html[] = '</ul>';
        $html[] = '</li>';

        return join('', $html);
    }

    protected function createLessonItem($lesson)
    {
        $lessonInput = '<input type="hidden" name="%s[][]" value="%s"/>%s';

        $link       = 'index.php?option=com_oscampus&task=lesson.edit&tmpl=component&id=' . $lesson->id;
        $attribs    = array(
            'class' => 'modal',
            'rel'   => '{handler: \'iframe\', size: {x: 800, y: 450}}'
        );
        $lessonLink = JHtml::_('link', $link, $lesson->title, $attribs);

        $html = array(
            '<li>',
            '<i class="handle fa fa-caret-right"></i> ',
            sprintf($lessonInput, $this->name, $lesson->id, $lessonLink),
            sprintf(' (%s: %s)', JText::_('COM_OSCAMPUS_ALIAS'), $lesson->alias),
            " - {$lesson->viewlevel_title}",
            '</li>'
        );

        return join('', $html);
    }

    protected function addJavascript()
    {
        JHtml::_('behavior.modal');
        JHtml::_('osc.jquery');
        JHtml::_('script', 'com_oscampus/jquery-ui.js', false, true);
        JHtml::_('script', 'com_oscampus/lesson.js', false, true);

        $options = json_encode(
            array(
                'container' => '#' . $this->id
            )
        );

        JHtml::_('osc.onready', "$.Oscampus.lesson.ordering({$options});");
    }
}
