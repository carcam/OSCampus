<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelTag extends OscampusModelAdmin
{
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
