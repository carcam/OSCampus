<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="content-files" class="osc-course-tabs-content" style="display: none">
    <?php
    foreach ($this->files as $file) {
        echo '<p>' . JHtml::_('link', $file->path, $file->title, 'target="_blank"') . '</p>';
    }
    ?>
</div>
<!-- #content-files -->
