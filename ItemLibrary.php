<?php


namespace TGL\MapGen;
use Exception;

class ItemLibrary
{
    public static function getItemLibrary()
    {
        return [
            ["21","05","39","35"],
            ["24","23","26"],
            ["36","25"],
            ["28","27"],
            ["29","2A","37"],
            ["01","2B","2C"],
            ["0A","2D","2E","02"],
            ["03","2F","30"],
            ["31","38","04","32"],
            ["06","33","34","08","07"],
            ["09"]
        ];

    }




    public static function getItemBlocks()
    {
        return ["AE94","C994","BE94","B394"];
    }



    public static function getMiniboss(int $area)
    {
        switch ($area) {
            case 0:
                return ["0B","0C"];
            case 1:
                return ["0D", "0E"];
            case 2:
                return ["0F","10"];
            case 3:
                return ["12","11"];
            case 4:
                return ["13","14"];
            case 5:
                return ["15","16"];
            case 6:
                return ["17","18"];
            case 7:
                return ["19","1A"];
            case 8:
                return ["1B","1C"];
            case 9:
                return ["1D","1E"];
            case 10:
                return ["1F","20"];
            default:
                $error = 'Requested Invalid Area';
                throw new Exception($error);

        }
    }


    public static function getTextBlock(int $area)
    {
        switch ($area) {
            case 0:
                return ["00","01","02","03","10","12"]; //removed "00" because we'll place it manually earlier
            case 1:
            case 6:
            case 8:
                return [];
            case 2:
                return ["0C"];
            case 3:
                return ["0D"];
            case 4:
                return ["0E"];
            case 5:
                return ["0F"];
            case 7:
                return ["11"];
            case 9:
                return ["13"];
            case 10:
                return ["14"];
            default:
                $error = 'Requested Invalid Area';
                throw new Exception($error);

        }
    }


    public static function getMultiShopLibrary()
    {
        return [
            [],
            [],
            ["3F"],
            [],
            ["41"],
            [],
            ["42"],
            ["40"],
            [],
            [],
            ["43"]
        ];
    }
    public static function getSingleShopLibrary()
    {
        return [
            ["3D","3E","3C","3B","3A"],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            []
        ];


    }

    //transitionType is 0 for none, 1 for area, 2 for corridor
    //direction is 1up, 2down, 3left, 4right
    public static function getRandomRoomBlock(bool $hasChips, int $transitionType, int $direction)
    {
        if(!$hasChips&&$transitionType === 0)
        {
            //    6894 up arrow complete
            //    3d94 left arrow smashed
            //    4694 left arrow with red tail
            //    5094 right arrow smashed
            //    8794 down arrow smashed
            //    3f94 left arrow smashed
            //    8c94 down arrow smashed into a checkerboard pattern
            //    5d94 right arrow with a bunch of red blocks added in
            //    5294 right arrow with a smashed shadow
            //    e394 smashed block in an odd offset grid thing
            //    dd94 random block corners going away from eachother
            //    df94 a bunch of 2x2 squares
            //    d994 a bunch of u's in a cycle
            //    d194 red block us in a rotation
            //    d794 a bunch of 2x2 squares but now red
            //    d594 pointy small triangles but now in red blocks
            $values = ["6894","3d94","4694","5094","8794","3f94","8c94","5d94","5294","e394","dd94","df94","d994","d194","d794","d594"];
            return $values[array_rand($values)];
        }
        else if($hasChips&&$transitionType === 0)
        {
            //    6f94 up arrow with back smashed and 2 unrevealed chips
            //    4194 right arrow with 2 blue chips cut out
            //    9d94 arrow pointing down with a bunch of unrevealed chips in it
            $values = ["6f94","4194","9d94"];
            return $values[array_rand($values)];

        }
        //area
        else if (!$hasChips&&$transitionType === 1 && $direction === 1) //up
        {
            //    1c95 a0 area transition going up with a bunch of extra red blocks
            return "1c95";
        }
        else if (!$hasChips&&$transitionType ===1 && $direction === 2) //down
        {
            //    3c95 A0 area transition going down with 5 red blocks above
            //    4995 an area transition going down with 4 cut off blocks
            $values = ["3c95","4995"];
            return $values[array_rand($values)];

        }
        else if (!$hasChips&&$transitionType ===1 && $direction === 3) //left
        {
            //    e994 a0 area transition pattern going left
            //    f394 area transition going left with an upsidedown L cut out of it
            //    eb94 area transition going left with some blocks that are red
            $values = ["e994","f394","eb94"];
            return $values[array_rand($values)];

        }
        else if (!$hasChips&&$transitionType ===1 && $direction === 4) //right
        {
            //    0895 a0 area transition going right with 2 extra red blocks
            return "0895";
        }
        //area with chips
        else if ($hasChips&&$transitionType ===1 && $direction === 1) //up
        {
            //    1795 area transition up with some chips to pull up
            //    2995 area transition up with 2 blue chips and some random holes
            //    2495 area transition up with a chip block in the center of the room


            $values = ["1795","2995","2495"];
            return $values[array_rand($values)];

        }
        else if ($hasChips&&$transitionType ===1 && $direction === 2) //down
        {
            //    3795 area transition down with a chip block in the middle
            //    4495 area transition down with a blue chip in the middle


            $values = ["4495","3795"];
            return $values[array_rand($values)];

        }
        else if ($hasChips&&$transitionType ===1 && $direction === 3) //left
        {
            //    fb94 area transition left with 4 chip tiles

            return "fb94";
        }
        else if ($hasChips&&$transitionType ===1 && $direction === 4) //right
        {
            //    0395 area transition right with some chips to pull up
            //    0d95 area transition to the right with 4 blue chips in the middle

            $values = ["0395","0d95"];
            return $values[array_rand($values)];

        }

        //corridor
        else if (!$hasChips && $transitionType === 2 && $direction === 1)//up
        {
            //    7a95 corridor topper with up exit 6 point
            //    8595 corridor topper up with 8 point
            $values =["7a95","8595"];
            return $values[array_rand($values)];

        }
        else if (!$hasChips && $transitionType === 2 && $direction === 2)//down
        {
            //    9095 corridor topper with down exit 6 point
            //    9B95 corridor topper with down exit 8 point
            $values = ["9095","9B95"];
            return $values[array_rand($values)];

        }
        else if (!$hasChips && $transitionType === 2 && $direction === 3)//left
        {
            //    4e95 corridor topper with left exit 6 point
            //    5995 corridor topper left with 8 4oint
            $values = ["4e95","5995"];
            return $values[array_rand($values)];

        }
        else if (!$hasChips && $transitionType === 2 && $direction === 4)//right
        {
            //    6495 corridor topper with right exit 6 point
            //    6f95 corridor topper with right exit 8 point
            $values = ["6495","6f95"];
            return $values[array_rand($values)];

        }
        else if ($hasChips&&$transitionType ===2)
        {
            throw new Exception("tried to place a chip decoration on a corridor, the game has none of those");
        }
        throw new Exception("somehow didn't hit an if block on the decoration placement");

    }

    public static function getPChipRoom() //this can be added to the unimportant rooms of a0
    {
        //ea95 the p chip rooms
        return "ea95";
    }

    public static function getCardinalLetter(string $letter)//N S E or W
    {
        switch(strtoupper($letter))
        {
            case "N":
                return "a695";
            case "S":
                return "b495";
            case "E":
                return "c895";
            case "W":
                return "d995";
            default:
                throw new Exception("don't request bad cardinal letters...");
        }
    }

}