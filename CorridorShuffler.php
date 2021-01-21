<?php


namespace TGL\MapGen;



class CorridorShuffler
{
    public static function shuffleCorridors(Patcher $patcher)
    {
        $table = [ //corridor (and therefore id), bossid, corridor pointer
            [1,"40","509C","21"],
            [2,"45","7F9D","21"],
            [3,"2E","5EA2","22"],
            [4,"46","0CA4","22"],
            [5,"23","8EA9","23"],
            [6,"47","11AB","23"],
            [7,"06","11B0","24"],
            [8,"49","96B1","24"],
            [9,"18","BFB6","26"],
            [10,"19","C1B8","26"],
            [11,"2D","2B9F","21"],
            [12,"41","8EA0","21"],
            [13,"4C","F7A5","22"],
            [14,"25","B1A7","22"],
            [15,"24","90AC","23"],
            [16,"2F","29AE","23"],
            [17,"06","46B3","24"],
            [18,"48","E0B4","24"],
            [19,"4D","FFBA","25"],
            [20,"26","32BD","25"]
        ];

        shuffle ($table);
        $bosses = "";
        $pointers = "";
        $graphics = "";
        echo "\n";
        foreach($table as $row)
        {
            echo $row[0].",";
            $bosses.=$row[1];
            $pointers.=$row[2];
            $graphics.=$row[3];
        }
        echo "\n";



        $patcher->addChange($bosses,"d162");
        $patcher->addChange($pointers,"10029");
        $patcher->addChange($graphics,"1ef66");



        //change the boss tied to the corridor
        //boss table is d161 and starts at 0, we're not shifting 0 for the moment
        //boss id's for area //40,45,2E,46,23,47,06,49,18,19,2D,41,4C,25,24,2F,06,48,4D,26,4D,4F

        //change the corridor label itself
        //area table starts at 10027
        //area id's
        //10027    45 80    0
        //10029    50 9C    1
        //1002B    7F 9D    2
        //1002D    5E A2    3
        //1002F    0C A4    4
        //10031    8E A9    5
        //10033    11 AB    6
        //10035    11 B0    7
        //10037    96 B1    8
        //10039    BF B6    9
        //1003B    C1 B8    10
        //1003D    2B 9F    11
        //1003F    8E A0    12
        //10041    F7 A5    13
        //10043    B1 A7    14
        //10045    90 AC    15
        //10047    29 AE    16
        //10049    46 B3    17
        //1004B    E0 B4    18
        //1004D    FF BA    19
        //1004F    32 BD    20

        //need to do 3 things
        //change the graphics tied to the corridor
        //1ef65 is the start of the graphics id's
        //21,21,22,22,23,23,24,24,26,26,21,21,22,22,23,23,24,24,25,25
    }


    public static function randomizeMinibosses(Patcher $patcher, $allowMissingno)
    {
        //22 minibosses
        //3 * a number between 0 and 11, 12 if i allow missingno
        if($allowMissingno)
            $monstervalues = 12;
        else
            $monstervalues = 11;

        $datum = "";
        for ($i = 0; $i < 22; $i++) {
            $datum .= Helpers::inthex(rand(0,$monstervalues) * 3);
        }

        $patcher->addChange($datum,"1669D");
    }




}