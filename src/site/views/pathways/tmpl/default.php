<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var OscampusViewPathways $this
 */

JHtml::_('behavior.core');

?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-pathways'); ?>" id="oscampus">
    <?php
    if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_ONLINE_TRAINING')) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;
    ?>

    <?php
    if (!$this->items) :
        ?>
        <div class="osc-alert-notify">
            <i class="fa fa-info-circle"></i>
            <?php echo JText::sprintf('COM_OSCAMPUS_PATHWAYS_NOTFOUND'); ?>
        </div>
        <?php
    else :
        foreach ($this->items as $this->item) :
            echo $this->loadTemplate('pathway');
        endforeach;
        ?>
        <?php
    endif;
    ?>
</div>
<div>
    <form action="" method="post" name="adminForm" id="adminForm">
        <div class="pagination">
            <?php
            if ($this->pagination->getPaginationPages()) :
                echo $this->pagination->getListFooter();
            else :
                echo $this->pagination->getLimitBox();
            endif;
            ?>
        </div>
    </form>
</div>
