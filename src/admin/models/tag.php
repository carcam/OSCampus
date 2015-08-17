<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelTag extends OscampusModelAdmin
{
    public function getTable($type = 'Tags', $prefix = 'OscampusTable', $config = array())
    {
        return OscampusTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_oscampus.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = OscampusFactory::getApplication()->getUserState('com_oscampus.edit.tag.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * @param JTable $table
     *
     * @return void
     */
    protected function prepareTable($table)
    {
        if (empty($table->alias)) {
            $table->alias = $table->title;
        }

        $table->alias = preg_replace('/[^a-z0-9\-]*/', '', $table->alias);
        var_dump($table->alias);

        // Check if the alias and title already exists
        $data = $this->generateNewTitle($table->alias, $table->title);
        $table->title = $data['0'];
        $table->alias = $data['1'];
    }

    /**
     * Method to change the title & alias.
     *
     * @param   string   $alias        The alias.
     * @param   string   $title        The title.
     *
     * @return  array  Contains the modified title and alias.
     *
     * @since   12.2
     */
    protected function generateNewTitle($alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias))) {
            $title = JString::increment($title);
            $alias = JString::increment($alias, 'dash');
        }

        return array($title, $alias);
    }
}
