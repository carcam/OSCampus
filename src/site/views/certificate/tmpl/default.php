<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

/** @var OscampusViewCertificate $this */

// This is the ugly hack to attempt to adjust to larger image
list($x, $y) = getimagesize($this->imagePath);
$adjust = "{$x}x{$y}" == '800x600' ? .13 : 0;

$fontSize = 18;
$width    = 8.25;
$height   = 6.25;

try {
    $pdf = $this->getGenerator($width, $height, 'L');

    $pdf->SetAuthor(JText::_('COM_OSCAMPUS_CERTIFICATE_AUTHOR'));
    $pdf->SetTitle(JText::_('COM_OSCAMPUS_CERTIFICATE_TITLE'));
    $pdf->SetSubject($this->certificate->name);
    $pdf->SetKeywords(JText::sprintf('COM_OSCAMPUS_CERTIFICATE_KEYWORDS', $this->certificate->course_title));

    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->SetFontSize($fontSize);

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Start a page
    $pdf->AddPage();
    $pdf->Image($this->imagePath, .06, .06, 8);

    // Student Name
    $pdf->SetXY(0, 2.24 + $adjust);
    $pdf->Write(1, $this->certificate->name, null, false, 'C');

    // Course Title
    $pdf->SetXY(0, 3.02 + $adjust);
    $pdf->Write(1, $this->certificate->course_title, null, false, 'C');

    // Domain name of the site
    $uri = OscampusFactory::getURI();
    $pdf->SetXY(0, 3.84 + $adjust);
    $pdf->Write(1, $uri->getHost(), null, false, 'C');

    // Instructor Name
    $pdf->SetXY(.7, 4.45 + $adjust);
    $pdf->Write(1, $this->certificate->teacher_name);

    // Completion date
    $pdf->SetXY(5.8, 4.45 + $adjust);
    $pdf->Write(1, $this->certificate->date->format('M j, Y'));

    // Wheee!
    $pdf->Output('certificate-' . $this->certificate->id . '.pdf', 'D');

} catch (Exception $e) {
    $this->redirect($e->getMessage());
}
