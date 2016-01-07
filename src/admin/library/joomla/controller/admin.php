<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');

abstract class OscampusControllerAdmin extends JControllerAdmin
{
    protected function checkToken()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
    }
}
