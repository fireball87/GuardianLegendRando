<?php


namespace TGL\MapGen;


class Helpers
{
    public static function inthex(int $hex): string
    {
        $enstring = dechex($hex);
        if(strlen($enstring) === 1)
        {
            return "0".$enstring;
        }
        return $enstring;
    }


    public static function padHex(string $hex, int $digits): string
    {
        while(true)
        {
            if(strlen($hex) >= $digits)
            {
                break;
            }
            else
            {
                $hex = "0".$hex;
            }
        }
        return $hex;
    }

}