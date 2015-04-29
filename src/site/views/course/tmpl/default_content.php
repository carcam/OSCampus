<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="content-content" class="osc-course-tabs-content">
    <div class="osc-table">
        <?php
        foreach ($this->lessons as $module) :
            ?>
            <div class="osc-section osc-row-heading">
                <div class="block12"><i class="uk-icon uk-icon-play"></i>
                    <?php echo $module->title; ?>
                </div>
            </div>
            <?php
            foreach ($module->lessons as $i => $lesson) :
                ?>
                <div class="<?php echo 'osc-section ' . ($i%2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                    <div class="block10">
                        <a href="javascript:alert('Under Construction');">
                            <?php echo $lesson->title; ?>
                        </a>
                    </div>
                    <div class="block2 osc-check-viewed">
                        <i class="uk-icon uk-icon-check"></i> Viewed
                    </div>
                </div>
                <?php
            endforeach;
        endforeach;
    ?>
    </div>
</div>
<!-- #content-lessons -->
