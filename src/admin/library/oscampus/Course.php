<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

defined('_JEXEC') or die();

abstract class Course
{
    const BEGINNER     = 'beginner';
    const INTERMEDIATE = 'intermediate';
    const ADVANCED     = 'advanced';

    const DEFAULT_IMAGE = 'media/com_oscampus/images/default-course.jpg';
}
