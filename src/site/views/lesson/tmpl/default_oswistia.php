<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

$content = json_decode($this->lesson->content);
if (!empty($content->id)) :
?>
<div class="osc-lesson-links">
    <div class="osc-btn-group wistia_buttons_container" id="wistia_NaN_buttons_container">
        <a href="#" class="osc-btn wistia_button download hidden" id="wistia_NaN_download_button">
            <i class="fa fa-cloud-download"></i>
            <span class="osc-hide-tablet"> Download</span>
        </a><a href="#" class="osc-btn wistia_button autoplay hidden" id="wistia_NaN_autoplay_button">
            <i class="fa fa-check"></i>
            <span class="osc-hide-tablet">Autoplay</span>
        </a><a href="#" class="osc-btn osc-btn-active wistia_button focus hidden" id="wistia_NaN_focus_button">
            <i class="fa fa-times"></i>
            <span class="osc-hide-tablet">Focus</span>
        </a>
    </div>
    <div class="osc-btn-group hidden" id="course-navigation">
        <a href="#" class="osc-btn">
            <i class="fa fa-bars"></i>
            <span class="osc-hide-tablet">Home</span>
        </a><a href="#" class="osc-btn" id="prevbut">
            <i class="fa fa-chevron-left"></i>
            <span class="osc-hide-tablet">Prev</span>
        </a><a href="#" class="osc-btn" id="nextbut">
            <span class="osc-hide-tablet">Next</span>
            <i class="fa fa-chevron-right"></i>
        </a>
    </div>
</div>
<!-- .osc-lesson-links -->

<div class="osc-lesson-embed">
    <?php echo JHTML::_('content.prepare', '{wistia}' . $content->id . '{/wistia}'); ?>
</div>
<?php
endif;
