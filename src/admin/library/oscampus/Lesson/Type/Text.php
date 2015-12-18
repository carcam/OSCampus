<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Oscampus\Lesson\Type\AbstractType;

defined('_JEXEC') or die();

class Text extends AbstractType
{
    public function render()
    {
        return $this->lesson->content;
    }
}
