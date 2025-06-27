<?php
require "ChronologicalIterator.php";
class LogsIterator implements IteratorAggregate
{
    private $items=[];

    public function getItems(): array
    {
        return $this->items;
    }
    public function addItems(array $items)
    {
        $this->items = array_merge($this->items, $items);
    }
    public function getIterator(): Iterator
    {
        return new ChronologicalIterator($this);
    }
    public function getReverseIterator(): Iterator
    {
        return new ChronologicalIterator($this,true);
    }
}