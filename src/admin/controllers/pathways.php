<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerPathways extends OscampusControllerAdmin
{
    protected $text_prefix = 'COM_OSCAMPUS_PATHWAYS';

    public function getModel($name = 'Pathway', $prefix = 'OscampusModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
