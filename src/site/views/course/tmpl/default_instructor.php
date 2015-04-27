<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="content-trainer" class="osc-course-tabs-content" style="display: none">
    <?php
    if ($this->instructor->links) :
        ?>
        <div class="osc-instructor-links">
            <?php
            foreach ($this->instructor->links as $name => $link) {
                echo '<span class="osc-instructor-' . $name . '">'
                    . JHtml::_('link', $link, JText::_('COM_OSCAMPUS_INSTRUCTOR_LINK_' . $name), 'target="_blank"')
                    . '</span>';
            }
            ?>
        </div>
    <?php
    endif;
    echo JHtml::_('image', $this->instructor->image, $this->instructor->name);
    echo $this->instructor->bio;
    ?>
</div>
<!-- #content-trainer -->
