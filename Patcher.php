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

    public function writeIPS() : String
    {
        $byte_array = "";
        $byte_array .= hex2bin("5041544348"); #add header

        foreach($this->changes as $change){
            #first a 6 byte offset
                $offset = $change->offset;
                $offset = Helpers::padHex($offset,6);

            #then a 2 byte length of payload in bytes

            $changehex = $change->hex;

            /*while(strlen($changehex) > 250) {
                #need to grab a substring
                $subhex = substr($changehex, 0,250);

                #write with the new object
                $this->writeHexToBin($subhex, $byte_array, $offset);
                #make the changehex the other half of the substring, and move the offset

                $changehex = substr($changehex, 249);
                $offset = Helpers::padHex(dechex(hexdec($offset)+250),6);

            }*/

            $this->writeHexToBin($changehex, $byte_array, $offset);

        }


        $byte_array .= hex2bin("454f46"); #add end of file
        return $byte_array;



    }

    public function writeHexToBin($changehex, &$byte_array, $offset)
    {
        $byte_array .= hex2bin($offset);
        if (strlen($changehex) % 2 === 1) {
            $changehex = $changehex . "0";
        }
        $length = strlen($changehex)/2;
        $lengthhex = dechex($length);

        /*if (strlen($lengthhex) % 2 === 1) {
            $lengthhex = "0" . $lengthhex;
        }*/
        $lengthhex = Helpers::padHex($lengthhex,4);
        $byte_array .= hex2bin($lengthhex);
        #then the payload

        $byte_array .= hex2bin($changehex);
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