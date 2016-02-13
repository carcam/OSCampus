<?php
/**
 * @package    plg_search_oscampus
 * @contact    www.ostraining.com, support@ostraining.com
 * @copyright  2016 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();

class PlgSearchOscampus extends JPlugin
{
    protected $areas = array(
        'oscampus' => 'PLG_SEARCH_OSCAMPUS_OSCAMPUS'
    );

    public function __construct($subject, array $config)
    {
        parent::__construct($subject, $config);

        $this->loadLanguage();
    }

    public function onContentSearchAreas()
    {
        return $this->areas;
    }

    /**
     *
     * Return objects should be:
     * {
     *    href       : {string}
     *    title      : {string}
     *    section    : {string}
     *    created    : {string}
     *    text       : {string}
     *    browsernav : {0|1}
     * }
     *
     * @param   string $text     Target search string.
     * @param   string $phrase   Matching option (possible values: exact|any|all).
     *                           Default is 'any'
     * @param   string $ordering Ordering option (possible values: newest|oldest|popular|alpha|category).
     *                           Default is 'newest'
     * @param   mixed  $areas    An array if the search it to be restricted to areas or null to search all areas.
     *
     * @return  object[]  Search results.
     */

    public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
    {
        // See if we've been selected for searching
        if (is_array($areas)) {
            if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
                return array();
            }
        }

        $db = JFactory::getDbo();

        $lessonQuery = $db->getQuery(true)
            ->select(
                array(
                    'lesson.id AS lessons_id',
                    'module.courses_id',
                    'lesson.title',
                    $db->quote('lesson') . ' AS section',
                    'lesson.created',
                    $db->quote('') . ' AS introtext',
                    'lesson.description'
                )
            )
            ->from('#__oscampus_lessons AS lesson')
            ->innerJoin('#__oscampus_modules AS module ON module.id = lesson.modules_id')
            ->innerJoin('#__oscampus_courses AS course ON course.id = module.courses_id')
            ->where(
                array(
                    'lesson.published = 1',
                    'course.published = 1',
                    'course.released <= CURDATE()',
                    $this->getSearchAtom($text, $phrase, array('lesson.title', 'lesson.description'))
                )
            );


        $courseQuery = $db->getQuery(true)
            ->select(
                array(
                    '0',
                    'course.id',
                    'course.title',
                    $db->quote('course'),
                    'course.created',
                    'course.introtext',
                    'course.description'
                )
            )
            ->from('#__oscampus_courses AS course')
            ->where(
                array(
                    'course.published = 1',
                    'course.released <= CURDATE()',
                    $this->getSearchAtom($text, $phrase,
                        array('course.title', 'course.introtext', 'course.description'))
                )
            );

        $query = sprintf('(%s) UNION (%s)', $lessonQuery, $courseQuery);

        switch (strtolower($ordering)) {
            case 'alpha':
                $order = 'title ASC';
                break;

            case 'newest':
                $order = 'created DESC';
                break;

            case 'oldest':
                $order = 'created ASC';
                break;

            case 'popular':
            case 'category':
            default:
                $order = 'section ASC';
                break;
        }
        $query .= ' ORDER BY ' . $order;

        $limit = $this->params->get('limit', 10);
        $items = $db->setQuery($query, 0, $limit)->loadObjectList();

        foreach ($items as $item) {
            $item->href       = 'javascript:alert(\'Under Construction\');';
            $item->text       = ($item->introtext ?: $item->description) ?: 'What to use for empty descriptions?';
            $item->browsernav = 0;

            unset($item->introtext, $item->description);
        }

        return $items;
    }

    /**
     * Create a standard subclause for where to search for the selected text
     *
     * @param string       $text   Target search string
     * @param string       $phrase (any|all|exact)
     * @param string|array $fields The field names to search in
     *
     * @return string
     */
    protected function getSearchAtom($text, $phrase, $fields)
    {
        if (!is_array($fields)) {
            $fields = (array)$fields;
        }

        $db     = JFactory::getDbo();
        $phrase = strtolower($phrase) ?: 'any';
        $glue   = $phrase == 'all' ? 'AND' : 'OR';

        if ($phrase == 'exact') {
            $texts = array($text);
        } else {
            // @TODO: simple explode on space? Or use a regex on word boundaries?
            $texts = explode(' ', $text);
        }
        foreach ($texts as $idx => $text) {
            $texts[$idx] = $db->quote('%' . $text . '%');
        }

        $mainClause = array();
        foreach ($fields as $field) {
            $subClause = array();

            foreach ($texts as $text) {
                $subClause[] = $field . ' like ' . $text;
            }

            if (count($subClause) > 1) {
                $mainClause[] = '(' . join(') ' . $glue . '(', $subClause) . ')';
            } else {
                $mainClause[] = array_pop($subClause);
            }
        }

        if (count($mainClause) > 1) {
            $where = '((' . join(') OR (', $mainClause) . '))';
        } else {
            $where = array_pop($mainClause);
        }

        return $where;
    }
}
