<?php
/**
 * Created by PhpStorm.
 * User: hossein
 * Date: 5/21/2017
 * Time: 9:01 PM
 */

namespace SilverPHPMail;


abstract class RowSet implements \SeekableIterator, \Countable, \ArrayAccess
{
    protected $_data = array();
    protected $_pointer = 0;
    protected $_count = 0;
    function __construct(array $config)
    {
        if (isset($config['data'])) {
            $this->_data       = $config['data'];
        }
        $this->_count = count($this->_data);
    }

    /**
     * @param int $position
     * @param bool $seek
     * @return Mail
     */
    public function getRow($position, $seek = false)
    {
       if(!isset($this->_data[$position]))
           return false;

        return $this->_data[$position];
    }

    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        // return the row object
        return $this->getRow($this->_pointer);
    }

    public function next()
    {
        ++$this->_pointer;
    }

    public function valid()
    {
        return $this->_pointer >= 0 && $this->_pointer < $this->_count;
    }

    public function seek($position)
    {
        $position = (int) $position;

        $this->_pointer = $position;
        return $this;
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[(int) $offset]);
    }

    public function offsetGet($offset)
    {
        $offset = (int) $offset;

        $this->_pointer = $offset;

        return $this->current();
    }
    public function offsetSet($offset, $value)
    {
    }
    public function offsetUnset($offset)
    {
    }
    public function key()
    {
        return $this->_pointer;
    }
    public function count()
    {
        return $this->_count;
    }
}