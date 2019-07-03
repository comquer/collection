<?php declare(strict_types=1);

namespace Comquer\Collection;

use Countable;
use Iterator;

abstract class IterableAndCountable implements Iterator, Countable
{
    /** @var array */
    private $elements = [];

    public function getElements() : array
    {
        return $this->elements;
    }

    protected function addElement($element) : void
    {
        $this->elements[] = $element;
    }

    protected function unsetElement($key) : void
    {
        unset($this->elements[$key]);
    }

    public function rewind()
    {
        return reset($this->elements);
    }

    public function current()
    {
        return current($this->elements);
    }

    public function key()
    {
        return key($this->elements);
    }

    public function next()
    {
        return next($this->elements);
    }

    public function valid() : bool
    {
        return key($this->elements) !== null;
    }

    public function count() : int
    {
        return count($this->elements);
    }
}