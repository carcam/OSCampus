<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusViewLesson extends OscampusViewSite
{
    protected $lesson = null;

    public function display($tmpl = null)
    {
        /** @var OscampusModelLesson $model */
        $model = $this->getModel();

        $this->lesson = $model->getLesson();

        parent::display($tmpl);
    }
}
