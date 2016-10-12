<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusViewCertificate extends OscampusViewPdf
{
    /**
     * @var OscampusModelCertificate
     */
    protected $model = null;

    /**
     * @var object
     */
    protected $certificate = null;

    /**
     * @var string
     */
    protected $imagePath = null;

    public function display($tpl = null)
    {
        $config = OscampusComponentHelper::getParams();
        $this->imagePath = $config->get('certificateImage');

        if (!$this->imagePath) {
            $this->redirect('No Certificate image selected');
        } elseif (!is_file(JPATH_SITE . $this->imagePath)) {
            $this->redirect('Certificate image files was not found');
        }
        $this->image = JPATH_SITE . $this->imagePath;

        $this->model = $this->getModel();

        try {
            $this->certificate = $this->model->getItem();

        } catch (Exception $e) {
            $this->redirect($e->getMessage());
        }

        parent::display($tpl);
    }
}
