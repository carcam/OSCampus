<?php
/**
 * @package    Oscampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class OscampusView extends JViewLegacy
{
    /**
     * @return JObject
     */
    public function getState($property = null, $default = null)
    {
        if ($model = $this->getModel()) {
            return $model->getState($property, $default);
        }

        return $default;
    }
}
