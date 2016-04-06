<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** \Oscampus\Module\Search $this */

$actionUrl = JRoute::_(OscampusRoute::getInstance()->get('search'));

$textValue = $this->getState('filter.text');
$textClass = $this->getStateClass($textValue);
?>
<div class="osc-module-container">
    <form
        name="oscampusFilter"
        id="<?php echo $this->id; ?>"
        method="get"
        action="<?php echo $actionUrl; ?>">

        <input
            name="text"
            type="text"
            value="<?php echo $textValue; ?>"
            class="<?php echo $textClass; ?>"/>

        <?php
        echo $this->getFilter('Tag');

        echo $this->getTypes();
        ?>

        <div class="osc-btn-group">
            <button type="button" class="osc-btn osc-clear-filters">
                <i class="fa fa-times"></i> <span>
                    <?php echo JText::_('MOD_OSCAMPUS_SEARCH_CLEAR'); ?>
                </span>
            </button>
        </div>
    </form>
</div>
