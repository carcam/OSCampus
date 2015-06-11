<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();
/**
 * @var OscampusViewCourse $this
 */

$linkBase = OscampusRoute::get('pathways') . '&view=lesson&cid=%s&idx=%s';
?>
<div id="content-content" class="osc-course-tabs-content">
    <div class="osc-table">
        <?php
        $idx = 0;
        foreach ($this->lessons as $module) :
            ?>
            <div class="osc-section osc-row-heading">
                <div class="block12 p-left-x"><i class="fa fa-align-justify"></i>
                    <?php echo $module->title; ?>
                </div>
            </div>
            <?php
            foreach ($module->lessons as $i => $lesson) :
                $link  = sprintf($linkBase, $this->course->id, $idx++);
                ?>
                <div class="<?php echo 'osc-section ' . ($i%2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                    <div class="block9 p-left-xx">
                        <?php echo JHtml::_('link', $link, $lesson->title); ?>
                    </div>
                    <?php if (isset($this->viewed[$lesson->id])) : ?>
                    <div class="block3 osc-check-viewed osc-hide-tablet">
                        <i class="fa fa-check"></i> Viewed
                    </div>
                    <?php endif; ?>
                </div>
                <?php
            endforeach;
        endforeach;
    ?>
    </div>
</div>
<!-- #content-lessons -->
