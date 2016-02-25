<?php
/**
 * @package    OSCampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


class OscampusModelCourse extends OscampusModelAdmin
{
    /**
     * @var JObject
     */
    protected $item = null;

    /**
     * @param int $pk
     *
     * @return object|false
     */
    public function getItem($pk = null)
    {
        if ($this->item === null) {
            $this->item = parent::getItem($pk);

            if (!$this->item->id) {
                $this->item->setProperties(
                    array(
                        'released'  => date('Y-m-d'),
                        'access'    => 1,
                        'published' => 1,
                        'image'     => \Oscampus\Course::DEFAULT_IMAGE,
                        'pathways'  => array(),
                        'tags'      => array()
                    )
                );

            } else {
                $this->item->pathways = $this->getPathways($this->item->id);
                $this->item->tags     = $this->getTags($this->item->id);
                $this->item->files    = $this->getFiles($this->item->id);

                if ($this->item->introtext && $this->item->description) {
                    $this->item->description = trim($this->item->introtext)
                        . '<hr id="system-readmore" />'
                        . trim($this->item->description);
                }
            }
        }

        return $this->item;
    }

    /**
     * @param int $courseId
     *
     * @return string[]
     */
    public function getPathways($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db = $this->getDbo();

            $query = $db->getQuery(true)
                ->select('pathways_id')
                ->from('#__oscampus_courses_pathways')
                ->where('courses_id = ' . $courseId);

            $pathways = $db->setQuery($query)->loadColumn();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
            } else {
                return $pathways;
            }
        }

        return array();
    }

    /**
     * @param int $courseId
     *
     * @return string[]
     */
    public function getTags($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select('tags_id')
                ->from('#__oscampus_courses_tags')
                ->where('courses_id = ' . $courseId);

            $tags = $db->setQuery($query)->loadColumn();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
            } else {
                return $tags;
            }
        }

        return array();
    }

    /**
     * @param int $courseId
     *
     * @return object[]
     */
    public function getFiles($courseId = null)
    {
        if ($courseId = (int)($courseId ?: $this->getState($this->getName() . '.id'))) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->select(
                    array(
                        'file.id',
                        'file.path',
                        'file.title',
                        'file.description',
                        'flink.courses_id',
                        'flink.lessons_id',
                        'flink.ordering'
                    )
                )
                ->from('#__oscampus_files AS file')
                ->innerJoin('#__oscampus_files_links AS flink ON flink.files_id = file.id')
                ->where('flink.courses_id = ' . $courseId)
                ->order(
                    array(
                        'flink.ordering ASC',
                        'file.title ASC'
                    )
                );

            $files = $db->setQuery($query)->loadObjectList();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
            } else {
                return $files;
            }
        }

        return array();
    }

    /**
     * Due to m:m relationship to pathways, we do a special reordering here
     *
     * @param array $pks
     * @param array $order
     *
     * @return bool
     * @throws Exception
     */
    public function saveorder($pks = null, $order = null)
    {
        $app = OscampusFactory::getApplication();
        if ($pathwayId = $app->input->getInt('filter_pathway')) {
            $db  = $this->getDbo();
            $sql = 'UPDATE #__oscampus_courses_pathways SET ordering = %s WHERE courses_id = %s AND pathways_id = ' . $pathwayId;
            foreach ($pks as $index => $courseId) {
                $db->setQuery(sprintf($sql, $order[$index], $courseId))->execute();
                if ($error = $db->getErrorMsg()) {
                    $this->setError($error);
                    return false;
                }
            }

            return true;
        }

        $this->setError(JText::_('COM_OSCAMPUS_ERROR_COURSE_REORDER_PATHWAY'));
        return false;
    }

    protected function getReorderConditions($table)
    {
        $app = OscampusFactory::getApplication();

        if ($pathwayId = $app->input->getInt('filter_pathway')) {
            return array('pathways_id = ' . $pathwayId);
        }

        return array();
    }

    protected function prepareTable($table)
    {
        $descriptions = preg_split('#<.*?id=["\']system-readmore["\'].*?>#', $table->description, 2);

        $table->description = trim(array_pop($descriptions));
        $table->introtext   = trim(array_pop($descriptions));

        parent::prepareTable($table);
    }

    public function save($data)
    {
        if (parent::save($data)) {
            // Handle additional update tasks
            $courseId = (int)$this->getState($this->getName() . '.id');
            $pathways = empty($data['pathways']) ? array() : $data['pathways'];
            $tags     = empty($data['tags']) ? array() : $data['tags'];
            $ordering = empty($data['lessons']) ? array() : $data['lessons'];

            return $this->updatePathways($courseId, $pathways)
            && $this->updateJunctionTable(
                '#__oscampus_courses_tags.courses_id',
                $courseId,
                'tags_id',
                $tags
            )
            && $this->setLessonOrder($ordering);
        }

        return false;
    }

    /**
     * Special handling for pathways junction due to ordering being stored there
     *
     * @param int   $courseId
     * @param int[] $pathways
     *
     * @return bool
     */
    protected function updatePathways($courseId, array $pathways)
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__oscampus_courses_pathways')
            ->where('courses_id = ' . (int)$courseId);

        $oldPathways = $db->setQuery($query)->loadObjectList('pathways_id');
        $newPathways = array_flip($pathways);

        // Remove from unselected pathways
        if ($removePathways = array_diff_key($oldPathways, $newPathways)) {
            $query = $db->getQuery(true)
                ->delete('#__oscampus_courses_pathways')
                ->where(
                    array(
                        'courses_id = ' . (int)$courseId,
                        'pathways_id IN (' . join(',', array_keys($removePathways)) . ')'
                    )
                );
            $db->setQuery($query)->execute();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }
        }

        // Add to new pathways at bottom of list
        if ($addPathways = array_diff_key($newPathways, $oldPathways)) {
            $query = $db->getQuery(true)
                ->select('pathway.id, max(cp.ordering) AS lastOrder')
                ->from('#__oscampus_pathways AS pathway')
                ->leftJoin('#__oscampus_courses_pathways AS cp ON cp.pathways_id = pathway.id')
                ->where('pathway.id IN (' . join(',', array_keys($addPathways)) . ')')
                ->group('pathway.id');

            // Find last ordering # and verify pathway exists
            $ordering = $db->setQuery($query)->loadObjectList('id');

            $insertValues = array();
            foreach ($addPathways as $pid => $null) {
                if (isset($ordering[$pid])) {
                    $insertValues[] = join(',', array(
                            (int)$courseId,
                            (int)$pid,
                            (int)$ordering[$pid]->lastOrder + 1
                        )
                    );
                } else {
                    $this->setError(JText::sprintf('COM_OSCAMPUS_ERROR_MISSING_ADD_PATHWAY', $pid));
                    return false;
                }
            }

            $query = $db->getQuery(true)
                ->insert('#__oscampus_courses_pathways')
                ->columns(array('courses_id', 'pathways_id', 'ordering'))
                ->values($insertValues);

            $db->setQuery($query)->execute();
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }
        }

        return true;
    }

    /**
     * Do a forced/manual ordering update for all modules and lessons.
     * We are trusting that all ids are appropriate to this course.
     *
     * @param array $ordering
     *
     * @return bool
     */
    protected function setLessonOrder(array $ordering)
    {
        $db = $this->getDbo();

        $moduleOrder = 1;
        foreach ($ordering as $moduleId => $lessons) {
            $setModule = (object)array(
                'id'       => $moduleId,
                'ordering' => $moduleOrder++
            );
            $db->updateObject('#__oscampus_modules', $setModule, 'id');
            if ($error = $db->getErrorMsg()) {
                $this->setError($error);
                return false;
            }

            foreach ($lessons as $lessonOrder => $lessonId) {
                $set = (object)array(
                    'id'       => $lessonId,
                    'ordering' => $lessonOrder + 1
                );
                $db->updateObject('#__oscampus_lessons', $set, 'id');
                if ($error = $db->getErrorMsg()) {
                    $this->setError($error);
                    return false;
                }
            }
        }

        return true;
    }
}
