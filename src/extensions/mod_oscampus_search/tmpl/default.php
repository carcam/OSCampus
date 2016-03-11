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
    <?php echo join('', $this->getFilters()); ?>
    <button type="submit">go</button>
    <input type="hidden" name="option" value="com_oscampus"/>
    <input type="hidden" name="task" value="filter.pathway"/>
</form>
