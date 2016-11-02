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
     * @return void
     */
    protected function setup()
    {
        // For use in subclasses
    }

    /**
     * Render the view
     *
     * @param  string $tpl
     *
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->setup();

        echo $this->displayHeader();
        parent::display($tpl);
        echo $this->displayFooter();
    }

    /**
     * Display a header on admin pages
     *
     * @return string
     */
    protected function displayHeader()
    {
        // To be set in subclasses
        return '';
    }

    /**
     * Display a standard footer on all admin pages
     *
     * @return string
     */
    protected function displayFooter()
    {
        // To be set in subclassess
        return '';
    }

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
