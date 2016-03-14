<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** \Oscampus\Module\Search $this */
?>
<form
    name="oscampusFilter"
    id="<?php echo $this->id; ?>"
    method="post"
    action="">

    <input
        name="search"
        type="text"
        value="<?php echo $this->getState('filter.search'); ?>"/>

    <?php echo $this->getFilter('Pathway'); ?>
    <?php echo $this->getFilter('Tag'); ?>
    <?php echo $this->getFilter('Difficulty'); ?>
    <?php echo $this->getFilter('Teacher'); ?>

    <button type="submit">go</button>
    <input type="hidden" name="option" value="com_oscampus"/>
    <input type="hidden" name="task" value="filter.pathway"/>
</form>
