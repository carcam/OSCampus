<?php
/**
 * @package    Oscampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
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
