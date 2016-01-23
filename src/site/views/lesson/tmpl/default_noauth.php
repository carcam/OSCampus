<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

?>
<div id="signup_overlay">
    <div class="wrapper">
        <h1>Become a member to view this session</h1>
    </div>
</div>

<script>
    (function($) {
        var parent = $('#oscampus');

        var overlay = $('#signup_overlay');
            //.css({
            //    width : parent.width(),
            //    height: parent.height()
            //});

        parent.prepend(overlay);
    })(jQuery);
</script -->
