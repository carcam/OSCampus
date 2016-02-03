<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewLesson $this */

$user = OscampusFactory::getUser();

$signupType = $user->guest ? 'signup.new' : 'signup.upgrade';

if ($itemid = (int)$this->getParams()->get($signupType)) {
    $signupPage = JRoute::_('index.php?Itemid=' . $itemid);
}

?>
<div id="signup_overlay">
    <div class="wrapper">
        <h1>Become a member to view this session</h1>
        <?php
        if (!empty($signupPage)) :
            ?>
            <p><?php echo JHtml::_('link', $signupPage, 'Signup here!'); ?></p>
            <?php
        endif;
        ?>
    </div>
</div>

<script>
    (function($) {
        var parent = $('#oscampus');

        var overlay = $('#signup_overlay')
            .css({
                width : parent.width(),
                height: parent.height()
            });

        parent.prepend(overlay);
    })(jQuery);
</script>
