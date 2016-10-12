<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** \Oscampus\Module\Search $this */

$actionUrl = JRoute::_(OscampusRoute::getInstance()->get('search'));

$textValue = $this->getState('filter.text');
$textClass = $this->getStateClass($textValue);

$advancedToggle  = $this->id . '-toggle';
$advancedContent = $this->id . '-advanced';
$advancedVisible = $this->getState('show.types')
    || array_filter(
        array_diff_key(
            $this->model->getActiveFilters(),
            array(
                'text' => null
            )
        )
    );

JHtml::_('osc.sliders', '#' . $advancedToggle, $advancedVisible);

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

        <div
            id="<?php echo $advancedToggle; ?>"
            data-content="<?php echo '#' . $advancedContent; ?>"
            class="osc-search-toggle">
            <i class="fa fa-cogs"></i>
            <?php echo JText::_('MOD_OSCAMPUS_SEARCH_ADVANCED'); ?>
        </div>
        <div id="<?php echo $advancedContent; ?>" class="osc-search-advanced" style="display: none;">
            <?php
            echo $this->getFilter('Tag');
            echo $this->getTypes();
            ?>
            <div>
                <button type="button" class="osc-btn osc-clear-filters">
                    <i class="fa fa-times"></i>
                    <?php echo JText::_('MOD_OSCAMPUS_SEARCH_CLEAR'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
