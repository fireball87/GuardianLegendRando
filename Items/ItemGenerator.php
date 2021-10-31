<?php


namespace TGL\MapGen\Items;
use Exception;
use TGL\MapGen\Helpers;
use TGL\MapGen\Patcher;


abstract class Item
{
    const MultiBullet = 0;
    const BackWave = 1;
    const WaveBeam = 2;
    const BulletShield = 3;
    const Grenade = 4;
    const Fireball = 5;
    const AreaBlaster = 6;
    const Repeller = 7;
    const HyperLaser = 8;
    const SaberLaser = 9;
    const CutterSaber = 10;
    const EnemyEraser = 11;
    const EnemyTank = 12;
    const BlueLander = 13;
    const Gun = 14;
    const Shield = 15;
    const RapidFire = 16;
    const RedLander = 17;
    //const BlueChip
}
class ItemGenerator
{

//fill corridors
//fill minibosses
//place and fill shops
//throw the rest of the items into place

    public static function prepareItems(Patcher $patcher, int $multi_shops, int $single_shops , int $weapon_size,int $blue,int $red,int $shield,int $guns,int $rapid_fires,int $etanks,int $enemyerasers, bool $log)
    {
        $single_shop_library = array_fill(0, 11, []);
        $multi_shop_library = array_fill(0, 11, []);
        $item_library = array_fill(0, 11, []);

        $itemPool = self::createItemPool($weapon_size,$blue,$red,$shield,$guns,$rapid_fires, $etanks, $enemyerasers);

        //corridors use items from 0-19
        $patchstring = "";
        if($log)
            echo "\n";
        for($i=0;$i<=19;$i++)
        {
            if($log)
                echo "corridor ".$i." has ". $itemPool[$i]."\n";
            $patchstring.=Helpers::inthex($itemPool[$i]);
        }
        $patcher->addChange($patchstring,"1EF51");

        //minibosses use items from 20-39
        $patchstring = "";
        $itemstring = "";
        $poolSize = count($itemPool);

        for($i=20;$i<=$poolSize-($single_shops+$multi_shops+1);$i++)
        {
            $itemstring.=Helpers::inthex($itemPool[$i]);

            if($i>=30&&$i<52)
            {
                if($log)
                    echo "miniboss ". dechex($i-19) ." has ". $itemPool[$i]."\n";

            }
            else
            {
                array_push($item_library[rand(0,10)],Helpers::inthex($i-19));
                if($log)
                    echo "item box ". dechex($i-19) ." has ". $itemPool[$i]."\n";
                if($i-19>57)
                {
                    throw new Exception("Tried to place more item boxes then the game had");
                }
            }
        }


        $patchstring = $itemstring;
        $patcher->addChange($patchstring,"16388");


        //shops use from the last number of shops away from the last shop
        $patchstring = "";
        for($i=$poolSize-($single_shops+$multi_shops+1)+1;$i<=$poolSize-($multi_shops+1);$i++) {

            $id = $i-($poolSize-($single_shops+$multi_shops+1))+57;
            $price = ItemGenerator::randomPriceForArea($i-($poolSize-($single_shops+$multi_shops+1)+1));

            $pricehex = str_pad(dechex($price),4,"0",STR_PAD_LEFT);
            $flippedPrice = substr($pricehex,2,2) . substr($pricehex,0,2);

            $patchstring.=$flippedPrice.Helpers::inthex($itemPool[$i]);
            array_push($single_shop_library[0],Helpers::inthex($id));
            if($log)
                echo "small shop ". dechex($id) ." has ". $itemPool[$i]."\n";
        }
        $patcher->addChange($patchstring,"16077");//1601


        $patchstring = "";
        for($i=$poolSize-($multi_shops+1)+1;$i<=$poolSize-1;$i++) {

            $id = $i-($poolSize-($multi_shops+1))+62;
            $desiredArea = rand(1,10);
            $price = ItemGenerator::randomPriceForArea($desiredArea);

            $pricehex = str_pad(dechex($price),4,"0",STR_PAD_LEFT);
            $flippedPrice = substr($pricehex,2,2) . substr($pricehex,0,2);

            $die = rand(0,5);
            if($die >= 0 && $die <= 2)
                $randItem0 = 12;
            else
                $randItem0 = 11;


            $patchstring.=$flippedPrice.Helpers::inthex($itemPool[$i]).Helpers::inthex($randItem0).Helpers::inthex(rand(0,10));
            array_push($multi_shop_library[$desiredArea],Helpers::inthex($id));

            if($log)
                echo "big shop ". dechex($id) ." has ". $itemPool[$i]. " in area ".$desiredArea."\n";
        }


        $patcher->addChange($patchstring,"1605e");

        return [$item_library,$single_shop_library,$multi_shop_library];

    }



    private static function randomPriceForArea(int $area)
    {
        switch($area)
        {
            case 0:
                return(rand(0,50));
            case 1:
                return(rand(50,100));
            case 2:
                return(rand(100,150));
            case 3:
                return(rand(150,300));
            case 4:
                return(rand(300,450));
            case 5:
                return(rand(450,600));
            case 6:
                return(rand(600,750));
            case 7:
                return(rand(750,1000));
            case 8:
                return(rand(1000,1600));
            case 9:
                return(rand(1600,2400));
            case 10:
                return(rand(2400,4000));

        }
    }


    



    private static function createItemPool($weapon_size,$blue,$red,$shield,$guns,$rapid_fires, $etanks, $enemyerasers)
    {
        $pool = [];
        while($weapon_size > 0)
        {
            for($i = 0; $i<=10; $i++)
                array_push($pool,$i);
            $weapon_size --;
        }
        while($blue > 0)
        {
            array_push($pool,Item::BlueLander);
            $blue --;
        }
        while($red > 0)
        {
            array_push($pool,Item::RedLander);
            $red --;
        }

        while($shield > 0)
        {
            array_push($pool,Item::Shield);
            $shield --;
        }

        while($guns > 0)
        {
            array_push($pool,Item::Gun);
            $guns --;
        }
        while($rapid_fires > 0)
        {
            array_push($pool,Item::RapidFire);
            $rapid_fires --;
        }

        while($etanks > 0)
        {
            array_push($pool,Item::EnemyTank);
            $etanks --;
        }

        while($enemyerasers > 0)
        {
            array_push($pool,Item::EnemyEraser);
            $enemyerasers --;
        }

        if (count($pool)<50)
            throw new Exception("Not enough items to fill shops.");
        if (count($pool)>57+30+10)
            throw new Exception("Too many items to place.");
         shuffle($pool);
         return $pool;

    }



}