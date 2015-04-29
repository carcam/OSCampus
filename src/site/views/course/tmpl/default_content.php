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
                <div class="block12 p-left-x"><i class="fa fa-align-justify"></i>
                    <?php echo $module->title; ?>
                </div>
            </div>
            <?php
            foreach ($module->lessons as $i => $lesson) :
                ?>
                <div class="<?php echo 'osc-section ' . ($i%2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                    <div class="block9 p-left-xx">
                        <a href="javascript:alert('Under Construction');">
                            <?php echo $lesson->title; ?>
                        </a>
                    </div>
                    <div class="block3 osc-check-viewed">
                        <i class="fa fa-check"></i> Viewed
                    </div>
                </div>
                <?php
            endforeach;
        endforeach;
    ?>
    </div>
</div>
<!-- #content-lessons -->
