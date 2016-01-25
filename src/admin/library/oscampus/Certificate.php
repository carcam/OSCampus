<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabase;
use Juser;

defined('_JEXEC') or die();

/**
 * Class Certificate
 *
 * @package Oscampus
 */
class Certificate extends AbstractBase
{
    /**
     * @var JUser
     */
    protected $user = null;

    public function __construct(JDatabase $dbo, JUser $user)
    {
        parent::__construct($dbo);

        $this->user = $user;
    }

    public function award($courseId, $userId)
    {
        // @TODO: Create a certificate
    }

    /**
     * Creates a snapshot of the selected course
     *
     * @param int $courseId
     *
     * @return null|object
     */
    protected function snapshot($courseId)
    {
        $query = $this->dbo->getQuery(true)
            ->select(
                array(
                    'course.id',
                    'course.difficulty',
                    'course.length',
                    'course.title',
                    'course.released'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->where('course.id = ' . (int)$courseId);

        if ($course = $this->dbo->setQuery($query)->loadObject()) {
            $query = $this->dbo->getQuery(true)
                ->select(
                    array(
                        'lesson.id',
                        'module.title AS module_title',
                        'lesson.title',
                        'lesson.type'
                    )
                )
                ->from('#__oscampus_lessons AS lesson')
                ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
                ->where('module.courses_id = ' . (int)$courseId)
                ->order('module.ordering ASC, lesson.ordering ASC');

            $course->lessons = $this->dbo->setQuery($query)->loadObjectList('id');

            return $course;
        }

        return null;
    }
}
