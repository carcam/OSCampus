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
    <p><?php echo JText::_('COM_OSCAMPUS_COURSE_DOWNLOAD_EXERCISE_FILES'); ?></p>
    <div class="osc-table">
        <?php
        foreach ($this->files as $i => $file) :

            // Extract file format
            $icon = pathinfo($file->path);

            switch($icon['extension'])
            {
                default:
                    $icon = 'fa-paperclip';
                    break;
                case 'pdf':
                    $icon = 'fa-file-pdf-o';
                    break;
                case 'html':
                    $icon = 'fa-file-code-o';
                    break;
                case 'css':
                case 'js':
                case 'php':
                    $icon = 'fa-file';
                    break;
                case 'zip':
                    $icon = 'fa-file-zip-o';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                    $icon = 'fa-file-image-o';
                    break;
            }
            ?>
            <div class="<?php echo 'osc-section ' . ($i % 2 ? 'osc-row-two' : 'osc-row-one'); ?>">
                <div class="block12">
                    <?php
                    echo JHtml::_(
                        'link',
                        $file->path,
                        '<i class="fa ' . $icon . '"></i> ' . $file->title,
                        'target="_blank" title="' . htmlspecialchars($file->description) . '"'
                    );
                    ?>
                </div>
            </div>
            <?php
        endforeach;
        ?>
    </div>
</div>
<!-- #content-files -->
