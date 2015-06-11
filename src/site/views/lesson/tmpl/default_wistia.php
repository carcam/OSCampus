<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

/**
 * @var OscampusViewLesson $this
 */
defined('_JEXEC') or die();

if (!empty($this->lesson->content->id)) :
    ?>
    <div class="osc-lesson-links">
        <div class="osc-btn-group wistia_buttons_container" id="wistia_NaN_buttons_container">
            <a href="#" class="osc-btn wistia_button download hidden" id="wistia_NaN_download_button">
                <i class="fa fa-cloud-download"></i>
                <span class="osc-hide-tablet">
                    <?php echo JText::_('COM_OSCAMPUS_DOWNLOAD'); ?>
                </span>
            </a>
            <a href="#" class="osc-btn wistia_button autoplay hidden" id="wistia_NaN_autoplay_button">
                <i class="fa fa-check"></i>
                <span class="osc-hide-tablet">
                    <?php echo JText::_('COM_OSCAMPUS_AUTOPLAY'); ?>
                </span>
            </a>
            <a href="#" class="osc-btn osc-btn-active wistia_button focus hidden" id="wistia_NaN_focus_button">
                <i class="fa fa-times"></i>
                <span class="osc-hide-tablet">
                    <?php echo JText::_('COM_OSCAMPUS_FOCUS'); ?>
                </span>
            </a>
        </div>
        <?php echo $this->loadTemplate('navigation'); ?>
    </div>
    <!-- .osc-lesson-links -->

    <div class="osc-lesson-embed">
        <?php echo JHtml::_('osc.wistia.player', $this->lesson->content->id); ?>
    </div>
<?php
endif;
