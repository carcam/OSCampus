<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** @var OscampusViewMycertificates $this */

?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-mycertificates'); ?>" id="oscampus">
    <?php
    if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_MYCERTIFICATES')) :
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
        <?php
    endif;
    ?>

    <?php
    if ($this->items) :
        ?>
        <div class="osc-table">
            <div class="osc-section osc-row-heading osc-hide-tablet">
                <div class="block6">
                    <i class="fa fa-bars"></i> <?php echo JText::_('COM_OSCAMPUS_COURSE_TITLE'); ?>
                </div>
                <div class="block3">
                    <i class="fa fa-calendar"></i> <?php echo JText::_('COM_OSCAMPUS_DATE_EARNED'); ?>
                </div>
                <div class="block3">
                    <i class="fa fa-file-pdf-o"></i> <?php echo JText::_('COM_OSCAMPUS_CERTIFICATE'); ?>
                </div>
            </div>

            <?php
            foreach ($this->items as $item) :
                ?>
                <div class="osc-section osc-row-one">
                    <div class="block6">
                        <?php echo JHtml::_('osc.course.link', $item); ?>
                    </div>
                    <div class="block3">
                        <?php echo $item->date_earned->format('F j, Y'); ?>
                    </div>
                    <div class="block3">
                        <?php echo JHtml::_('osc.link.certificate', $item->id); ?>
                    </div>
                </div>
                <?php
            endforeach;
            ?>

        </div>
        <?php
    else :
        ?>
        <div class="osc-alert-notify a-center">
            <?php
            $link = JRoute::_(OscampusRoute::getInstance()->get('pathways'));
            $link = JHtml::_('link', $link, JText::_('COM_OSCAMPUS_PATHWAYS_LINK'), 'class="osc-btn"');
            echo JText::sprintf('COM_OSCAMPUS_MYCERTIFICATES_GET_STARTED', $link);
            ?>
        </div>
        <?php
    endif;
    ?>
</div>
