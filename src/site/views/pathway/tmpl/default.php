<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewPathway $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-pathway'); ?>" id="oscampus">
    <div class="page-header">
        <h1><?php echo $this->pathway->title; ?></h1>
    </div>

    <?php
    if (!$this->items) :
        ?>
        <div class="osc-alert-notify">
            <i class="fa fa-info-circle"></i>
            <?php echo JText::_('COM_OSCAMPUS_PATHWAY_NO_COURSES'); ?>
        </div>
        <?php
    else :
        foreach ($this->items as $this->item) {
            echo $this->loadTemplate('course');
        }
    endif;
    ?>
</div>
