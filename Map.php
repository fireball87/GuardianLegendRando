<?php


namespace TGL\MapGen;
use Exception;

require_once("Room.php");

class Map
{
    //the map is an array of Room objects
    //top left room is x=0,y=0 in game
    //it is 24 tiles wide and high ending on x=23, y=23

    //the array has the first value as y, and the second value as x, this is because this is what seems natural in php
    //meaning, i can print it by doing a foreach over a foreach
    //it is an array of rows of values

    public $data = NULL;

    function __construct() {
        //first let's give the array some space
        $this->data = array_fill(0, 24, array_fill(0, 24, NULL));
        foreach ($this->data as &$row) {
            foreach($row as &$item) {
                $item = new Room();
            }
        }
    }

    public function printAreas()
    {
        foreach ($this->data as $row) {
            foreach($row as $item) {
                $this_area = $item->area;
                if($this_area < 0||$this_area === 10)
                    echo $item->area,",";
                else
                    echo " ",$item->area,",";
            }
            echo "\n";
        }
    }

    public function drawExits()
    {
        foreach ($this->data as $row) {
            for ($i = 1; $i <= 3; $i++) {

                foreach ($row as $item) {

                    if ($i === 1) {
                        echo "╔═";
                        if ($item->exit_up) {
                            echo "░░";
                        } else {
                            echo "══";
                        }
                        echo "═╗";
                    }
                    if ($i === 2) {
                        if ($item->exit_left) {
                            echo "░░";
                        } else {
                            echo "║║";
                        }

                        if ($item->accessible) {
                            switch($item->room_type)
                            {
                                //0 normal, 1 save, 2 corridor, 3 text, 4 multi_shop, 5 single_shop, 6 miniboss, 7 item drop
                                case 1:
                                    echo "SV";
                                    break;
                                case 2:
                                    $corridor = $item->enemy_type;
                                    if($corridor <10)
                                    {
                                        echo "X".$corridor;
                                    }else if($corridor == 10)
                                    {
                                        echo "X"."A";
                                    }
                                    else if($corridor == 21)
                                    {
                                        echo "X"."F";
                                    }
                                    else if($corridor == 20)
                                    {
                                        echo "x"."A";
                                    }
                                    else
                                    {
                                        echo "x".($corridor-10);
                                    }
                                    //echo substr("00{$item->enemy_type}", -2);
                                    break;
                                case 3:
                                    echo "TX";
                                    break;
                                case 4:
                                    echo "S".(hexdec($item->item_id)-hexdec("3F"));
                                    //echo "S3";
                                    break;
                                case 5:
                                    echo "s".(hexdec($item->item_id)-hexdec("3A"));
                                    //echo "S1";
                                    break;
                                case 6:
                                    echo $item->item_id;
                                    //echo "MB";
                                    break;
                                case 7:
                                    echo $item->item_id;
                                    //echo "IT";
                                    break;
                                default:
                                    echo "░░";

                            }
                        } else {
                            echo "╬╬";
                        }
                        if ($item->exit_right) {
                            echo "░░";
                        } else {
                            echo "║║";
                        }
                    }
                    if ($i === 3) {

                        echo "╚═";
                        if ($item->exit_down) {
                            echo "░░";
                        } else {
                            echo "══";
                        }
                        echo "═╝";
                    }
                }
                echo "\n";

            }
        }
    }


    public function writeHex(bool $log)
    {
        $finalhex = "";
        foreach ($this->data as $yPos => $row) {
            foreach ($row as $xPos => $item) {
                if(!$item->accessible)
                {
                    $finalhex .= "80";
                    //break 1;
                }
                else {
                    //1 is down
                    //2 is right
                    //4 is left
                    //8 is up

                    //these are flags so add together, examples picked from game below

                    $directionbit = 0;
                    if ($item->exit_down)
                        $directionbit += 1;
                    if ($item->exit_right)
                        $directionbit += 2;
                    if ($item->exit_left)
                        $directionbit += 4;
                    if ($item->exit_up)
                        $directionbit += 8;
                    $directionhex = dechex($directionbit);

                    $area = dechex($item->area);

                    switch($item->room_type)
                    {
                        //0 normal, 1 save, 2 corridor, 3 text, 4 multi_shop, 5 single_shop, 6 miniboss, 7 item drop
                        case 0:
                            $finalhex .= $this->normalRoomHex($directionhex,$area,$item->enemy_type,$item->block_set,$item->chip_tile);
                            break;
                        case 1:
                            $finalhex .= $this->saveRoomHex($directionhex,$area);
                            break;
                        case 2:
                            $finalhex .= $this->corridorHex($directionhex,$area,$item->enemy_type);
                            break;
                        case 3:
                            $finalhex .= $this->textHex($directionhex,$area,$item->item_id);
                            break;
                        case 4:
                            $finalhex .= $this->shopHex($directionhex,$area,$item->item_id,true);
                            break;
                        case 5:
                            $finalhex .= $this->shopHex($directionhex,$area,$item->item_id,false);
                            break;
                        case 6:
                            $finalhex .= $this->miniBossHex($directionhex,$area,$item->item_id);
                            break;
                        case 7:
                            $finalhex .= $this->itemRoomHex($directionhex,$area,$item->enemy_type,$item->block_set,$item->item_id);
                            break;

                    }
                }
            }

        }
        if ($log)
            echo strtoupper($finalhex);
        return $finalhex;
    }


    private function itemRoomHex(string $directionhex, string $area, int $enemy_type, string $block_type, string $item_id)
    {
        //give me a normal empty room
        $requiredkey = $this->getKeyFromAreaForRoomsThatCouldHaveEnemiesButDont($area);
        $meaninglessbyte = 0;


        $length = 5;
        $enemy_string = "";
        if($enemy_type !== 0)
        {
            $requiredkey = $this->getKeyFromAreaForMostRooms($area);

            $length ++;
            $enemy_string = dechex($enemy_type);
            if(strlen($enemy_string)===1)
            {
                $enemy_string = "0".$enemy_string;
            }
        }




        return  "3" . $length . $directionhex . $requiredkey .$meaninglessbyte . $area . $item_id . $enemy_string . $block_type;
    }


    private function normalRoomHex(string $directionhex, string $area, int $enemy_type, $block_type, $chip_tile)
    {
        //give me a normal empty room
        $requiredkey = $this->getKeyFromAreaForRoomsThatCouldHaveEnemiesButDont($area);
        $meaninglessbyte = 0;

        $room_type = 0;

        $length = 2;
        $enemy_string = "";
        if($enemy_type !== 0)
        {
            $requiredkey = $this->getKeyFromAreaForMostRooms($area);

            $length ++;
            $enemy_string = dechex($enemy_type);
            if(strlen($enemy_string)===1)
            {
                $enemy_string = "0".$enemy_string;
            }
        }

        $block_string = "";
        if($block_type !== null)
        {
            $length += 2;
            $block_string = $block_type;
            if($chip_tile)
            {
                $room_type = 7;
                //there's no byte that actually makes sense to control this, my best bet is the 4th byte, as that changes, but 95% of the places just use the key
                //and it's not terribly consistant what the results are otherwise
                //75 7 D 0 8 20 9D94 - area 3
                //75 9 A 0 3 13 9D94 - area 8
                //
                //D 20
                //A 13
            }
            else
            {
                $room_type = 1;
            }
        }



        $value = $room_type . $length . $directionhex . $requiredkey .$meaninglessbyte . $area . $enemy_string . $block_string;
        //echo $room_type ." ". $length ." ". $directionhex ." ". $requiredkey ." ". $meaninglessbyte ." ". $area ." ". $enemy_string ." ". $block_string . "\n";
        return $value;
    }

    private function miniBossHex(string $directionhex, string $area, string $item_id)
    {
        $requiredkey = $this->getKeyFromAreaForMostRooms($area);

        return "43" . $directionhex . $requiredkey . "1" . $area . $item_id;

    }

    private function saveRoomHex(string $directionhex, string $area)
    {
        $requiredkey = $this->getKeyFromAreaForMostRooms($area);

        return "82" . $directionhex . $requiredkey . "01";
    }

    private function corridorHex(string $directionhex, string $area, int $corridor)
    {
        $requiredkey = $this->getKeyFromAreaForMostRooms($area);
        if($corridor === 1)
        {
            $requiredkey = "0";
        }

        $corridor_id = dechex(128+$corridor);
        return "82" . $directionhex . $requiredkey . $corridor_id;
    }

    private function shopHex(string $directionhex, string $area, string $shopId, bool $isMultiShop)
    {
        $requiredkey = $this->getKeyFromAreaForRoomsThatCouldHaveEnemiesButDont($area);
        $meaninglessbyte = 0;

        if($isMultiShop)
        {
            return "A3" . $directionhex . $requiredkey . $meaninglessbyte . "2" . $shopId;
        }
        else
        {
            return "A3" . $directionhex . $requiredkey . $meaninglessbyte . "6" . $shopId;
        }
    }

    private function textHex(string $directionhex, string $area, string $textId)
    {
        $requiredkey = $this->getKeyFromAreaForRoomsThatCouldHaveEnemiesButDont($area);
        $meaninglessbyte = 0;
        return "A3" . $directionhex . $requiredkey . $meaninglessbyte . "3" . $textId;
    }




    private function getKeyFromAreaForMostRooms(string $area) //using the hex string for the area, not the int
    {
        return dechex($this->getKeyFromAreaForRoomsThatCouldHaveEnemiesButDont($area)+8);
    }

    private function getKeyFromAreaForRoomsThatCouldHaveEnemiesButDont(string $area): int //using the hex string for the area, not the int
    {
        switch(strtoupper($area))
        {
            case "0":
                return 0;
            case "1":
            case "2":
                return 1;
            case "3":
                return 2;
            case "4":
                return 3;
            case "5":
            case "6":
                return 4;
            case "7":
            case "8":
                return 5;
            case "9":
                return 6;
            case "A":
                return 7;
            default:
                $error = 'invalid area';
                throw new Exception($error);

        }
        //a0 is 0
        //a1 is 1
        //a2 is 1
        //a3 is 2
        //a4 is 3
        //a5 is 4
        //a6 is 4
        //a7 is 5
        //a8 is 5
        //a9 is 6
        //a10 is 7
    }


    private function pickRandomEnemy()
    {

        $enemy = rand(0,41);
        $enstring = dechex($enemy);
        if(strlen($enstring) === 1)
        {
            return "0".$enstring;
        }
        return $enstring;
        //enemy table
        //01 blue bubble
        ////02 red carpet
        ////03 red carpet and blue bubble
        ////04 yellow ball
        ////05 yellow ball and red and blue hockey pucks
        ////06 red and blue hockey pucks
        ////07 red and blue spiders
        ////08 those tall alien dudes
        ////09 5 yellow bats
        ////0A blue and green balls
        ////0B red and orange balls
        ////0C yellow bats and red hockey pucks
        ////0D single carrot
        ////0E Blue balls and red spiders
        ////0F Transformers and red carpet
        ////10 3 yellow bats
        ////11 multiplication spider
        ////12 1 carrot and 2 tall alien transformer dudes
        ////13 red carpets and 2 blue spinny flowers
        ////14 those tall pointy hermet crab things
        ////15 2 carrots
        ////16 4 bats 3 hermet crabs
        ////17 3 hermet crabs 2 green balls
        ////18 2 red balls
        ////19 1 ice cube a bunch of blue spinny flowers
        ////1A bunch of red small spiders
        ////1B bunch of multiplication ice cubes
        ////1C 2 multiplication ice cubes
        ////1D balls of every color
        ////1E 2 vertical worm things
        ////1F 2 small blue spinny flowers
        ////20 4 yellow bats
        ////21 4 carrots
        ////22 red and blue  hockey pucks, and 2 tall transformer aliens
        ////23 2 big boss spiders
        ////24 a couple spinny flowers, a couple blue hockey pucks
        ////25 bats and spinnys
        ////26 red carpets again
        ////27 bubble dropping robot
        ////28 falling moons
        ////29 bunch of green balls
        ////2A bunch of balls of every color
        ////2B bubble dropping robots except now there's 2 of them
        ////2C vertical worms and red hockey pucks
        ////2D falling moons and red carpets
        ////2E falling moons and one blue spider boss
        ////2F one red spider boss and 2 blue spinnies

    }
}