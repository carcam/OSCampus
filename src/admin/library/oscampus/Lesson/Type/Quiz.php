<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Oscampus\Lesson;
use Oscampus\Lesson\Type\AbstractType;

defined('_JEXEC') or die();

class Quiz extends AbstractType
{
    public function render()
    {
        ob_start();
        echo '<pre>';
        print_r(json_decode($this->lesson->content));
        echo '</pre>';

        $result = ob_get_clean();
        return $result;
    }
}
