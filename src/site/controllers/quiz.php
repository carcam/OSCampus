<?php
/**
 * @package    OSCampus
 * @contact    www.joomlashack.com, help@joomlashack.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\Lesson\Type\Quiz;

defined('_JEXEC') or die();

class OscampusControllerQuiz extends OscampusControllerBase
{
    public function display($cachable = false, $urlparams = array())
    {
        echo $this->getTask() . ' is under construction';
    }

    public function grade()
    {
        $this->checkToken();

        $app       = OscampusFactory::getApplication();
        $container = OscampusFactory::getContainer();

        if ($lessonId = $app->input->getInt('lid')) {
            $pathwayId = $app->input->getInt('pid');
            $responses = $app->input->get('questions', array(), 'array');

            $lesson   = $container->lesson->loadById($lessonId, $pathwayId);
            $activity = $container->activity;

            $activity->recordProgress($lesson, 0, $responses);

            $app->redirect(JURI::getInstance());
        }

    }
}
