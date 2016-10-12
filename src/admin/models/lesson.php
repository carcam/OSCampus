<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Joomla\Registry\Registry as Registry;

defined('_JEXEC') or die();


class OscampusModelLesson extends OscampusModelAdmin
{
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('module.courses_id, module.title')
                ->from('#__oscampus_modules AS module')
                ->where('module.id = ' . (int)$item->modules_id);

            $extra = $db->setQuery($query)->loadObject();

            $item->courses_id   = empty($extra) ? null : $extra->courses_id;
            $item->module_title = empty($extra) ? null : $extra->title;
        }

        return $item;
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = parent::getForm($data, $loadData);

        if ($data) {
            $fixedData = new Registry($data);
            OscampusFactory::getContainer()
                ->lesson
                ->loadAdminForm($form, $fixedData);
        }

        return $form;
    }

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        if ($data) {
            $fixedData = $data instanceof JObject ? $data->getProperties() : $data;
            $fixedData = new Registry($fixedData);

            if (!$fixedData->get('courses_id')) {
                $app      = OscampusFactory::getApplication();
                $courseId = $app->getUserState('com_oscampus.lessons.filter.course');

                $fixedData->set('courses_id', $courseId);
            }

            OscampusFactory::getContainer()
                ->lesson
                ->loadAdminForm($form, $fixedData);

            if (is_array($data)) {
                $data = $fixedData->toArray();

            } elseif ($data instanceof JObject) {
                $data->setProperties($fixedData->toArray());

            } elseif (is_object($data)) {
                $data = $fixedData->toObject();
            }
        }

        parent::preprocessForm($form, $data, $group);
    }

    protected function getReorderConditions($table)
    {
        $conditions = array(
            'modules_id = ' . (int)$table->modules_id
        );

        return $conditions;
    }

    public function save($data)
    {
        if ($module = $this->getModule($data)) {
            if (!$module->store()) {
                $this->setError($module->getError());
                return false;
            }

            $data['modules_id'] = $module->id;
        }

        try {
            unset($data['courses_id'], $data['module_title']);

            $fixedData = new Registry($data);

            OscampusFactory::getContainer()
                ->lesson
                ->saveAdminChanges($fixedData);

            $data = $fixedData->toArray();

        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return parent::save($data);
    }

    protected function getModule($data)
    {
        $courseId = empty($data['courses_id']) ? null : $data['courses_id'];
        $title    = empty($data['module_title']) ? null : $data['module_title'];

        if ($courseId && $title) {
            $table = OscampusTable::getInstance('Modules');

            $table->load(array('courses_id' => $courseId, 'title' => $title));
            if (!$table->id) {
                $table->setProperties(
                    array(
                        'courses_id' => $courseId,
                        'title'      => $title
                    )
                );
                $table->ordering = $table->getNextOrder('courses_id = ' . $courseId);
            }

            return $table;
        }

        return null;
    }

    /**
     * @param JTable $table
     */
    protected function prepareTable($table)
    {
        if (!$table->id) {
            $ordering = 0;
            if ($table->modules_id) {
                $ordering = $table->getNextOrder('modules_id = ' . $table->modules_id);
            }
            $table->ordering = $ordering;
        }

        if (!$table->alias) {
            $table->alias = OscampusApplicationHelper::stringURLSafe($table->title);

            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('count(*)')
                ->from('#__oscampus_lessons')
                ->where(
                    array(
                        'modules_id = ' . $table->modules_id,
                        'alias = ' . $db->quote($table->alias),
                        'id != ' . $table->id
                    )
                );

            if ($duplicates = $db->setQuery($query)->loadResult()) {
                $table->alias .= '-' . $duplicates;
            }
        }

    }
}
