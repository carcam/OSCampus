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
    echo JHtml::_('osc.teacher.links', $this->teacher);
    echo JHtml::_('image', $this->teacher->image, $this->teacher->name);
    echo $this->teacher->bio;
    ?>
</div>
<!-- #content-teacher -->
