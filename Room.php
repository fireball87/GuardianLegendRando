<?php


namespace TGL\MapGen;

abstract class RoomType
{
    const Normal = 0;
    const Save = 1;
    const Corridor = 2;
    const Text = 3;
    const MultiShop = 4;
    const SingleShop = 5;
    const Miniboss = 6;
    const Item = 7;
}


class Room
{
    public $area = NULL; //number between 0 and 10, or null if undefined, may also be a negative number if it's dead space, -1 is places in the template doors can form, -2 is places doors are not allowed to form, but still wall
    public $accessible = false;

    public $startingpoint = false;//starting point is the connection to area 0
    //room exits
    public $exit_up = false;
    public $exit_down = false;
    public $exit_left = false;
    public $exit_right = false;

    public $avoid_special = false; //there'll be special code to not add stuff to the ring


    public $room_type = RoomType::Normal; //0 normal, 1 save, 2 corridor, 3 text, 4 multi_shop, 5 single_shop, 6 miniboss, 7 item drop
    public $block_set = NULL; //shared between both item rooms and normal rooms
    public $chip_tile = false; //if this is set the blocks are chip blocks, null for nothing, 0 for blue chips, if that's even how things work as i don't currently encode it into blocks yet
    public $item_id = NULL; //can also be the miniboss id, as those are completely tied, also reusing this for the shop and text id

    public $enemy_type = 0; //0 is the code i'll use for empty, from 1-47 are valid enemy values, documented in map in one of the functions
    //also use enemy type for corridor because why not

    //contained items bosses and shit will be added later


    public function countBytes()
    {
        if(!$this->accessible)
            return 1;
        if($this->room_type === RoomType::Save || $this->room_type === RoomType::Corridor)
        {
            return 3;
        }
        if($this->room_type === RoomType::Text || $this->room_type === RoomType::MultiShop || $this->room_type === RoomType::SingleShop || $this->room_type === RoomType::Miniboss)
        {
            return 4;
        }
        $value = 3; //an empty room is 3 bytes
        if($this->room_type === RoomType::Item)
        {
            $value ++; // one byte for the item itself
        }
        if($this->block_set !== null)
        {
            $value += 2; //blocks are 2 byte pointers
        }
        if($this->enemy_type!==0)
        {
            $value += 1; //enemies take 1 byte to represent
        }
        return $value;
    }

}