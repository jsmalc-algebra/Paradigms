<?php

class ChronologicalIterator implements Iterator
{
    private $collection;
    private $position = 0;
    private $reverse = false;

    public function __construct($collection, $reverse = false){
        $this->collection = $collection;
        $this->reverse = $reverse;
    }
    public function current(){
        return $this->collection->getItems()[$this->position];
    }
    public function key(): int
    {
        return $this->position;
    }
    public function next(): void
    {
        if (!$this->reverse) $this->position++;
        else $this->position--;
    }
    public function valid(): bool
    {
        return isset($this->collection->getItems()[$this->position]);
    }
    public function rewind()
    {
        if ($this->reverse) $this->position = count($this->collection->getItems()) - 1;
        else $this->position = 0;
    }
}