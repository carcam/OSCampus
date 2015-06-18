<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace Oscampus\Quiz;

defined('_JEXEC') or die();

class Choice
{
    /**
     * @var string
     */
    public $id = null;

    /**
     * @var null
     */
    public $text = null;

    /**
     * @var bool
     */
    public $correct = false;

    public function __construct($text, $correct = false)
    {
        $this->id      = md5($text);
        $this->text    = $text;
        $this->correct = $correct;
    }
}
