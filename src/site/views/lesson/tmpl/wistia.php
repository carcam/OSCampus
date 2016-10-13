<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$component = OscampusFactory::getApplication()->input->getCmd('tmpl') === 'oscampus';

$classes = array(
    'osc-section',
    'oscampus-lesson-content',
    $this->lesson->isAuthorised() ? 'osc-authorised-box' : 'osc-signup-box'
);

if (!$component) :
    ?>
    <div class="osc-container oscampus-wistia" id="oscampus">
    <?php
endif;
?>
    <div class="osc-section">
        <h1 class="osc-lesson-title"><?php echo $this->lesson->title; ?></h1>
        <div class="osc-lesson-links">
            <?php echo $this->loadDefaultTemplate('navigation'); ?>
        </div>
    </div>
    <!-- .osc-lesson-links -->

    <div class="<?php echo join(' ', $classes); ?>">
        <?php echo $this->lesson->render(); ?>
    </div>
<?php
if ($this->lesson->isAuthorised()) {
    echo $this->loadDefaultTemplate('description');
    echo $this->loadDefaultTemplate('files');
}
echo OscampusHelper::renderModule('oscampus_lesson_bottom');

if (!$component) :
    ?>
    </div>
    <?php
endif;
