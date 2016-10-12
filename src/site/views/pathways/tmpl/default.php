<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
        foreach ($this->items as $item) :
            echo JLayoutHelper::render('pathway', $item);
        endforeach;
        ?>
        <?php
    endif;

    echo $this->pagination->getPaginationLinks('pagination');
    ?>
</div>
