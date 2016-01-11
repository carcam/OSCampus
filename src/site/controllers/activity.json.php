<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class OscampusControllerActivity extends OscampusControllerJson
{
    public function record()
    {
        $this->checkToken();

        $app       = OscampusFactory::getApplication();
        $container = OscampusFactory::getContainer();

        $lessonId = $app->input->getInt('lessonId');
        $score    = $app->input->getInt('score', 100);
        $data     = $app->input->getString('data') ?: null;

        if ($lessonId) {
            $lesson = $container->lesson->loadById($lessonId);

            $container->activity->recordProgress($lesson, $score, $data);
        }

        jexit();
    }
}
