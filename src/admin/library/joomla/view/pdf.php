<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusViewPdf extends OscampusViewSite
{
    public function display($tpl = null)
    {
        if (!defined('K_TCPDF_THROW_EXCEPTION_ERROR')) {
            define('K_TCPDF_THROW_EXCEPTION_ERROR', true);
        }

        if (!class_exists('TCPDF')) {
            $this->redirect('TCPDF Library has not been installed');
        }

        parent::display($tpl);
        jexit();
    }

    /**
     * Initialise and return the PDF generator class
     *
     * @param int    $width
     * @param int    $height
     * @param string $orientation
     * @param string $units
     *
     * @return TCPDF
     */
    protected function getGenerator($width = 8, $height = 11, $orientation = 'P', $units = 'in')
    {
        return new TCPDF($orientation, $units, array($width, $height));
    }

    /**
     * Provide a standard means of handling errors. Since we're supposed to be generating a
     * PDF file for download, we need to get back to a normal html page for displaying error
     * messages. We'll go back to the referring page if we can.
     *
     * @param string $message
     * @param string $type
     */
    protected function redirect($message, $type = 'error')
    {
        $app = OscampusFactory::getApplication();

        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE);
        } else {
            $referer = JURI::base();
        }

        $app->redirect($referer, $message, $type);
    }
}
