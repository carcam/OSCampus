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

        $table->alias = strtolower(preg_replace('/[^a-z0-9\-]*/i', '', $table->alias));

        // Check if the alias and title already exists
        $table->alias = $this->generateNewAlias($table->id, $table->alias);
    }

    /**
     * Method to change the title & alias
     *
     * @param   integer  $id           The id
     * @param   string   $alias        The alias
     *
     * @return  string  The modified alias
     */
    protected function generateNewAlias($id, $alias)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias)) && (@$table->id != $id)) {
            $alias = JString::increment($alias, 'dash');
        }

        return $alias;
    }
}
