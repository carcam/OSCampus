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
<style>
    #oscampus .oscampus-lesson-content {
        background: #000;
        position: relative;
    }
    #oscampus .oscampus-lesson-content img {
        opacity: 0.5;
        width: 100%;
        height: auto;
    }
    #oscampus .oscampus-lesson-content h3 {
        margin-top: 0;
        margin-bottom: 10px;
    }
    #oscampus #signup-overlay {
        position: absolute;
        top: 30px;
        left: 30px;
        width: 90%;
    }
    #oscampus .osc-overlay-inner {
        background: #fff;
        padding: 30px;
        width: 60%;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        -o-border-radius: 4px;
    }
</style>

<div id="signup-overlay">
    <div class="osc-overlay-inner">
        <h3>Become a member to view this session</h3>
        <?php
        if (!empty($signupPage)) :
            ?>
            <div><?php echo JHtml::_('link', $signupPage, 'Signup here!', 'class="osc-btn osc-btn-main"'); ?></div>
            <?php
        endif;
        ?>
    </div>
</div>

<script>
    (function($) {
        $('#signup-overlay').appendTo('.oscampus-lesson-content');
    })(jQuery);
</script>
