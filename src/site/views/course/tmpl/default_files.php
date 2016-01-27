<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="content-files" class="osc-course-tabs-content" style="display: none">
    <div class="osc-table">
        <?php
        foreach ($this->files as $i => $file) :
            ?>
            <div class="<?php echo 'osc-section ' . ($i % 2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                <div class="block6">
                    <?php echo JHtml::_('link', $file->path, $file->title, 'target="_blank"'); ?>
                </div>
                <div class="block6">
                    <?php echo $file->description; ?>
                </div>
            </div>
            <?php
        endforeach;
        ?>
    </div>
</div>
<!-- #content-files -->
