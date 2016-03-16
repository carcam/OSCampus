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
<div class="osc-module-container">
    <form
        name="oscampusFilter"
        id="<?php echo $this->id; ?>"
        method="post"
        action="">

        <input
            name="filter_text"
            type="text"
            value="<?php echo $this->getState('filter.text'); ?>"/>

        <?php
        echo $this->getFilter('Pathway');
        echo $this->getFilter('Tag');
        echo $this->getFilter('Completion');
        ?>

        <div class="osc-btn-group">
            <button type="submit" class="osc-btn osc-btn-main osc-search-filters">
                <i class="fa fa-search"></i> <span>
                    <?php echo JText::_('MOD_OSCAMPUS_SEARCH_GO'); ?>
                </span>
            </button><button type="button" class="osc-btn osc-clear-filters">
                <i class="fa fa-times"></i> <span>
                    <?php echo JText::_('MOD_OSCAMPUS_SEARCH_CLEAR'); ?>
                </span>
            </button>
        </div>

        <input type="hidden" name="option" value="com_oscampus"/>
        <input type="hidden" name="task" value="filter.courses"/>
    </form>
</div>
