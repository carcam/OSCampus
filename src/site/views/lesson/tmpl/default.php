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

?>

<div class="osc-container oscampus-lesson" id="oscampus">
    <div class="page-header">
        <h1><?php echo $this->lesson->title; ?></h1>
    </div>

    <?php if ($this->lesson->header) : ?>
        <div>
            <?php echo $this->lesson->header; ?>
        </div>
    <?php endif; ?>

    <?php
    echo $this->loadTemplate($this->lesson->type);
    ?>

    <?php if ($this->lesson->footer) : ?>
        <div>
            <?php echo $this->lesson->footer; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->files) : ?>
        <div>
            <h1>Files</h1>
            <ul>
                <?php
                foreach ($this->files as $file) {
                    echo '<li>' . JHtml::_('link', $file->path, $file->title, 'target="_blank"') . '</li>';
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
