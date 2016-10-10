<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewLesson $this */

$user = OscampusFactory::getUser();

$signupType = $user->guest ? 'signup.new' : 'signup.upgrade';
$signupPage = OscampusHelper::normalizeUrl($this->getParams()->get($signupType));

?>

<div id="signup-overlay">
    <div class="osc-overlay-inner">
        <h3><?php echo JText::_('COM_OSCAMPUS_LESSON_BECOME_A_MEMBER_TO_VIEW_LESSON'); ?></h3>
        <?php
        if (!empty($signupPage)) :
            ?>
            <div><?php echo JHtml::_('link', $signupPage, JText::_('COM_OSCAMPUS_LESSON_SIGNUP_HERE'), 'class="osc-btn osc-btn-main"'); ?></div>
            <?php
        endif;
        ?>
    </div>
</div>

<script>
    (function($) {
        $('#signup-overlay').appendTo('.osc-signup-box');
    })(jQuery);
</script>
