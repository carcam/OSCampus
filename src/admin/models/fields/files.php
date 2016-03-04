<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('List');

class OscampusFormFieldFiles extends JFormFieldList
{
    /**
     * @var array
     */
    protected $lessons = null;

    /**
     * @var string[]
     */
    protected $files = null;

    protected function getInput()
    {
        JHtml::_('stylesheet', 'com_oscampus/admin.css', null, true);
        JHtml::_('stylesheet', 'com_oscampus/awesome/css/font-awesome.min.css', null, true);
        $this->addJavascript();

        $html = array(
            sprintf('<div id="%s" class="osc-file-manager">', $this->id),
            '<ul>'
        );

        $files = (array)$this->value ?: array(null);
        foreach ($files as $file) {
            $html[] = $this->createFileBlock($file);
        }

        $html = array_merge(
            $html,
            array(
                '</ul>',
                $this->createButton('osc-btn-main-admin osc-file-add', 'fa-plus', 'COM_OSCAMPUS_FILES_ADD'),
                '</div>'
            )
        );

        return join('', $html);
    }

    /**
     * Generate html for single file block
     *
     * @param object $file
     *
     * @return string
     */
    protected function createFileBlock($file = null)
    {
        $id = sprintf(
            '<input type="hidden" name="%s[id][]" value="%s"/>',
            $this->name,
            empty($file->id) ? '' : $file->id
        );

        $title = sprintf(
            '<input type="text" class="required" required="required" name="%s[title][]" value="%s" size="40"/>',
            $this->name,
            empty($file->title) ? '' : htmlspecialchars($file->title)
        );

        $description = sprintf(
            '<textarea name="%s[description][]">%s</textarea>',
            $this->name,
            empty($file->description) ? '' : htmlspecialchars($file->description)
        );

        $upload   = sprintf('<input type="file" name="%s[upload][]" value=""/>', $this->name);
        $lessonId = empty($file->lessons_id) ? '' : $file->lessons_id;

        $html = '<li class="osc-file-block">'
            . $id
            . $this->createButton('osc-file-ordering', 'fa-arrows')
            . $this->createButton('osc-btn-warning-admin osc-file-delete', 'fa-times')
            . $title
            . '<br class="clr"/>' . $description
            . '<br class="clr"/>' . $this->getFileList($file->path)
            . $this->getLessonOptions($lessonId)
            . '<br class="clr"/>' . $upload
            . '</li>';

        return $html;
    }

    /**
     * Create a standard button
     *
     * @param string $class
     * @param string $icon
     * @param string $text
     *
     * @return string
     */
    protected function createButton($class, $icon, $text = null)
    {
        $button = sprintf(
            '<button type="button" class="%s"><i class="fa %s"></i> %s</button>',
            $class,
            $icon,
            ($text ? JText::_($text) : '')
        );

        return $button;
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
        JHtml::_('script', 'com_oscampus/admin/files.js', false, true);

        $options = json_encode(
            array(
                'container' => '#' . $this->id
            )
        );

        JHtml::_('osc.onready', "$.Oscampus.admin.files.init({$options});");
    }

    /**
     * Get a select dropdown for a previously uploaded file
     *
     * @param string $selected
     *
     * @return mixed
     */
    protected function getFileList($selected)
    {
        if ($this->files === null) {
            $files = JFolder::files(JPATH_SITE . '/' . \Oscampus\Course::FILE_PATH);

            $this->files = array();
            foreach ($files as $file) {
                $path = \Oscampus\Course::FILE_PATH . '/' . $file;
                $this->files[] = JHtml::_('select.option', $path, $file);
            }

            array_unshift(
                $this->files,
                JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_FILE_PATH'))
            );
        }

        $html = JHtml::_(
            'select.genericlist',
            $this->files,
            $this->name . '[path][]',
            null,
            'value',
            'text',
            $selected
        );

        return $html;
    }

    /**
     * Get a select field for attaching to specific lesson
     *
     * @param int $selected
     *
     * @return string
     */
    protected function getLessonOptions($selected)
    {
        if ($this->lessons === null) {
            $db = OscampusFactory::getDbo();

            $courseField = (string)$this->element['coursefield'];
            if (!$courseField) {
                OscampusFactory::getApplication()->enqueueMessage('Missing course ID field link', 'error');
                $this->lessons = '';

            } else {
                $courseId = (int)$this->form->getField($courseField)->value;

                $query = $db->getQuery(true)
                    ->select(
                        array(
                            'lesson.id',
                            'lesson.title',
                            'module.title AS ' . $db->quoteName('module_title')
                        )
                    )
                    ->from('#__oscampus_lessons AS lesson')
                    ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                    ->where('module.courses_id = ' . $courseId)
                    ->order('module.ordering ASC, lesson.ordering ASC');

                $lessons = $db->setQuery($query)->loadObjectList();

                $this->lessons = array();
                foreach ($lessons as $lesson) {
                    $key = $lesson->module_title;
                    if (empty($this->lessons[$key])) {
                        $this->lessons[$key] = array();
                    }
                    $this->lessons[$key][] = JHtml::_('select.option', $lesson->id, $lesson->title);
                }

                $this->lessons = array_merge(
                    array(array(JHtml::_('select.option', '', JText::_('COM_OSCAMPUS_OPTION_SELECT_FILE_LESSON')))),
                    $this->lessons
                );
            }
        }

        if ($this->lessons) {
            $options = array(
                'id'                 => null,
                'list.attr'          => null,
                'list.select'        => $selected,
                'group.items'        => null,
                'option.key.toHtml'  => false,
                'option.text.toHtml' => false
            );

            return JHtml::_('select.groupedlist', $this->lessons, $this->name . '[lessons_id][]', $options);
        }

        return '';
    }
}
