<?php
/**
 * @package   com_oscampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
