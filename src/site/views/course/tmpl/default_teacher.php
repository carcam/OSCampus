<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="content-teacher" class="osc-course-tabs-content" style="display: none">
    <?php
    if ($this->teacher->links) :
        ?>
        <div class="osc-teacher-links">
            <?php
            foreach ($this->teacher->links as $name => $link) {
                echo '<span class="osc-teacher-' . $name . '">'
                    . JHtml::_('link', $link, JText::_('COM_OSCAMPUS_TEACHER_LINK_' . $name), 'target="_blank"')
                    . '</span>';
            }
            ?>
        </div>
    <?php
    endif;
    echo JHtml::_('image', $this->teacher->image, $this->teacher->name);
    echo $this->teacher->bio;
    ?>
</div>
<!-- #content-teacher -->
