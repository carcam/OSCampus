<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewNewcourses $this */
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-search'); ?>" id="oscampus">
    <?php if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_SEARCH')): ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php endif; ?>

    <?php
    if ($this->items) :
        foreach ($this->items as $this->item) {
            // By default uses sub-template from pathway view
            echo $this->loadTemplate('course');
        }

    else:
        ?>
        <div class="osc-alert-notify"><i class="fa fa-info-circle"></i>
            <?php echo JText::_('COM_OSCAMPUS_PATHWAY_NO_COURSES'); ?>
        </div>
        <?php
    endif;
    ?>
</div>

