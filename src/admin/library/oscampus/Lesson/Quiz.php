<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace Oscampus\Lesson;

defined('_JEXEC') or die();

class Quiz extends AbstractLesson
{
    protected function loadData(array $data)
    {
        parent::loadData($data);

        $this->content = json_decode($this->content);
    }
}
