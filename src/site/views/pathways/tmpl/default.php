<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
?>
<div class="<?php echo $this->getPageClass('osc-container oscampus-pathways'); ?>" id="oscampus">
    <?php if ($heading = $this->getHeading('COM_OSCAMPUS_HEADING_ONLINE_TRAINING')): ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php endif; ?>

    <?php
    foreach ($this->items as $item) :
        ?>
        <div class="osc-section osc-course-list">
            <div class="block4 osc-course-image">
                <?php
                $link  = JHtml::_('osc.pathway.link', $item, null, null, true);
                $image = JHtml::_('image', $item->image, $item->title);
                echo JHtml::_('link', $link, $image);
                ?>
            </div>
            <div class="block8 osc-course-description">
                <h2><?php echo JHtml::_('link', $link, $item->title); ?></h2>

                <?php echo $item->description; ?>
            </div>
        </div>
        <!-- .osc-section -->
    <?php
    endforeach;
    ?>

</div>
