<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

namespace Oscampus;

use JDatabase;
use JUser;
use OscampusFactory;

defined('_JEXEC') or die();

class UserActivity extends AbstractBase
{
    /**
     * @var JUser
     */
    public $user = null;

    /**
     * @var array[]
     */
    protected $lessons = array();

    public function __construct(JDatabase $dbo, JUser $user = null)
    {
        parent::__construct($dbo);

        $this->user = $user ?: OscampusFactory::getUser();
    }

    public function setUser(JUser $user)
    {
        if ($user->id != $this->user->id) {
            $this->user = $user;
            $this->lessons = array();
        }
    }

    public function getCourse($id)
    {
        if ($this->user->id) {
            if (!isset($this->lessons[$id])) {
                $query = $this->dbo->getQuery(true)
                    ->select('ul.*')
                    ->from('#__oscampus_lessons lesson')
                    ->innerJoin('#__oscampus_modules module ON module.id = lesson.modules_id')
                    ->leftJoin('#__oscampus_users_lessons ul ON ul.lessons_id = lesson.id')
                    ->where(
                        array(
                            'ul.users_id = ' . $this->user->id,
                            'module.courses_id = ' . (int)$id
                        )
                    )
                    ->order('module.ordering ASC, lesson.ordering ASC');

                $this->lessons[$id] = $this->dbo->setQuery($query)->loadObjectList('lessons_id');
            }
        }

        return $this->lessons[$id];
    }
}
