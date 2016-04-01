<?php
/**
 * @package   Oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.filter.input');

class OscampusFilterInput extends JFilterInput
{
    protected static $instances = array();

    /**
     * @param array $tagsArray
     * @param array $attrArray
     * @param int   $tagsMethod
     * @param int   $attrMethod
     * @param int   $xssAuto
     * @param int   $stripUSC
     *
     * @return OscampusFilterInput
     */
    public static function &getInstance(
        $tagsArray = array(),
        $attrArray = array(),
        $tagsMethod = 0,
        $attrMethod = 0,
        $xssAuto = 1,
        $stripUSC = -1
    ) {
        $sig = md5(serialize(array($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAuto)));

        if (empty(static::$instances[$sig])) {
            static::$instances[$sig] = new static($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAuto);
        }
        return static::$instances[$sig];
    }

    public function clean($source, $type = 'string')
    {
        switch (strtoupper($type)) {
            case 'ARRAY_KEYS':
                if (is_array($source)) {
                    $result = $this->cleanArray($source);
                } else {
                    $result = $this->_remove($this->_decode($value));
                }
                break;

            default:
                $result = parent::clean($source, $type);
                break;
        }

        return $result;
    }

    /**
     * Filter an array and its keys to strings. Will recognize a key
     * of 'username' and use the username filter
     *
     * @param $source
     *
     * @return array
     */
    protected function cleanArray(array $source)
    {
        $result = array();
        foreach ($source as $key => $value) {
            $key = $this->_remove($this->_decode($key));
            if (is_string($value)) {
                $filter       = ($key == 'username' ? 'username' : 'string');
                $result[$key] = $this->clean($value, $filter);
            } else {
                $result[$key] = $this->cleanArray($value);
            }
        }
        return $result;
    }
}
