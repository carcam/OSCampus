<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if ($this->files) :
    ?>
    <div class="osc-section oscampus-lesson-files">
        <h3><?php echo JText::_('COM_OSCAMPUS_LESSON_FILES'); ?></h3>
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
    <?php
endif;
