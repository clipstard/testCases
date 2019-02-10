<?php


class sourceReader
{
    /** @var bool|resource $file */
    protected $file;

    public function __construct($source)
    {
    $this->file = fopen($source, 'r');
    }


    public function getSourceContentAsStringsArray()
    {
        $result = [];
        while(($row = fgets($this->file)) !== null) {
            foreach(explode(" ", $row) as $item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function getSourceContentAsIntArray()
    {
        return array_map(function ($value) {return intval($value);}, $this->getSourceContentAsStringsArray());
    }
}