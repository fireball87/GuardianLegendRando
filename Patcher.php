<?php


namespace TGL\MapGen;

class Change
{
    public string $hex;
    public string $offset;
    function __construct(string $hex, string $offset)
    {
        $this->hex = $hex;
        $this->offset = $offset;
    }
}
class Patcher
{
    private array $changes = [];

    /*public function addChange(Change $change)
    {
        array_push($changes,$change);
    }*/

    public function addChange(string $hex, string $offset)
    {
        array_push($this->changes,new Change($hex,$offset));
    }

    public function writeRom(string $filename, string $sourceData)
    {
        $file = fopen($filename, "w") or die("Unable to open file!");
        $patched = $sourceData;

        foreach($this->changes as $change){
            $offset = hexdec($change->offset) * 2;
            $patched = substr_replace($patched,$change->hex,$offset,strlen($change->hex));
        }
        fwrite($file, hex2bin($patched));
        fclose($file);
    }
}