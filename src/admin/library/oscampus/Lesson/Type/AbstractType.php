<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\Lesson\Type;

use Oscampus\Lesson;

defined('_JEXEC') or die();

abstract class AbstractType
{
    /**
     * @var Lesson
     */
    protected $lesson = null;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    abstract public function render();

    public function __toString()
    {
        return get_class($this);
    }
}
