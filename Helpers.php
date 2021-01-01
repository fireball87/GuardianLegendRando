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


}