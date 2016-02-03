<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/**
 * @var OscampusViewEmail        $this
 * @var OscampusModelCertificate $model
 */
$model = OscampusModel::getInstance('Certificate');

$certificate = $model->getItem();

$link = JHtml::_('osc.link.certificate', $certificate->id, null, null, true, true);
?>
<p>Congratulations <?php echo $certificate->name; ?>! You've passed <?php echo $certificate->course_title; ?></p>
<p>Download a pdf certificate from <?php echo JHtml::_('link', $link, $link); ?></p>
