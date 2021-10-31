<?php


namespace TGL\MapGen;
use Exception;

require_once("Map.php");
require_once("Room.php");
require_once("DivisionLibrary.php");
require_once("ItemLibrary.php");
require_once("SecretLibrary.php");



class Generator
{
    //The Concept map design process
    //create the map
    //subdivide the map into areas off a0 (trying to keep a tgl like grid map)
    //generate a multiple path maze for each area
    //find all candidates for connections between two touching areas (and select some based on flags)
    //number all areas and assign progression with the note that areas that will connect need to be sequential
    //place rooms and items and stuff, something i can consider more after i do maze gen



    public function run(array $itemLibrary, array $smallShopLibrary, array $multiShopLibrary, bool $secret,int $min_area_size,int $max_area_size, int $desiredConnections, int $desiredOneWayConnections, bool $portalOnlyOneWays,  int $decoration_odds, int $chip_odds, int $empty_room_odds, bool $log)
    {
        //create the map

        $map = new Map();

        //subdivide the map into areas off a0 (trying to keep a tgl like grid map)
        $this->subdivide_using_template($map);

        $this->shuffle_areas($map);

        $this->growA0ring( $map); //i need to place cardinal directions before mapping the starting points, because the starting points will grow out a0 and break the calculation
        $this->placeCardinalDirections($map);

        $this->find_starting_points($map);


        for($i = 1; $i<=10; $i++)
        {
            $this->grow_zone($i,rand($min_area_size,$max_area_size),$map);
            $this->addConnections($map, $i, $desiredConnections,false, false);
            $this->addConnections($map, $i, $desiredOneWayConnections,true, $portalOnlyOneWays);

        }
        $this->grow_zone(0,50,$map);
        $this->addConnections($map, 0, $desiredConnections,false, false);
        $this->addConnections($map, 0, $desiredOneWayConnections,true, $portalOnlyOneWays);



        $this->placeStartingPointRooms($map);
        $this->placeAreaDecorations($map);


        $this->placeStartingTextRoom($map);

        //place all my items
        for($i = 0; $i<=10; $i++)
        {
            $this->placeImportantRooms($map,$smallShopLibrary,$multiShopLibrary,$i,$secret);
            $this->placeItemsAndMinibosses($map,$itemLibrary,$i,$secret);
            $this->placeNonImportantRooms($map,$i,$secret);
        }

        $this->placeCorridorDecorations($map);
        $this->placeRandomDecorations($map, $decoration_odds, $chip_odds);


        $this->populateEnemies($map,$empty_room_odds);
        $bytes = $this->countAllRoomBytes($map);
        if($log)
        echo $bytes . "\n";
        if($bytes > 1916)
        {
            $error = 'Produced map that is too large';
            throw new Exception($error);
        }

        return $map;
    }


    private function countAllRoomBytes($map)
    {
        $total = 0;
        foreach ($map->data as $yPos => &$row) {
            foreach ($row as $xPos => &$item) {
                $total+=$item->countBytes();
            }
        }
        return $total;
    }
    private function subdivide_using_template(Map $map)
    {
        //select from a template
        $selected_entry = rand(0,DivisionLibrary::entry_count() - 1);
        $division = DivisionLibrary::get($selected_entry);

        $should_flip = rand(0,1);
        if($should_flip === 1)
        {
            $division = array_map(null, ...$division);
        }

        $rotate_times = rand(0,3);

        switch ($rotate_times) {
            case 3:$division=$this->rotate90($division);
            case 2:$division=$this->rotate90($division);
            case 1:$division=$this->rotate90($division);

        }


        foreach ($map->data as $yPos => &$row) {
            foreach($row as $xPos => &$item) {
                $item->area=$division[$yPos][$xPos];
            }
        }

    }

    private function shuffle_areas(Map $map)
    {
        $newareas = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        shuffle($newareas);

        foreach ($map->data as $yPos => &$row) {
            foreach ($row as $xPos => &$item) {
                if($item->area >=1 && $item->area<=10)
                {
                    $item->area = $newareas[$item->area - 1];
                }
            }
        }
    }



    //taken from https://stackoverflow.com/questions/30087158/how-can-i-rotate-a-2d-array-in-php-by-90-degrees answer
    function rotate90($mat) {
        $height = count($mat);
        $width = count($mat[0]);
        $mat90 = array();

        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                $mat90[$height - $i - 1][$j] = $mat[$height - $j - 1][$i];
            }
        }
        $mat90 = array_values($mat90);
        return $mat90;
    }


    private function find_starting_points(Map $map)
    {

        $possible_points = array_fill(0, 10, array());

        foreach ($map->data as $yPos => &$row) {
            foreach($row as $xPos => &$item) {
                if($item->area===-1) //only borders can be made starting positions
                {
                    $above_block = null;
                    $below_block = null;
                    $right_block = null;
                    $left_block = null;
                    if($yPos>0)
                    {
                        $above_block = $map->data[$yPos-1][$xPos];
                    }
                    if($yPos<23)
                    {
                        $below_block = $map->data[$yPos+1][$xPos];
                    }
                    if($xPos>0)
                    {
                        $left_block = $map->data[$yPos][$xPos-1];
                    }
                    if($xPos<23)
                    {
                        $right_block = $map->data[$yPos][$xPos+1];
                    }

                    /*if(!($above_block===0||$below_block===0||$right_block===0||$left_block===0))
                    {
                        continue;//if nothing we touch is a 0 we're done son, this block isn't a candidate
                    }*/
                    //it seems like the map rules for the standard game allows diagonals between a0 and other areas at their entry point
                    //because i don't know how to enforce the diagonals in one way without the other, i will not allow it for the moment
                    if($above_block&&$above_block->area===0)
                    {
                        if($below_block->area>0&&$below_block->area<=10)
                        {
                            array_push($possible_points[$below_block->area-1],array($yPos,$xPos,"up"));
                            continue;
                        }
                        continue;
                    }
                    if($below_block&&$below_block->area===0)
                    {
                        if($above_block->area>0&&$above_block->area<=10)
                        {
                            array_push($possible_points[$above_block->area-1],array($yPos,$xPos,"down"));
                            continue;
                        }
                        continue;
                    }
                    if($left_block&&$left_block->area===0)
                    {
                        if($right_block->area>0&&$right_block->area<=10)
                        {
                            array_push($possible_points[$right_block->area-1],array($yPos,$xPos,"left"));
                            continue;
                        }
                        continue;
                    }
                    if($right_block&&$right_block->area===0)
                    {
                        if($left_block->area>0&&$left_block->area<=10)
                        {
                            array_push($possible_points[$left_block->area-1],array($yPos,$xPos,"right"));
                            continue;
                        }
                        continue;
                    }
                }
            }
        }


        //now that we have a list of candidates lets pick one at random for each stage
        //TODO: this can pick diagonal rooms, make sure you can enter rooms in that setup, if you can i see no reason to fix it
        foreach ($possible_points as $area => $position_array) {
            if(count($position_array)<1)
            {
                $error = "Area "+($area+1)+" did not generate with any possible entry points.";
                throw new Exception($error);
            }
            $index = rand(0,count($position_array)-1);
            $value = $position_array[$index];

            $yPos = $value[0];
            $xPos = $value[1];

            $map->data[$yPos][$xPos]->area = 0;
            $map->data[$yPos][$xPos]->startingpoint=true;
            $map->data[$yPos][$xPos]->accessible=true;

            $dir = $value[2];
            if($dir=="down")
            {
                $map->data[$yPos][$xPos]->exit_down=true;
                $map->data[$yPos][$xPos]->exit_up=true;

                $map->data[$yPos-1][$xPos]->exit_down=true;
                $map->data[$yPos-1][$xPos]->startingpoint=true;
                $map->data[$yPos-1][$xPos]->accessible=true;

                $map->data[$yPos+1][$xPos]->exit_up=true;
                $map->data[$yPos+1][$xPos]->accessible=true;
            }
            elseif($dir=="up")
            {
                $map->data[$yPos][$xPos]->exit_down=true;
                $map->data[$yPos][$xPos]->exit_up=true;

                $map->data[$yPos+1][$xPos]->exit_up=true;
                $map->data[$yPos+1][$xPos]->startingpoint=true;
                $map->data[$yPos+1][$xPos]->accessible=true;

                $map->data[$yPos-1][$xPos]->exit_down=true;
                $map->data[$yPos-1][$xPos]->accessible=true;
            }
            elseif($dir=="right")
            {
                $map->data[$yPos][$xPos]->exit_left=true;
                $map->data[$yPos][$xPos]->exit_right=true;

                $map->data[$yPos][$xPos-1]->exit_right=true;
                $map->data[$yPos][$xPos-1]->startingpoint=true;
                $map->data[$yPos][$xPos-1]->accessible=true;

                $map->data[$yPos][$xPos+1]->exit_left=true;
                $map->data[$yPos][$xPos+1]->accessible=true;
            }
            elseif($dir=="left")
            {
                $map->data[$yPos][$xPos]->exit_left=true;
                $map->data[$yPos][$xPos]->exit_right=true;

                $map->data[$yPos][$xPos+1]->exit_left=true;
                $map->data[$yPos][$xPos+1]->startingpoint=true;
                $map->data[$yPos][$xPos+1]->accessible=true;

                $map->data[$yPos][$xPos-1]->exit_right=true;
                $map->data[$yPos][$xPos-1]->accessible=true;
            }

        }
    }


    private function grow_zone(int $zone, int $desired_size, Map &$map)
    {
        //find the starting point

        $possible_grow_points = array();
        foreach ($map->data as $yPos => &$row) {
            foreach($row as $xPos => &$item) {
                if($map->data[$yPos][$xPos]->area === $zone && $map->data[$yPos][$xPos]->accessible === true)
                {
                    array_push($possible_grow_points,[$yPos,$xPos]);
                    //break;
                }
            }
        }

        do
        {
            //take a grow point
            $index = rand(0,count($possible_grow_points)-1);
            $location = $possible_grow_points[$index];
            $yPos = $location[0];
            $xPos = $location[1];


            //pick a direction you wish to grow, 0 up, 1 left, 2 right, 3 down
            $grow_direction = rand(0,3);

            //randomize the preference of the next blocks
            $sequence = rand(0,5);
            switch($sequence)
            {
                // up left right down
                case 0:
                    $up = 0;
                    $left = 1;
                    $right = 2;
                    $down = 3;
                    break;
                //         1 up left down right
                case 1:
                    $up = 0;
                    $left = 1;
                    $down = 2;
                    $right = 3;
                    break;
                //         2 up right down left
                case 2:
                    $up = 0;
                    $right = 1;
                    $down = 2;
                    $left = 3;
                    break;
                //         3 up right left down
                case 3:
                    $up = 0;
                    $right = 1;
                    $left = 2;
                    $down = 3;
                    break;
                //         4 up down left right
                case 4:
                    $up = 0;
                    $down = 1;
                    $left = 2;
                    $right = 3;
                    break;
                //         5 up down right left
                case 5:
                    $up = 0;
                    $down = 1;
                    $right = 2;
                    $left = 3;
                    break;

            }




            $grew = false;
            for ($i = 0; $i <= 3; $i++) {
                $rem = ($grow_direction + $i)%4;
                //check to see if i can grow in this direction
                switch($rem)
                {
                    case $up:
                        if($yPos>0&&$map->data[$yPos-1][$xPos]->area===$zone&&!($map->data[$yPos-1][$xPos]->accessible))
                        {
                            //we can grow here oh boy
                            $map->data[$yPos][$xPos]->exit_up=true;
                            $map->data[$yPos-1][$xPos]->exit_down=true;
                            $map->data[$yPos-1][$xPos]->accessible=true;
                            array_push($possible_grow_points,[$yPos-1,$xPos]);
                            $grew = true;
                        }
                        break;
                    case $left:
                        if($xPos>0&&$map->data[$yPos][$xPos-1]->area===$zone&&!($map->data[$yPos][$xPos-1]->accessible))
                        {
                            //we can grow here oh boy
                            $map->data[$yPos][$xPos]->exit_left=true;
                            $map->data[$yPos][$xPos-1]->exit_right=true;
                            $map->data[$yPos][$xPos-1]->accessible=true;
                            array_push($possible_grow_points,[$yPos,$xPos-1]);
                            $grew = true;
                        }
                        break;
                    case $right:
                        if($xPos<23&&$map->data[$yPos][$xPos+1]->area===$zone&&!($map->data[$yPos][$xPos+1]->accessible))
                        {
                            //we can grow here oh boy
                            $map->data[$yPos][$xPos]->exit_right=true;
                            $map->data[$yPos][$xPos+1]->exit_left=true;
                            $map->data[$yPos][$xPos+1]->accessible=true;
                            array_push($possible_grow_points,[$yPos,$xPos+1]);
                            $grew = true;
                        }
                        break;
                    case $down:
                        if($yPos<23&&$map->data[$yPos+1][$xPos]->area===$zone&&!($map->data[$yPos+1][$xPos]->accessible))
                        {
                            //we can grow here oh boy
                            $map->data[$yPos][$xPos]->exit_down=true;
                            $map->data[$yPos+1][$xPos]->exit_up=true;
                            $map->data[$yPos+1][$xPos]->accessible=true;
                            array_push($possible_grow_points,[$yPos+1,$xPos]);

                            $grew = true;
                        }
                        break;
                }
                if($grew)
                    break;

            }

            if(!$grew)
            {
                //if i didn't grow, we need to remove this cell from the growable cells list
                //array_splice($possible_grow_points, $index, 1);
                unset($possible_grow_points[$index]);
                $temp = array_values($possible_grow_points);
                $possible_grow_points = $temp;
            }
            else
            {
                //otherwise lower the desired size
                $desired_size = $desired_size - 1;
            }


        } while ($desired_size >0 && count($possible_grow_points)>0);
    }


    private function growA0ring(Map $map)
    {
        //form the outside ring
        foreach ($map->data as $yPos => &$row) {
            foreach($row as $xPos => &$item) {
                if($map->data[$yPos][$xPos]->area === 0)
                {
                    //see if i'm next to a wall grow

                    $growvertically = false;
                    $growhorizontally = false;
                    if(($map->data[$yPos -1][$xPos]->area <= -1)||($map->data[$yPos+1][$xPos]->area <= -1))
                    {
                        $growhorizontally = true;
                    }
                    if(($map->data[$yPos][$xPos -1]->area <= -1)||($map->data[$yPos][$xPos+1]->area <= -1))
                    {
                        $growvertically = true;
                    }

                    if($growvertically)
                    {
                        $map->data[$yPos][$xPos]->accessible=true;
                        $map->data[$yPos][$xPos]->avoid_special=true;
                        //grow up if can
                        if($map->data[$yPos -1][$xPos]->area === 0)
                        {
                            $map->data[$yPos][$xPos]->exit_up = true;
                            $map->data[$yPos -1][$xPos]->accessible=true;
                            $map->data[$yPos -1][$xPos]->avoid_special=true;
                            $map->data[$yPos -1][$xPos]->exit_down=true;
                        }
                        //grow down if can
                        if($map->data[$yPos +1][$xPos]->area === 0)
                        {
                            $map->data[$yPos][$xPos]->exit_down = true;
                            $map->data[$yPos +1][$xPos]->accessible=true;
                            $map->data[$yPos +1][$xPos]->avoid_special=true;
                            $map->data[$yPos +1][$xPos]->exit_up =true;
                        }
                    }

                    if($growhorizontally)
                    {
                        $map->data[$yPos][$xPos]->accessible=true;
                        $map->data[$yPos][$xPos]->avoid_special=true;

                        //growleftifcan
                        if($map->data[$yPos][$xPos-1]->area === 0)
                        {
                            $map->data[$yPos][$xPos]->exit_left = true;
                            $map->data[$yPos][$xPos-1]->accessible=true;
                            $map->data[$yPos][$xPos-1]->exit_right=true;
                            $map->data[$yPos][$xPos-1]->avoid_special=true;

                        }
                        //growrightifcan
                        if($map->data[$yPos][$xPos+1]->area === 0)
                        {
                            $map->data[$yPos][$xPos]->exit_right = true;
                            $map->data[$yPos][$xPos+1]->accessible=true;
                            $map->data[$yPos][$xPos+1]->exit_left=true;
                            $map->data[$yPos][$xPos+1]->avoid_special=true;

                        }
                    }



                }
            }
        }
    }


    private function placeItemsAndMinibosses($map, array $itemsLibrary, $area, bool $secret)
    {


        $locations = $this->createListOfSuitableRooms($map, $area, false, false);


        $items_to_place = $itemsLibrary[$area];

        //minibosses are the same between secret and the normal items
        $minibosses_to_place = ItemLibrary::getMiniboss($area);

        $item_blocksets = ItemLibrary::getItemBlocks();



        foreach ($items_to_place as $item)
        {

            if(count($locations) > 0)
            {
                $index = array_rand($locations);

                $yPos = $locations[$index][0];
                $xPos = $locations[$index][1];
                $map->data[$yPos][$xPos]->room_type = 7;
                $map->data[$yPos][$xPos]->item_id = $item;
                $map->data[$yPos][$xPos]->block_set = $item_blocksets[array_rand($item_blocksets)];
                unset($locations[$index]);
                $temp = array_values($locations);
                $locations = $temp;

            }
            else
            {
                $error = 'map has no valid spot to place an item';
                throw new Exception($error);
            }

        }

        foreach ($minibosses_to_place as $item)
        {

            if(count($locations) > 0)
            {
                $index = array_rand($locations);

                $yPos = $locations[$index][0];
                $xPos = $locations[$index][1];
                $map->data[$yPos][$xPos]->room_type = 6;
                $map->data[$yPos][$xPos]->item_id = $item;

                unset($locations[$index]);
                $temp = array_values($locations);
                $locations = $temp;

            }
            else
            {
                $error = 'map has no valid spot to place a miniboss';
                throw new Exception($error);
            }

        }
    }

    private function placeStartingTextRoom(Map $map)
    {
            $yPos = 12;
            $xPos = 11;
            $map->data[$yPos][$xPos]->room_type = 3;
            $map->data[$yPos][$xPos]->item_id = "00";
    }
    private function placeImportantRooms(Map $map, array $singleShopLibrary, array $multiShopLibrary, int $area, bool $secret)
    {
        $locations = $this->createListOfSuitableRooms($map, $area, true, true);




        //place corridors
        if($area === 0)
        {
            $this->placeCorridor($map,21,$locations);
        }
        else if($area === 1)
        {
            $this->placeCorridor($map,11,$locations);
        }
        else if ($secret && $area === 4&&false)
        {
            //find a sutable c4 location
            $arrayCopy = $locations; //this actually copies the array
            shuffle($arrayCopy);
            $foundRoom=false;
            foreach($arrayCopy as $item)
            {
                //check the rooms if it is possible
                //[4][1]
                //[5][2][0]
                //   [3]

                //check that the rooms fit on the map
                if($item[0]-1>=0&&$item[0]+1<=23&&$item[1]-2>0)
                {
                    //if the room can be placed in space check all the rooms
                    $rooms = [[$item[0],$item[1]],[$item[0]-1,$item[1]-1],[$item[0],$item[1]-1],[$item[0]+1,$item[1]-1],[$item[0]-1,$item[1]-2],[$item[0],$item[1]-2]];
                    $workingRoomset = true;
                    foreach($rooms as $room)
                    {
                        $yPos = $room[0];
                        $xPos = $room[1];
                        if($map->data[$yPos][$xPos]->area !== 4||!$map->data[$yPos][$xPos]->room_type === 0)
                        {
                            $workingRoomset = false;
                            break;
                        }
                    }

                    if($workingRoomset)
                    {
                        $foundRoom = true;
                        foreach($rooms as $room)
                        {
                            $map->data[$room[0]][$room[1]]->accessible = true;
                            $map->data[$room[0]][$room[1]]->avoid_special = true;
                        }
                        //place the text room in map tile marked 0
                        $map->data[$item[0]][$item[1]]->exit_left = true;
                        $map->data[$item[0]][$item[1]]->room_type = 3;
                        $map->data[$item[0]][$item[1]]->item_id = "12";

                        //place the corridor in map tile marked 2
                        $map->data[$item[0]][$item[1]-1]->exit_left = true;
                        $map->data[$item[0]][$item[1]-1]->exit_right = true;
                        $map->data[$item[0]][$item[1]-1]->exit_up = true;
                        $map->data[$item[0]][$item[1]-1]->exit_down = true;
                        $map->data[$item[0]][$item[1]-1]->room_type = 2;
                        $map->data[$item[0]][$item[1]-1]->enemy_type = 4;

                        //exits for 1
                        $map->data[$item[0]-1][$item[1]-1]->exit_left = true;
                        $map->data[$item[0]-1][$item[1]-1]->exit_down = true;
                        //exits for 3
                        $map->data[$item[0]+1][$item[1]-1]->exit_up = true;
                        //exits for 4
                        $map->data[$item[0]-1][$item[1]-2]->exit_down = true;
                        $map->data[$item[0]-1][$item[1]-2]->exit_right = true;

                        //exits for 5
                        $map->data[$item[0]][$item[1]-2]->exit_up = true;
                        $map->data[$item[0]][$item[1]-2]->exit_right = true;


                        //remove all rooms from rooms array

                        foreach($rooms as $room)
                        {

                            $result = array_search ( $room , $locations ,false );
                            if($result!== false)
                            {
                                unset($locations[$result]);
                            }
                        }



                        $temp = array_values($locations);
                        $locations = $temp;



                        break;
                    }
                }

            }

            if(!$foundRoom)
                throw new Exception("could not place secret C4");




            $this->placeCorridor($map,10+$area,$locations);
        }
        else
        {
            $this->placeCorridor($map,$area,$locations);
            $this->placeCorridor($map,10+$area,$locations);
        }
        //place single shops


        $singleshops = $singleShopLibrary[$area];
        $multishops = $multiShopLibrary[$area];


        foreach ($singleshops as $item)
        {

            if(count($locations) > 0)
            {
                $index = array_rand($locations);

                $yPos = $locations[$index][0];
                $xPos = $locations[$index][1];
                $map->data[$yPos][$xPos]->room_type = 5;
                $map->data[$yPos][$xPos]->item_id = $item;

                unset($locations[$index]);
                $temp = array_values($locations);
                $locations = $temp;

            }
            else
            {
                $error = 'map has no valid spot to place a single_shop';
                throw new Exception($error);
            }
        }

        //place multishops
        foreach ($multishops as $item)
        {

            if(count($locations) > 0)
            {
                $index = array_rand($locations);

                $yPos = $locations[$index][0];
                $xPos = $locations[$index][1];
                $map->data[$yPos][$xPos]->room_type = 4;
                $map->data[$yPos][$xPos]->item_id = $item;

                unset($locations[$index]);
                $temp = array_values($locations);
                $locations = $temp;

            }
            else
            {
                $error = 'map has no valid spot to place a multi_shop';
                throw new Exception($error);
            }
        }



    }

    private function placeNonImportantRooms($map,$area,bool $secret)
    {
        $locations = $this->createListOfSuitableRooms($map, $area, true, true);

        //place save room
        if(count($locations) > 0) {
            $index = array_rand($locations);

            $yPos = $locations[$index][0];
            $xPos = $locations[$index][1];
            $map->data[$yPos][$xPos]->room_type = 1;

            unset($locations[$index]);
            $temp = array_values($locations);
            $locations = $temp;
        }

        //place text rooms
        if($secret)
            $textrooms = SecretLibrary::getTextBlock($area);
        else
            $textrooms = ItemLibrary::getTextBlock($area);
        foreach ($textrooms as $item)
        {

            if(count($locations) > 0)
            {
                $index = array_rand($locations);

                $yPos = $locations[$index][0];
                $xPos = $locations[$index][1];
                $map->data[$yPos][$xPos]->room_type = 3;
                $map->data[$yPos][$xPos]->item_id = $item;

                unset($locations[$index]);
                $temp = array_values($locations);
                $locations = $temp;

            }
        }

        if($area===0)//place the PChip room
        {
            $index = array_rand($locations);
            $yPos = $locations[$index][0];
            $xPos = $locations[$index][1];
            $map->data[$yPos][$xPos]->block_set = ItemLibrary::getPChipRoom();
            $map->data[$yPos][$xPos]->chip_tile = true;
        }
    }

    private function placeCardinalDirections($map)
    {
        //find all rooms on ring
        $ringRooms = array();
        foreach ($map->data as $yPos => $row) {
            foreach ($row as $xPos => $item) {
                if($item->area===0&&$item->avoid_special&&$item->room_type===0&&$item->accessible)
                {
                    array_push($ringRooms,[$yPos,$xPos]);
                }
            }
        }

        //find the farthest points
        $northY = null;
        $southY = null;
        $westX = null;
        $eastX = null;
        foreach ($ringRooms as $item) {
            $yPos = $item[0];
            $xPos = $item[1];
            //find the northern most point
            if($northY === null||$yPos < $northY)
            {
                $northY = $yPos;
            }
            //find the southern most point
            if($westX === null||$yPos > $southY)
            {
                $southY = $yPos;
            }

            //find the western most point
            if($westX === null||$xPos < $westX)
            {
                $westX = $xPos;
            }
            //find the eastern most point
            if($eastX === null||$xPos > $eastX)
            {
                $eastX = $xPos;
            }
        }

        //find the mean position along each side
        $northSum = 0;
        $northTiles = 0;
        $southSum = 0;
        $southTiles = 0;
        $westSum = 0;
        $westTiles = 0;
        $eastSum = 0;
        $eastTiles = 0;

        foreach ($ringRooms as $item) {
            $yPos = $item[0];
            $xPos = $item[1];
            if($yPos === $northY)
            {
                $northSum += $xPos;
                $northTiles++;
            }
            if($yPos === $southY)
            {
                $southSum += $xPos;
                $southTiles++;
            }
            if($xPos === $westX)
            {
                $westSum += $yPos;
                $westTiles++;
            }
            if($xPos === $eastX)
            {
                $eastSum += $yPos;
                $eastTiles++;
            }
        }
        $northAvg = $northSum / $northTiles;
        $southAvg = $southSum / $southTiles;
        $westAvg = $westSum / $westTiles;
        $eastAvg = $eastSum / $eastTiles;


        //find the closest tile to each tile
        $northRoom = null;
        $northRoomDistance = null;
        $southRoom = null;
        $southRoomDistance = null;
        $westRoom = null;
        $westRoomDistance = null;
        $eastRoom = null;
        $eastRoomDistance = null;

        foreach ($ringRooms as $item) {
            $yPos = $item[0];
            $xPos = $item[1];
            if($yPos == $northY)
            {
                $distanceAway = abs($northAvg - $xPos);
                if($northRoomDistance === null ||  $distanceAway < $northRoomDistance)
                {
                    $northRoom = $item;
                    $northRoomDistance = $distanceAway;
                }
                else if (abs($northAvg - $xPos) === $southRoomDistance)
                {
                    if(rand(0,1) === 1)
                    {
                        $northRoom = $item;
                        $northRoomDistance = $distanceAway;
                    }
                }
            }

            if($yPos == $southY)
            {
                $distanceAway = abs($southAvg - $xPos);
                if($southRoomDistance === null ||  $distanceAway < $southRoomDistance)
                {
                    $southRoom = $item;
                    $southRoomDistance = $distanceAway;
                }
                else if (abs($southAvg - $xPos) === $southRoomDistance)
                {
                    if(rand(0,1) === 1)
                    {
                        $southRoom = $item;
                        $southRoomDistance = $distanceAway;
                    }
                }
            }


            if($xPos == $westX)
            {
                $distanceAway = abs($westAvg - $yPos);
                if($westRoomDistance === null ||  $distanceAway < $westRoomDistance)
                {
                    $westRoom = $item;
                    $westRoomDistance = $distanceAway;
                }
                else if (abs($westAvg - $yPos) === $westRoomDistance)
                {
                    if(rand(0,1) === 1)
                    {
                        $westRoom = $item;
                        $westRoomDistance = $distanceAway;
                    }
                }
            }
            if($xPos == $eastX)
            {
                $distanceAway = abs($eastAvg - $yPos);
                if($eastRoomDistance === null ||  $distanceAway < $eastRoomDistance)
                {
                    $eastRoom = $item;
                    $eastRoomDistance = $distanceAway;
                }
                else if (abs($eastAvg - $yPos) === $eastRoomDistance)
                {
                    if(rand(0,1) === 1)
                    {
                        $eastRoom = $item;
                        $eastRoomDistance = $distanceAway;
                    }
                }
            }

        }

        $map->data[$northRoom[0]][$northRoom[1]]->block_set = ItemLibrary::getCardinalLetter("N");
        $map->data[$southRoom[0]][$southRoom[1]]->block_set = ItemLibrary::getCardinalLetter("S");
        $map->data[$eastRoom[0]][$eastRoom[1]]->block_set = ItemLibrary::getCardinalLetter("E");
        $map->data[$westRoom[0]][$westRoom[1]]->block_set = ItemLibrary::getCardinalLetter("W");




    }
    private function placeCorridor($map,$corridor_id,&$locations){
        if(count($locations) > 0) {
            $index = array_rand($locations);

            $yPos = $locations[$index][0];
            $xPos = $locations[$index][1];
            $map->data[$yPos][$xPos]->room_type = 2;
            $map->data[$yPos][$xPos]->enemy_type = $corridor_id;

            unset($locations[$index]);
            $temp = array_values($locations);
            $locations = $temp;
        }
        else
        {
            $error = 'map has no valid spot to place a corridor';
            throw new Exception($error);
        }
    }
    private function placeStartingPointRooms($map)
    {
        foreach ($map->data as $yPos => $row) {
            foreach ($row as $xPos => $item) {
                if($item->area>= 1 && $item->area<= 10 && $item->startingpoint)
                {
                    if($item->area ===1)
                    {
                        $item->room_type = 2;
                        $item->enemy_type = 1;
                    }
                    else
                    {
                        $item->room_type = 1;
                    }
                }
            }
        }
    }


    //empty room odds is, if >= 1, 1 out of $empty_room_odds will be empty, if zero no rooms will be empty
    private function populateEnemies(Map &$map, $empty_room_odds)
    {
        foreach ($map->data as $yPos => &$row) {
            foreach ($row as $xPos => &$item) {
                if($item->accessible && ($item->room_type === 0 || $item->room_type === 7))
                {
                    if($empty_room_odds>0)
                    {
                        if(rand(1,$empty_room_odds) !=1)
                        {
                            $item->enemy_type = rand(1,47);
                        }
                    }
                    else
                    {
                        $item->enemy_type = rand(1,47);
                    }
                }
            }
        }
    }


    private function placeAreaDecorations(Map &$map)
    {
        $startingRooms = array();
        foreach ($map->data as $yPos => $row) {
            foreach ($row as $xPos => $item) {
                if($item->startingpoint && $item->area !== 0)
                {
                    array_push($startingRooms,[$yPos,$xPos]);
                }
            }
        }

        foreach($startingRooms as $room)
        {
            $yPos = $room[0];
            $xPos = $room[1];
            if($map->data[$yPos][$xPos]->exit_up)
            {
                if($map->data[$yPos-1][$xPos]->room_type === 0 && $map->data[$yPos-1][$xPos]->block_set === null)
                {
                    $chips = rand(0,1); //do i want chips;
                    if($chips === 0) //no
                    {
                        $map->data[$yPos-1][$xPos]->block_set = ItemLibrary::getRandomRoomBlock(false,1,2);
                        $map->data[$yPos-1][$xPos]->chip_tile = false;
                    }
                    else
                    {
                        $map->data[$yPos-1][$xPos]->block_set = ItemLibrary::getRandomRoomBlock(true,1,2);
                        $map->data[$yPos-1][$xPos]->chip_tile = true;
                    }
                }
            }

            if($map->data[$yPos][$xPos]->exit_down)
            {
                if($map->data[$yPos+1][$xPos]->room_type === 0 && $map->data[$yPos+1][$xPos]->block_set === null)
                {
                    $chips = rand(0,1); //do i want chips;
                    if($chips === 0) //no
                    {
                        $map->data[$yPos+1][$xPos]->block_set = ItemLibrary::getRandomRoomBlock(false,1,1);
                        $map->data[$yPos+1][$xPos]->chip_tile = false;
                    }
                    else
                    {
                        $map->data[$yPos+1][$xPos]->block_set = ItemLibrary::getRandomRoomBlock(true,1,1);
                        $map->data[$yPos+1][$xPos]->chip_tile = true;
                    }
                }
            }

            if($map->data[$yPos][$xPos]->exit_left)
            {
                if($map->data[$yPos][$xPos-1]->room_type === 0 && $map->data[$yPos][$xPos-1]->block_set === null)
                {
                    $chips = rand(0,1); //do i want chips;
                    if($chips === 0) //no
                    {
                        $map->data[$yPos][$xPos-1]->block_set = ItemLibrary::getRandomRoomBlock(false,1,4);
                        $map->data[$yPos][$xPos-1]->chip_tile = false;
                    }
                    else
                    {
                        $map->data[$yPos][$xPos-1]->block_set = ItemLibrary::getRandomRoomBlock(true,1,4);
                        $map->data[$yPos][$xPos-1]->chip_tile = true;
                    }
                }
            }
            if($map->data[$yPos][$xPos]->exit_right)
            {
                if($map->data[$yPos][$xPos+1]->room_type === 0 && $map->data[$yPos][$xPos+1]->block_set === null)
                {
                    $chips = rand(0,1); //do i want chips;
                    if($chips === 0) //no
                    {
                        $map->data[$yPos][$xPos+1]->block_set = ItemLibrary::getRandomRoomBlock(false,1,3);
                        $map->data[$yPos][$xPos+1]->chip_tile = false;
                    }
                    else
                    {
                        $map->data[$yPos][$xPos+1]->block_set = ItemLibrary::getRandomRoomBlock(true,1,3);
                        $map->data[$yPos][$xPos+1]->chip_tile = true;
                    }
                }
            }
        }

    }


    private function placeCorridorDecorations($map)
    {
        $corridor = array();
        foreach ($map->data as $yPos => $row) {
            foreach ($row as $xPos => $item) {
                if($item->room_type === 2)
                {
                    array_push($corridor,[$yPos,$xPos]);
                }
            }
        }

        foreach($corridor as $room)
        {
            $yPos = $room[0];
            $xPos = $room[1];
            if($map->data[$yPos][$xPos]->exit_up)
            {
                if($map->data[$yPos-1][$xPos]->room_type === 0 && $map->data[$yPos-1][$xPos]->block_set === null)
                {

                        $map->data[$yPos-1][$xPos]->block_set = ItemLibrary::getRandomRoomBlock(false,2,2);
                        $map->data[$yPos-1][$xPos]->chip_tile = false;
                }
            }

            if($map->data[$yPos][$xPos]->exit_down)
            {
                if($map->data[$yPos+1][$xPos]->room_type === 0 && $map->data[$yPos+1][$xPos]->block_set === null)
                {

                        $map->data[$yPos+1][$xPos]->block_set = ItemLibrary::getRandomRoomBlock(false,2,1);
                        $map->data[$yPos+1][$xPos]->chip_tile = false;

                }
            }

            if($map->data[$yPos][$xPos]->exit_left)
            {
                if($map->data[$yPos][$xPos-1]->room_type === 0 && $map->data[$yPos][$xPos-1]->block_set === null)
                {

                        $map->data[$yPos][$xPos-1]->block_set = ItemLibrary::getRandomRoomBlock(false,2,4);
                        $map->data[$yPos][$xPos-1]->chip_tile = false;

                }
            }
            if($map->data[$yPos][$xPos]->exit_right)
            {
                if($map->data[$yPos][$xPos+1]->room_type === 0 && $map->data[$yPos][$xPos+1]->block_set === null)
                {
                        $map->data[$yPos][$xPos+1]->block_set = ItemLibrary::getRandomRoomBlock(false,2,3);
                        $map->data[$yPos][$xPos+1]->chip_tile = false;
                }
            }
        }

    }

    // decoration odds if 0 there are no decorations, if > 1, 1 out of x rooms has decorations
    // chip odds if 0 no decorations will be chip decorations,, if > 1, 1 out of decorations is a chip decoration

    private function placeRandomDecorations($map, $decoration_odds, $chip_odds)
    {
        foreach ($map->data as $yPos => &$row) {
            foreach ($row as $xPos => &$item) {
                if($item->accessible && ($item->room_type === 0) && $item->block_set === NULL)
                {
                    $decorate = false;
                    $usechips = false;
                    if($decoration_odds>0)
                    {
                        if(rand(1,$decoration_odds) ===1)
                        {
                            $decorate = true;
                        }
                    }
                    else
                    {
                        $decorate = true;
                    }
                    if($decorate)
                    {
                        if($chip_odds>0)
                        {
                            if(rand(1,$chip_odds) ===1)
                            {
                                $usechips = true;
                            }
                        }
                        else
                        {
                            $usechips = true;
                        }

                        if($usechips)
                        {


                            $item->block_set = ItemLibrary::getRandomRoomBlock(true,0,0);
                            $item->chip_tile = true;
                        }
                        else
                        {
                            $item->block_set = ItemLibrary::getRandomRoomBlock(false,0,0);
                            $item->chip_tile = false;

                        }
                    }
                }
            }
        }
    }




    private function createListOfSuitableRooms(Map $map, int $area, bool $discard_special, bool $allow_overwrite_entry)
    {
        $suitable_rooms = array();

        foreach ($map->data as $yPos => $row) {
            foreach ($row as $xPos => $item) {
                if($item->area === $area && $item->accessible && $item->block_set === NULL && (($item->room_type === 0)||($area!==0&&$allow_overwrite_entry&&$item->room_type===1&&$item->startingpoint)))
                {
                    if(!$discard_special||!$item->avoid_special)
                    {
                        if($area!==0 || !$item->startingpoint) //in area 0 don't make any of the starting points miniboss rooms
                            array_push($suitable_rooms,[$yPos,$xPos]);
                    }
                }
            }
        }
        return $suitable_rooms;
    }

    private function addConnections(Map $map, int $zone, int $desiredConnections, bool $oneWay, bool $portalOnly)
    {
        //find all the points in the area
        $rooms_in_zone = array();
        foreach ($map->data as $yPos => &$row) {
            foreach($row as $xPos => &$item) {
                if($map->data[$yPos][$xPos]->area === $zone && $map->data[$yPos][$xPos]->accessible === true)
                {
                    array_push($rooms_in_zone,[$yPos,$xPos]);
                }
            }
        }

        while($desiredConnections>=1&&count($rooms_in_zone)>1)
        {
            //pick a point at random
            $index = array_rand($rooms_in_zone);
            $roomToEdit = $rooms_in_zone[$index];
            //pick a direction at random
            $desiredDirection = rand(0,3);

            $yPos = $roomToEdit[0];
            $xPos = $roomToEdit[1];

            $up = 0;
            $left = 1;
            $right = 2;
            $down = 3;

            //check room to see if any no directions can be added

            $canGoUp = $yPos>0&&$map->data[$yPos-1][$xPos]->area===$zone&&($map->data[$yPos-1][$xPos]->accessible)&&!$map->data[$yPos][$xPos]->exit_up;
            $canGoLeft = $xPos>0&&$map->data[$yPos][$xPos-1]->area===$zone&&($map->data[$yPos][$xPos-1]->accessible)&&!$map->data[$yPos][$xPos]->exit_left;
            $canGoRight = $xPos<23&&$map->data[$yPos][$xPos+1]->area===$zone&&($map->data[$yPos][$xPos+1]->accessible)&&!$map->data[$yPos][$xPos]->exit_right;
            $canGoDown = $yPos<23&&$map->data[$yPos+1][$xPos]->area===$zone&&($map->data[$yPos+1][$xPos]->accessible)&&!$map->data[$yPos][$xPos]->exit_down;
            if($portalOnly)
            {
                $thisRoomType = $map->data[$yPos][$xPos]->room_type;
                $goodRooms = [1,2,3,4,5];//i'll do good rooms only because it's possible i could add a room type

                if($canGoUp)
                {
                    $otherRoomType = $map->data[$yPos-1][$xPos]->room_type;
                    $canGoUp = in_array($thisRoomType,$goodRooms)||in_array($otherRoomType,$goodRooms);
                }
                if($canGoLeft)
                {
                    $otherRoomType = $map->data[$yPos][$xPos-1]->room_type;
                    $canGoLeft = in_array($thisRoomType,$goodRooms)||in_array($otherRoomType,$goodRooms);
                }
                if($canGoRight)
                {
                    $otherRoomType = $map->data[$yPos][$xPos+1]->room_type;
                    $canGoRight = in_array($thisRoomType,$goodRooms)||in_array($otherRoomType,$goodRooms);
                }
                if($canGoDown)
                {
                    $otherRoomType = $map->data[$yPos+1][$xPos]->room_type;
                    $canGoRight = in_array($thisRoomType,$goodRooms)||in_array($otherRoomType,$goodRooms);
                }

            }

            if(!$canGoUp&&!$canGoLeft&&!$canGoRight&&!$canGoDown)
            {
                unset($rooms_in_zone[$index]);
                //$temp = array_values($rooms_in_zone);
                //$rooms_in_zone = $temp;
            }


            switch($desiredDirection)
            {
                case $up:
                    if($canGoUp)
                    {
                        //we can grow here oh boy
                        $map->data[$yPos][$xPos]->exit_up=true;
                        if(!$oneWay)
                            $map->data[$yPos-1][$xPos]->exit_down=true;
                        $desiredConnections --;
                    }
                    break;
                case $left:
                    if($canGoLeft)
                    {
                        //we can grow here oh boy
                        $map->data[$yPos][$xPos]->exit_left=true;
                        if(!$oneWay)
                            $map->data[$yPos][$xPos-1]->exit_right=true;
                        $desiredConnections --;
                    }
                    break;
                case $right:
                    if($canGoRight)
                    {
                        //we can grow here oh boy
                        $map->data[$yPos][$xPos]->exit_right=true;
                        if(!$oneWay)
                            $map->data[$yPos][$xPos+1]->exit_left=true;
                        $desiredConnections --;
                    }
                    break;
                case $down:
                    if($canGoDown)
                    {
                        //we can grow here oh boy
                        $map->data[$yPos][$xPos]->exit_down=true;
                        if(!$oneWay)
                            $map->data[$yPos+1][$xPos]->exit_up=true;
                        $desiredConnections --;
                    }
                    break;
            }

            //if i was able to set it de-increment desired connections
        }
    }



}