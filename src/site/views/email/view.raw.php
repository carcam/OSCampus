<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewEmail extends OscampusViewSite
{
    public function display($tpl = null)
    {
        $app = OscampusFactory::getApplication();

        if ($layout = $app->input->getCmd('layout')) {
            $this->setLayout($layout);
        }

        parent::display($tpl);
    }
}
