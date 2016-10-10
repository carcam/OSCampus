<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
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
    foreach ($this->items as $item) :
        if ($item->section != $lastSection) :
            $heading = 'COM_OSCAMPUS_SEARCH_RESULTS_' . $item->section;
            $count   = $this->model->getTotal($item->section);
            ?>
            <div class="osc-alert-success m-bottom"><i class="fa fa-info-circle"></i>
                <?php echo JText::plural($heading, $count); ?>
            </div>
            <?php
        endif;

        echo JLayoutHelper::render($item->section, $item);
        $lastSection = $item->section;
    endforeach;

    echo $this->pagination->getPaginationLinks('pagination');
    ?>
</div>

