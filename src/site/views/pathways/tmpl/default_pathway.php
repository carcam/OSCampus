<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewPathways $this */

?>
<div class="osc-section osc-course-list">
    <div class="block4 osc-course-image">
        <?php
        $link  = JHtml::_('osc.pathway.link', $this->item, null, null, true);
        $image = JHtml::_('image', $this->item->image, $this->item->title);
        echo JHtml::_('link', $link, $image);
        ?>
    </div>
    <div class="block8 osc-course-description">
        <h2><?php echo JHtml::_('link', $link, $this->item->title); ?></h2>

        <?php echo $this->item->description; ?>
    </div>
</div>
<!-- .osc-section -->
