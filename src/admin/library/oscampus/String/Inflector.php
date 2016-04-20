<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus\String;

defined('_JEXEC') or die();

class Inflector extends \Joomla\String\Inflector
{
    protected function __construct()
    {
        parent::__construct();

        $this
            ->addWord('course', 'courses')
            ->addWord('lesson', 'lessons')
            ->addWord('pathway', 'pathways')
            ->addWord('tag', 'tags')
            ->addWord('teacher', 'teachers');
    }
}
