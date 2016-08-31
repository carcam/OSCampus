<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

abstract class OscampusModelList extends JModelList
{
    /**
     * @var string[]
     */
    protected $accessList = array();

    /**
     * Create a where clause of OR conditions for a text search
     * across one or more fields. Optionally accepts a text
     * search like 'id: #' if $idField is specified
     *
     * @param string          $text
     * @param string|string[] $fields
     * @param string          $idField
     *
     * @return string
     */
    public function whereTextSearch($text, $fields, $idField = null)
    {
        $text = trim($text);

        if ($idField && stripos($text, 'id:') === 0) {
            $id = (int)substr($text, 3);
            return $idField . ' = ' . $id;
        }

        $searchText = $this->getDbo()->quote('%' . $text . '%');

        $ors = array();
        foreach ($fields as $field) {
            $ors[] = $field . ' LIKE ' . $searchText;
        }

        if (count($ors) > 1) {
            return sprintf('(%s)', join(' OR ', $ors));
        }

        return array_pop($ors);
    }

    /**
     * Provide a generic access search for selected field
     *
     * @param string $field
     * @param JUser  $user
     *
     * @return string
     */
    public function whereAccess($field, JUser $user = null)
    {
        $user   = $user ?: OscampusFactory::getUser();
        $userId = $user->id;

        if (!isset($this->accessList[$userId])) {
            $this->accessList[$userId] = join(', ', array_unique($user->getAuthorisedViewLevels()));
        }

        if ($this->accessList[$userId]) {
            return sprintf($field . ' IN (%s)', $this->accessList[$userId]);
        }

        return 'TRUE';
    }
}
