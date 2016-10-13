<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$classes = array(
    'osc-section',
    'oscampus-lesson-content',
    $this->lesson->isAuthorised() ? 'osc-authorised-box' : 'osc-signup-box'
);
?>
<div class="osc-container oscampus-embed" id="oscampus">
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadDefaultTemplate('navigation'); ?>
        </div>
    </div>

    <div
        class="<?php echo join(' ', $classes); ?>">
        <?php
        if ($content = $this->lesson->render()) :
            echo $content;
        else :
            ?>
            <div class="osc-alert-warning">
                <i class="fa fa-info-circle"></i>
                <?php echo JText::_('COM_OSCAMPUS_EMBED_UNRECOGNIZED'); ?>
            </div>
            <?php
        endif;
        ?>
    </div>

    <?php
    echo $this->loadDefaultTemplate('description');
    echo $this->loadDefaultTemplate('files');

    echo OscampusHelper::renderModule('oscampus_lesson_bottom');
    ?>
</div>
