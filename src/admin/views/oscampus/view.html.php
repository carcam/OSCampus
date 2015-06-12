<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewOscampus extends OscampusViewAdmin
{
    public function display($tpl = null)
    {
        $this->setToolBar(false);
        parent::display($tpl);
    }
}
