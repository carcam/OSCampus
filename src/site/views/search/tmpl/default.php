<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewSearch $this */

JHtml::_('behavior.core');

?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-search'); ?>" id="oscampus">
    <?php
    if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_SEARCH')) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;

    if (!$this->items) :
        ?>
        <div class="osc-alert-warning m-bottom"><i class="fa fa-info-circle"></i>
            <?php echo JText::_('COM_OSCAMPUS_SEARCH_RESULTS_NOTFOUND'); ?>
        </div>
        <?php
    endif;

    $lastSection = null;
    foreach ($this->items as $this->item) :
        if ($this->item->section != $lastSection) :
            $heading = 'COM_OSCAMPUS_SEARCH_RESULTS_' . $this->item->section;
            $count   = $this->model->getTotal($this->item->section);
            ?>
            <div class="osc-alert-success m-bottom"><i class="fa fa-info-circle"></i>
                <?php echo JText::plural($heading, $count); ?>
            </div>
            <?php
        endif;

        switch ($this->item->section) :
            case 'pathways':
                echo $this->loadViewTemplate('pathways', 'pathway');
                break;

            case 'courses':
                echo $this->loadViewTemplate('pathway', 'course');
                break;

            case 'lessons':
                echo $this->loadTemplate('lesson');
                break;

        endswitch;

        $lastSection = $this->item->section;
    endforeach;
    ?>
    <div class="pagination">
        <p class="counter pull-right">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
        <?php echo $this->pagination->getLimitBox(); ?>
    </div>
</div>

