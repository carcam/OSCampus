<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();


if ($this->lesson->description && $this->lesson->isAuthorised()) :
    ?>
    <div class="osc-section oscampus-lesson-description">
        <?php echo $this->lesson->description; ?>
    </div>
    <?php
endif;

