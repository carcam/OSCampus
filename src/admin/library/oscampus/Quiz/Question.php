<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015 Open Source Training, LLC. All rights reserved
 * @license
 */
namespace Oscampus\Quiz;

defined('_JEXEC') or die();

class Question
{
    /**
     * @var string
     */
    public $id = null;

    /**
     * @var string
     */
    public $text = null;

    /**
     * @var array
     */
    public $choices = array();

    public function __construct($text, array $choices = null)
    {
        $this->id = md5($text);
        $this->text = $text;

        if ($choices) {
            $this->loadChoices($choices);
        }
    }

    public function loadChoices(array $choices)
    {
        $this->choices = array();
        foreach ($choices as $row) {
            $choice = new Choice($row->text, $row->correct);
            $this->choices[$choice->id] = $choice;
        }
    }
}
