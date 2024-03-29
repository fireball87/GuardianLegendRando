<?php


namespace TGL\MapGen;



class CorridorShuffler
{


    public static function randomizeBosses(Patcher $patcher, $randomizeFinalBoss) : array
    {
        $table = [ //corridor (and therefore id), bossid, corridor pointer
            ["boss"=>"eyegore","patch"=>["003780013A800242DA834FF6","00378001028E0242DA834FF6"],"pointer"=>["C2BF","CEBF"],"ids"=>["18","19"]],
            ["boss"=>"fleepa","patch"=>["0037800139888243B2"],"pointer"=>["B8B8"],"ids"=>["40","41"]],
            ["boss"=>"optomon","patch"=>["0037800140C68253F6"],"pointer"=>["5DA0"],"ids"=>["2d","2e","2f"]],
            ["boss"=>"crawdaddy","patch"=>["0037808149BA"],"pointer"=>["FD9E"],"ids"=>["45"]],
            ["boss"=>"bombarderclawbot","patch"=>["003780013588022AD88351F6"],"pointer"=>["F2BC"], "ids"=>["4c","4d","24","25","26"]],
            ["boss"=>"teramute","patch"=>["0037800144B08203F2"],"pointer"=>["DBA3"],"ids"=>["46"]],
            ["boss"=>"glider","patch"=>["0037800134C68235E6"],"pointer"=>["20AE"],"ids"=>["47"]],
            ["boss"=>"zibzub","patch"=>["0037800135888241D2"],"pointer"=>["7CA7"],"ids"=>["23"]],
            ["boss"=>"grimgrin","patch"=>["00378001049A821CA2"],"pointer"=>["8AB6"],"ids"=>["48","49"]],
        ];

        if($randomizeFinalBoss)
        {
            $table[] = ["boss"=>"it","patch"=>["00378001368802459C8318D0"],"pointer"=>["0585"],"ids"=>["4f"]];
        }

        foreach($table as $boss)
        {
            foreach($boss["patch"] as $key => $patch) {
                $bankstart = hexdec("8010");
                $flipped_pointer = substr($boss["pointer"][$key], 2, 2) . substr($boss["pointer"][$key], 0, 2);
                $offset = dechex($bankstart + hexdec($flipped_pointer));
                $patcher->addChange($patch, $offset);
            }
        }

        $finalBoss = null;
        //pick the final boss
        if($randomizeFinalBoss)
        {
            $boss_array_id = array_rand($table);
            $id_array_id = array_rand($table[$boss_array_id]["ids"]);
            $id = $table[$boss_array_id]["ids"][$id_array_id];
            if(count($table[$boss_array_id]["pointer"]) > 1) {
                $pointer = $table[$boss_array_id]["pointer"][$id_array_id];
                unset($table[$boss_array_id]["pointer"][$id_array_id]);
            }
            else {
                $pointer = $table[$boss_array_id]["pointer"][0];
            }
            unset($table[$boss_array_id]["ids"][$id_array_id]);


            if(count($table[$boss_array_id]["ids"])==0)
            {
                unset($table[$boss_array_id]);
            }
            $finalBoss = array("id"=>$id,"pointer"=>$pointer);
        }



        //make the c21 boss list
        //need to pick 6 bosses
        shuffle($table);
        $c21bosses = array();
        for($x=0;$x<6;$x++)
        {
            $key = array_rand($table[$x]["ids"]);
            if(count($table[$x]["pointer"]) > 1)
                $pointer = $table[$x]["pointer"][$key];
            else
                $pointer = $table[$x]["pointer"][0];
            $id = $table[$x]["ids"][$key];
            $c21bosses[]=array("id"=>$id,"pointer"=>$pointer);
        }

        //now need to make the array for every enemy
        $levelBosses = array();
        foreach($table as $boss)
        {
            foreach($boss["ids"] as $key => $id)
            {
                if(count($boss["pointer"]) > 1)
                    $levelBosses[]=array("id"=>$id,"pointer"=>$boss["pointer"][$key]);
                else
                    $levelBosses[]=array("id"=>$id,"pointer"=>$boss["pointer"][0]);
            }
        }
        shuffle($levelBosses);
        $levelBosses = array_merge(array_slice($levelBosses, 0, 6),
            array(null),
            array_slice($levelBosses, 6, count($levelBosses) - 1));
        $levelBosses = array_merge(array_slice($levelBosses, 0, 16),
            array(null),
            array_slice($levelBosses, 16, count($levelBosses) - 1));



        return array("level"=>$levelBosses,"c21"=>$c21bosses,"final"=>$finalBoss);
    }

    public static function shuffleCorridors(Patcher $patcher, bool $shuffleCorridors, ?array $shuffledBosses, bool $log)
    {
        $table = [ //corridor (and therefore id), bossid, corridor pointer, graphicstable
            [1, "40", "509C", "21"],
            [2, "45", "7F9D", "21"],
            [3, "2E", "5EA2", "22"],
            [4, "46", "0CA4", "22"],
            [5, "23", "8EA9", "23"],
            [6, "47", "11AB", "23"],
            [7, "06", "11B0", "24"],
            [8, "49", "96B1", "24"],
            [9, "18", "BFB6", "26"],
            [10, "19", "C1B8", "26"],
            [11, "2D", "2B9F", "21"],
            [12, "41", "8EA0", "21"],
            [13, "4C", "F7A5", "22"],
            [14, "25", "B1A7", "22"],
            [15, "24", "90AC", "23"],
            [16, "2F", "29AE", "23"],
            [17, "06", "46B3", "24"],
            [18, "48", "E0B4", "24"],
            [19, "4D", "FFBA", "25"],
            [20, "26", "32BD", "25"]
        ];

        //refresh the list with the shuffled bosses
        if(!is_null($shuffledBosses))
        {
            for($x = 0;$x<20;$x++)
            {
                $entry = $shuffledBosses["level"][$x];
                if(!is_null($entry))
                    $table[$x][1]=$entry["id"];
            }


        }

        if($shuffleCorridors)
            shuffle($table);
        $bosses = "";
        $pointers = "";
        $graphics = "";
        if ($log)
            echo "\n";
        foreach($table as $row)
        {
            if ($log)
                echo $row[0].",";
            $bosses.=$row[1];
            $pointers.=$row[2];
            $graphics.=$row[3];
        }

        if(!is_null($shuffledBosses))
        {
            //copying c21 final to the boss array isn't required, the next patch is
            $c21final = $shuffledBosses["c21"][array_key_last($shuffledBosses["c21"])]["id"];
            $bosses.=$c21final.$shuffledBosses["final"]["id"];

            $patcher->addChange($c21final,"d3cb");
            $patcher->addChange($c21final,"d3ac");


        }
        if ($log)
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


    public static function tokenizeCorridor($input,$corridor_number, ?array $bosses) : Array
    {
        $bossesplaced = 0;

        $last_c6 = null;
        $last_c7 = null;
        $returnArray = array();
        while(strlen($input) > 0) {
            //take 4 digits as time
            $time =  substr($input,2,2).substr($input,0,2);
            //take 2 digits as command
            $command =  substr($input,4,2);
            $data = null;
            //take ?? digits as data
            $digits = 4;
            switch ($command)
            {
                case "00": //set scroll speed
                case "01": //does something with background
                case "04": //sets the palette
                case "07": //loads monster graphics
                    $digits=4;
                    break;
                case "05": //start the boss //this seems to be digits 2 or 0 based on some internal variable, likely 0 except for final corridor where it is 2, actually, as we're comparing equality to 21 this is almost certainly true
                    if($corridor_number == 21)
                        $digits=2;
                    else
                        $digits=0;
                    break;
                case "02": //donno
                    $firstbit = hexdec(substr($input,6,2));
                    $value = $firstbit & 2;
                    if($value == 2)
                        $digits=6;
                    else
                        $digits=2;
                    break;
                case "06": //this is our enemy summoning logic
                    $digits=-1;
                    CorridorShuffler::parseCommand6($input, $data);
                    //$digits=hexdec(substr($input,6,2))*2-2;
                    break;
                case "08": //unknown
                    $digits=8; //////this digit count seems probably wrong
                    break;
                case "09":
                    $digits=4;
                    break;
                case "03"://command 3 seems to modify the BC pointer, which would affect future commands, if it is ever run we need to deal with it, it wasn't ran in zanac, pray it doesn't get used here either
                    //$error = 'Corridor used command 3, which does voodoo.';
                    //throw new Exception($error);
                    $digits=4;
                    break;
                default:
                    $error = 'Tried to tokenize an unknown command.';
                    throw new Exception($error);
            }

            if($digits>=0) {
                $data = substr($input, 6, $digits);
                $input = substr($input, 6 + $digits);
            }



            // detach messy
            unset($entry);

            $entry = array( "time"=>$time, "command"=>$command, "data"=>$data);

            /*
            if($command == "04")
            {
                echo "C".$corridor_number."- command 4 - ".$data."\n";

            }
            if($command == "07")
            {
                echo "C".$corridor_number."- command 7 - ".$data."\n";

            }
            if($command == "01")
            {
                echo "C".$corridor_number."- command 1 - ".$data."\n";

            }
            if($command == "02")
            {
                echo "C".$corridor_number."- command 2 - ".$data."\n";

            }*/


            if($command == "07")
            {
                $last_c7 = &$entry;
            }
            if($command == "05")
            {
                if(!is_null($bosses))
                {
                    if($corridor_number==21)
                    {
                       $last_c7["data"]=$bosses["c21"][$bossesplaced]["pointer"];
                       $entry["data"] = $bosses["c21"][$bossesplaced]["id"];
                    }
                    else if($corridor_number==22)
                    {
                        $last_c7["data"]=$bosses["final"]["pointer"];
                    }
                    else if($corridor_number > 0 && $corridor_number < 21)
                    {
                        $level_data = $bosses["level"][$corridor_number-1];
                        if(!is_null($level_data)) {
                            $last_c7["data"] = $level_data["pointer"];
                        }


                    }
                    $bossesplaced++;

                }

            }
            $bossHit = false;
            if($command == "06"||$command =="05"||$command =="01")
            {
                if($bossHit == true)
                {
                    if($command=="06")
                        $entry["static"]=true;
                }
                else if(!is_null($last_c6)&&!array_key_exists("time_to_next_important_command",$last_c6)) {
                    if($command == "06")
                        $last_c6["time_to_next_important_command"] = hexdec($time) - hexdec($last_c6["time"]);
                    if($command =="05"||$command =="01")
                    {
                        if($command =="05")
                            $bossHit=true;
                        $last_c6["static"]=true;
                        unset($last_c6);
                        $last_c6 = null;
                    }
                }
                else
                {
                    if($command=="06"&&is_null($last_c6))
                        $entry["static"]=true;
                }

            }


            if($command == "06")
            {
                $last_c6 = &$entry;
            }

            //lock c0 cannons
            if($corridor_number == 0 && $entry["time"]=="074E")
            {
                $entry["static"]=true;
            }

            $returnArray[] = &$entry;


        }




        return $returnArray;

    }


    public static function parseCommand6(&$input, &$data)
    {
        $entries = hexdec(substr($input,6,2));
        $input = substr($input, 6 + 2);
        $enemyArray = array();
        for($x=1;$x<=$entries;$x++)
        {
            //get the command bit
            $initialDelay=null;
            $copySpacing=null;
            $HStep=null;
            $copies=null;
            $H=null;
            $W=null;

            $commandTXT=substr($input,0,2);
            $command=hexdec($commandTXT);
            $input = substr($input, 2);

            if(!($command & 0x80))
            {
                $initialDelay=substr($input,0,2);
                $input=substr($input, 2);

                if(!($command & 0x20))
                {
                    $copySpacing=substr($input,0,2);
                    $input=substr($input, 2);
                }
            }

            if(!($command & 0x40))
            {
                $HStep = substr($input,0,2);
                $input=substr($input, 2);
            }

            if(!($command & 0x20))
            {
                $copies = substr($input,0,2);
                $input=substr($input, 2);
            }


            $column = substr($input,0,2);
            $input=substr($input, 2);


            $addr = substr($input,0,4);
            $input=substr($input, 4);

            if($command & 0x10)
            {
                $H = substr($input,0,2);
                $input=substr($input, 2);
                $W = substr($input,0,2);
                $input=substr($input, 2);
            }

            $enemyArray[] = array(
                "command"=>$commandTXT,
                "initialDelay"=>$initialDelay,
                "copySpacing"=>$copySpacing,
                "HStep"=>$HStep,
                "copies"=>$copies,
                "column"=>$column,
                "addr"=>$addr,
                "H"=>$H,
                "W"=>$W
            );


        }
        $data=$enemyArray;

    }

    public static function shufflec2lists(Patcher $patcher)
    {
        //if in a 3 byte section, if the 3rd byte first bit is 1 then we do another 3 byte section
        //
        //
        //first bit is what gets spawned, second bit includes length?
        //
        //on 2 byte ones, first says what spawns, second is length before next command happens
        //on 4 byte loops
        //    first bit says what spawns, then number of spawns, time between spawns, then time to next command
        //
        //    on + byte ones
        //    in every extra iteration, thing that would be time says what spawns in next iteration, then number of spawns, time between spawns and then time to next command or next iteration


        $c0 = ["address"=>"101c4","data"=>"005400641C641C649C0A78649C0496649C045A649C0A78649C0A78641B641B649A0A5A641C641B64A005F0649C0A7864E20478649C0A5064020000"];

        $c1 = ["address"=>"11d6a","data"=>"000AC20F7864C20F3C28430AC40C7828C20F7878C40A3C46C2143C64C304966EC20F2864020000"];

        $c2 = ["address"=>"11f14","data"=>"C2061E0EC405283CE403F032E106F0C20A6446E3141E64C40A3C14E3141EE3142828C20A3232020000"];

        $c3 = ["address"=>"123f5", "data"=>"0012E6081E34E632641EE702641EA108321EE002961E1B0A1B149C0F1414E20A641E9C143264020000"];

        $c4 = ["address"=>"125cd", "data"=>"00149C321E50E21432509C0A5064D9043C14E0039614E8049632E60A463CE704823C9B033C9C1E7864E61446649C0A3264A0030A5AF6032814020000"];

        $c5 = ["address"=>"12afa", "data"=>"7B149C3C0A289B04643CEE0A3C3C9A0F2832D9083C28E30F1428EE083C289A1E28329A141432020000"];

        //also has a loop point, was removed see c7 for details, i have split this into 2 sections to make the loop point safe

        $c6_1 = ["address"=>"12c80","data"=>"00129A0A289C141E169B033C28"];
        $c6_2 = ["address"=>"12c8D","data"=>"9A065A1EA0061E28EE06321EED081428"];


        //c7 is special, not only does it have something that i don't know how to parse, it has enemies through the boss that i don't want to shuffle, therefore i have manually cut the input off early
        //there is a pointer in a 01 command to point to b18b, so i cut JUST before that point
        $c7 = ["address"=>"13175","data"=>"0011E91446E6085A176728E9143C640A641EEC086428E1089628E6147850E904C850E4087864"];

        //c8 also hits a loop before the boss that is removed, also remove the last phase because enemies end up spawning into the boss
        $c8 = ["address"=>"13337","data"=>"0014EC285A9B14F01EE21432A10A6432E7063C32C3065032"];

        //c9 uses a strange firt byte 02 that copies a clusterfuck of memory as it's last command, so we'll skip that
        $c9 = ["address"=>"1389f","data"=>"9C280F289A141E14BE025A28E904C850A00C3C1EA10A3C50EE055A28EA047864F60F5A78BE047864"];

        $c10 = ["address"=>"13ac9","data"=>"9C1E1E329B063C509A0A789C0A1464E9043CEE086478EA0828789B047878A00846329C0F3C9B0478649C051E78F60364649C0A3250BE0AC8509C1428649A066464E9043264020000"];


        $c11 = ["address"=>"12077","data"=>"000AC20FB432E3083C6EE30A463CC2059664E3050A32C206B41EC306B464E102B428C2061464020000"];

        $c12 = ["address"=>"12243","data"=>"420A4246434664286464C20F643CE30A7832C2023264E402783CC20A3C64C30A782800640064C20A1E64020000"];

        //shortened by one to fix enemies spawning into the boss
        $c13 = ["address"=>"12797","data"=>"00149C14289C644628E2067828A006501ED902641E9C3C5A1E9B03F064D904641E9C32283C"];

        $c14 =  ["address"=>"12971","data"=>"00149C0F280AE802F0329C0A5050E20AC81EE60A5A32A0051432E804F0A00A3C1EE60A3C289C1E50A10A7864020000"];

        //this does that crazy data pass of c9, but i leave it in the stream because i consider command 2 to be an end string now
        $c15 = ["address"=>"12e06","data"=>"7B14ED101E329A0A323CE903501EEE03324B6B19ED1414A004283CED1414A1063C3C9C143CE2142D78020C35C40D2AD68E51F40000"];

        //c16 includes my fix
        $c16 = ["address"=>"12fec","data"=>"7B146A50E0035A649B0350649C0A1E50F602F078A10A28509C0A1E32ED0A1EEE0A32786B78F6043C50A0045AE30F287FEA056464020000"];

        //this has the looping point and i will remove it
        $c17 = ["address"=>"134c1","data"=>"0014EC051414E106281EC3063C28E60F3C32E705286EF6026428EC08503CE4045A50E70A3264"];

        $c18 = ["address"=>"136a5","data"=>"0011A10A3C3FE904503C9C14281E9B0232647614C3043CE70378E60A649C143C64F60350149C141464020000"];

        $c19 = ["address"=>"13d0f","data"=>"960A461E96141E1E9A04501EA0083C960F3C50EA0450E90650509C141E9B02F014EE0464646B32A10A46329C1932EE066464020000"];

        $c20 = ["address"=>"13e32","data"=>"E90A3C329C0F5AEA085A46A10A509A0F3C50961478A00882E9043232E914C89A0FC8A004C8EE08C8649C1E5AA008C8649B04C8961E1E64020000"];

        $inputData = array($c0,$c1,$c2,$c3,$c4,$c5,$c6_1,$c6_2,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15,$c16,$c17,$c18,$c19,$c20);

        foreach($inputData as $corridor)
        {
            $input = $corridor["data"];
            $split = array();
            while(strlen($input) > 0)
            {
                //take first byte
                $length = 0;
                $first = substr($input, 0, 2);
                //if($first == "00"||$first == "02"||$first == "AC")
                    if(substr($input, 2, 4)=="00")
                        break;

                if($first == "02")
                    break;

                if(128 & hexdec($first))
                {
                    // 2 more values then length
                    $length += 6;
                }
                else
                {
                    $length += 2;
                    //only length yet
                }
                CorridorShuffler::parsec2length($length, $input);

                $split[]= substr($input,0,$length);

                if($first == "01")
                    throw new Exception("hit a loop point");
               $input = substr($input,$length);

            }

            shuffle($split);
            $patchstring = implode('', $split);

            $patcher->addChange($patchstring,$corridor["address"]);
        }

    }

    public static function parsec2length(&$length, $input)
    {
        $first = substr($input, $length, 2);
        if(128 & hexdec($first))
        {
            // 2 more values then length
            $length += 6;
            CorridorShuffler::parsec2length($length, $input);
        }
        else
        {
            $length += 2;
            //only length yet
        }
    }



    public static function shuffleCorridorInternals(Patcher $patcher,bool $shuffleInternals, ?array $bosses, bool $log)
    {

        //c0
        $c0 = array("id"=>0, "address"=>"10055", "data"=>"000000008700000161830000045983000007C5BC00000202B48100000604400503140161824108083100618242010542066182030880FE080D9E841400000010900106044482060604D38345CE061104A08346640097FCA184476400970CA1844C020164832A030607E0FCA083E10CA083E304A1844202000000DB844414260C04C78445163A0904C7844613260C04C7845C03000001BA0406024400180605D184458C180609D184D20506036026FCA08361260CA083C21500DB8404060605C05C04A18441001208FCA083420C12080CA083435A080706D184445A0E0508D184C2060607400001220005844100012204058442000122080584430001220C0584040C0D0404001984052809FC040C198406440D04040019844E070605C01704A184C108FCA083C2080CA08343160504049D83441605040B9D83580700800085070606E0002784610603978342040003059783430500030797834404000309978365060B97838607059107000000");

        //c1
        $c1 = array("id"=>1,"address"=>"11c60", "data"=>"000004139600000191850000000001000007439D000002025A9D0000060341000605006696420606050466966346095E975500060241051103072C9642000F0307AD8891000603E100E3966212009F964004000700C196C3000606410014030C25976246FD2597030A140902021B966450041B966550081B9666500C1B9627010601E100338845010602E105859842000408096696BD010602E10A85984200010AF8669635020603E10785986250031B9663500A1B968F02060243000404FED188440004040CD188BC02008000BC0207559DC10205D5020604610A00E396621C009F96400E000700C196E3095E97F30203259D");

        //c11
        $c11 = array("id"=>11,"address"=>"11f3b", "data"=>"00000413960000018E85000000800100000748A00000020267A006000604C102FCAE97C2020CAE97631005AE9740000B0206EB971E00060541001805F8EB9742051805FAEB97430A1805F7EB97441432030AAE97455A0A03032C96B40006040400020202022C9605140202020A2C96062802FE02062C9607320302020A2C9604010603420C2205077997431222050979974132140600049868010603401E0D05FB0498450C0203037D98460C0203077D98CC01060441000C0A00EB970328050203011B96042805FE030D1B9645460503071B963002060542000114076096430A010F056096440A010F0960964514010A0360964614010A0B609644020080004402075DA04E02056C0206020200030300F479976346FEAE97C6020332A0");

        //c2
        $c2 = array("id"=>2,"address"=>"11d8f", "data"=>"00000413960000019185000000000100000748A000000202049F0100060886FF0301329684FF0503329680FF0705329682FF090732968301090732968101070932968501050B32968701030D3296090006064401010A00329645000104013296460001040D32964701010A0E329640281900F8369641301900F536961800060444020A04062C9645000A04082C9646040A04082C9647020A040A2C96460006030208170403002597031208FC020C1B9644430A03021B96B40006034000020600E3964102040600C19642040406009F96F00006060300140B02012C9644320502022C96455005020C2C96400C1A0300E396410E1C0300C19642101C03009F96540106030100010208003296020001FE080E32964014060008369670010605E10D1B96620C021B96630C051B96042408FE03051B960524080203091B96A4010602000502FF070632960105020107083296B801004000BD0107FD9EC10105C20106044000060008369641060C00F83696623601AD8863360DAD88FE0103DC9E");

        //c12
        $c12 = array("id"=>12,"address"=>"1209e", "data"=>"00000413960000018E850000000003000007439D0000020233A20000060545230E05022C96E2F9E986630403CD876411F92F87401E0A0000049832000604E10A7D89420F0102087D89431E01030B7D89443201040E7D897800060542000202027D896402067D89430002020A7D8940000700F9049841142C00002C9896000604E2027D89030A010302FF7D890419010303027D89052D010304017D89F00006046204027D8944000103067D8963040A7D89401411000804980E01060545141004052C96461910040D2C96E1F4E98642040008FECD87636CF42F8782010603E0FCE9864104000806CD87626CFC2F878C010605030001FF05066096041E020104016096053702FF03066096064B020102016096675A036096F40106034000060006049841030B00FA04986214078598940206030200010202027D890300010202067D8964000A7D89A3020602E2078598638C098598C0030605C203007D89C3030D7D89440A0003077D8945140003007D89461400030D7D89C003008000C00307559DDE0305DE030605E0F9E986610403CD876211F92F8763160A7D8964160D7D89FC030312A2");

        //c3
        $c3 = array("id"=>3,"address"=>"1226e", "data"=>"0000047190000001799000000000010000075BA700000202E5A306000607E0002E93E108F79242080407064B93031D02FD03044B93041D020302094B93052900FE0305A292063000020309A2923C0006044000040504F792411E06020D4B936237FEF792633E0A9991820006064000040404F792011E0601030A4B93622DFF4B9303080CFE0306A292040A08010301A29205120AFE0307A292B900060240000005082E934103000400F792DC0006054200000A08F792031E14FC02064B93000F1E0304024B93440D080303A292050C06FE0308A292270106034200000A002E93031E0C020308AA92040A0002020A99916801060441020304044B9342000303074B934402090402A292450A07030DA2928001060440060502014B9341020503054B9342000502094B93430403020E4B939E01060440020503004B9341000302044B9342040503074B93430605020C4B93B80107DBA3B80105BC01060440010307004B9341000303044B9342020303084B93430003070D4B93EE0103B6A3");

        //c13
        $c13 = array("id"=>13,"address"=>"12607", "data"=>"0000047190000001799000000000010000075BA70000020287A706000602E100C59162260017920C00017C902C0001799032000605610000C5916206001792030C0AFD030CA29204110A040202A292051608FE030EA2925A000602800405FC9991611402999173000602800405FC999161130099918C000605800405FC999141190002019991421A0002099991030104FC020DA292040002030308A292AF000602800405FC9991071007FC0205AA92C3000604E00D9991610AFB9991420300100B2591430D0002F92591E5000606E0FF99916114FF99916420FF99916517FD2591060A00020401A292072B00020401A29215010603E0FD999143030007FC259146140F0408A29269010606E0FE9991611A0D9991070F04020202AA924623030403A2924521020407A292442503030BA292A901077CA7A90105B3010603E0045292410C0202005292420C0202085292DB01060280FE03049A928102030A9A92EA010330A7");

        //c4
        $c4 = array("id"=>4,"address"=>"1241c", "data"=>"00000471900000017F9000000080030000075BA700000202BDA506000605E001BB954102000A013A94629801D49543160D0608A292041F0DFF050AA692AA000605E005BB9541020005053A94624D05D49543030D030DA29244180D030BA6920401060440001C04FDBB9541020F04FD3A9442111404FB1695031F0F020302AA928101060240000006F41695410300060C1695BD010608600004EE94610502169542140E0600BB9543160C0600D495043C1FF2060ABB95053E1DF2060AD495666E00EE946773FE169544020608601EFABB95610008BB9542200006FA3A9443020006083A94645C08D4954542070302A2924634060403A292470D0E040DA692BE020604E4FAD495400D0A0300EE9441120503001695022001040206AA92FD020604000001FF080F9B950118010108089B950219010108F39B95033101FF08FA9B9552030604000001FF080F9B950118010108089B950219010108F39B95033101FF08FA9B956603008000980307AFA5A20305A7030606000001FF0F0F9B95412D0112009B95026403FF060E9B950364030106F39B95648C02EE9465910016953304037D");

        //c14
        $c14 = array("id"=>14,"address"=>"127c1", "data"=>"000004719000000179900000075BA70000020261A90A000603E0035F93010801FE03FE5F930208010203085F933200060240000203FD5F93411C0103045F9378000604610000DE93440A0B0202B092450F0B0208B092662800A7937E00018290A000017990A5000605412D0103FC5F93020A0FFE050CA69203140B020506A692041E0C030505A692052808FD050DA692F5000603610000DE93640A05B092661400A793FB00018290090101799018010606010004020508DE93020606020508A7930002020205035F930406040107EFDE93050C060107EFA7930306020107F95F936301060700140F020302A292011C0AFE0408A292023C0F030208A292030001FE030A5F93041E020202085F930537030203FB5F93065501FE02FD5F93D1010608E100DE93620600A793631400DE93441E0A0300E19245240A0302E192463C020209E192473C02020CE192605000A793EB01018290210201799030020606610000DE93420A050205B0924322050208B092443C0B0200B092453C0B0209B092665A00A79336020182908A020179909402077CA794020594020603C105FD5F9342040005085F93632A055B93C1020347A9");

        //c5
        $c5 = array("id"=>5,"address"=>"1299e", "data"=>"000004889800000190980000008001000007C9AD00000202EAAA0000060441000502FEA499420205080CA49943073C0205C79940140604FE54995A000603E0009E9A610700F69A621200319A5A0001939868000190986C0001939882000603E10A8499423202030384990000050110FD3399DC000602E000E599610A00319ADE00019098E600019398F000060541051B06082699020A000603FD8499030D000603008499042800FA030D8499052B00FA030A849936010604020A000603058499030D000603088499040D00FA03028499051000FA03FF849954010607E000E599610A06C7994219000200C799431900020BC799642D05FC9B052800FD0204C799053C00030208C7995701019098A90106044100030205A499420A0303FDA499430A03030DA499641E05FC9BD10107E2AAD60105D6010604400006050154996132FFC799422A010305C79963320BC799260203C1AA");

        //c15
        $c15 = array("id"=>15,"address"=>"12ca0", "data"=>"000004889800000193980000008001000007C9AD00000202F6AD060006044100000F078099020000010E008099030000FF0E0E8099041A00FF0204809928000603001405FF000C2699410015040833990344340302009E9BC8000603E000E599611400319A620A05FC9BCC00019098DC00019398F000060441000B08053399420A0F060D339903211B0502059E9B0435230702FE9E9B72010602E000E599610A00319A76010190987C010193988601060340381402062699020A000204008B9B0323000204088B9BC20106040200000204008B9B0319000204088B9B0419000204008B9B0532000204088B9B08020604E000E599611E00319A620801FC9B030F02FB020AFC9B0C0201909826020193983A020720AE3A02053A020603010003FE02069E9B020F1E0402018B9B031410FE020C8B9B8A0203A8AD");

        //c6
        $c6 = array("id"=>6,"address"=>"12b21", "data"=>"000004889800000190980000000001000007C9AD0000020270AC0400060680020300469C81FE030C469C82020204469CE308469C040F00020204469C650F08469C1E0006030000100110F84B9901020BFF10084B99022814050204FC9B6400060341001410F8CA9B02140A070202FC9B432A00020AFC9B96000605400007100AE29B4105111008E29B4214190200FC9B632303FC9B643C0AFC9BE60006064000080709179C41041405FC179C4208200300179C040C02030201FC9B050C03030207FC9B662103FC9B0E0106044400040308FC9B652D00FC9B662D09FC9B4737010205FC9B4A01060640000119007A99410001190F7A9942050108027A99430501080D7A99440A0103047A99450A01030B7A9968010602420A140202FC9B4300140207FC9B9C0102009F010768ACA40105A90106044007040A02179C410B040AFE179C0200010202027A99030001FE020D7A99D8010341AC");

        //c16
        $c16 = array("id"=>16,"address"=>"12e39", "data"=>"000004889800000193980000000004000007C9AD00000202DCAF0100060440000702009E9A610700F69A42150000004C9B431500000D6C9B50000606401E14000526990400080202055B9A053208FE02075B9A068C080202055B9A07DC08FE02075B9A416E0203095B9A96010604E200F69A631E00319A40280C00FD3399413208000C33999A01019098B50101939844020604E000E5996105009E9A420C0000004C9B430C00000D6C9B8002060400460F020800269944000204065B9A6514095B9A0664280505045B9A3403060500460FFE080A269944000103055B9A45010103095B9A46282802075B9A6778056A9ABA030604E000F69A6107009E9A420E000A004C9B430E000A0D6C9B18040605E000F69A6107009E9A420E000A004C9B430E000A0D6C9B641E065B9A76040605E000F69A6107009E9A420E000A004C9B430E000A0D6C9B641E056A9AD4040604E000F69A6107009E9A420E000A004C9B430E000A0D6C9B280507D4AF320505320500000132050606E000F69A6125009E9A422C0005004C9B432C00050D6C9B4407020802469C450702080A469C360501909858050193988605039CAF");

        //c7
        $c7 = array("id"=>7,"address"=>"13021", "data"=>"000004638A0000016E8A0000078DB40000020265B1000006064100020500C38E4200020506C38E430002050CC38E44020205FFC38E4502020505C38E460202050BC38E1E000604E105968E4005000A055F8E42140A030D718D4332050300718D46000606E1FC968EE20D968E6305FB5F8E64050D5F8E650CFB4F8E660C0D4F8E69000603E0054F8E610AFC0E90620A0A0E9082000603000009020303968E0105070203035F8E020C0C0203034F8EB4000606E1FC968EE20C968E6305FC5F8E64050C5F8E650CFC4F8E660C0C4F8EC8000604E2F4218FE30A218F4001000FF42B8F4101000F0A2B8F32010603E1F4178FE20A178F43050803040E904A010606010002FE060CD18E022402020601D18E034802FE060CD18E641E08E68F654602E68F666407E68FB8010602E100218F4001000A002B8FF401000001FE0105FF010602E100918F620105078D");

        //c17
        $c17 = array("id"=>17,"address"=>"13356", "data"=>"000004638A0000016B8A0000078DB400000202B1B400000605C18F0C828B624200DA8AC346F0828B6446F0988B454B0037F4828B5A00060247000505012D8D6626014F8D82000602E000B68A42040042F0828B8B000605E0083F8B4309004608828B64040C988B471405050A3C8D464005020A5E8DC8000602E0FCB68A6712090F8BFA000604E000DA8A610900B68A431E010202278C452801020B278C2C010605E0093F8B61090A0F8B031400FF030B278C05210001020A278C062801FF0308278C5E010604E2093F8B4006000609E78B6304FFDA8A410A0008FFAE8B8E010604E30D0F8B6412FDB68A651905DA8C062800060200DA8CCA010605E2FFDA8AE1083F8B4009003200B88C434607030B5E8D44280A05014F8D260200000137020604E1002D8CD2030DA58A0305630304078D500B000000A58A10013A02053E020000004202000080");

        //c8
        $c8 = array("id"=>8,"address"=>"131a6", "data"=>"000004638A0000016E8A0000078DB400000784B60000020227B3000006040005000106F1DF8E010700FF060DDF8E42270A04F4DF8E432E0A0409DF8E64000602000014FB060DDF8E010A140506F1DF8E6E000603E2070E90630AFD0E9004140AFE020C0E90A500060302002D0702000E90030408FF020B0E906420FD0E90FA000607F4FF298F0201F50D298F02014001000DFE42904101000D0C42907269FF1F8F020173690D1F8F02014624140204E68F5E010603F706298F0201450100040542907621061F8F020181010603E101E68FE208E68F430A050205E68F90010604F401298F0201F50C298F020142010004004290430100040B4290A0010604F405298F0201F508298F020140010019066D9041010019096D90B1010602F2011F8F0201F30C1F8F0201B8010604E200218F630100918F640F00218F651000918FD3010602F6051F8F0201F7081F8F0201D6010606E200218F630100918F641400218F651500918F660C01E68F670C08E68FFE01078AB608020508020602000500FF06F7DF8E010700010608DF8E3002030EB3");

        //c18
        $c18 = array("id"=>18,"address"=>"134f0", "data"=>"000004638A0000016E8A00000000060000078DB400000784B60000020295B6000006060104000204F2BF8D020800FE04F6AD8D730706848A0201040A00FE040EBF8D050E0002040AAD8D760D08848A02011E0006040100000206F2BF8D52060004FAA58A1001030A00FE06FAAD8D74050A848A02013200060300010D0106027F8D01000F010606088E020510010606D18D8200060300000D0305007F8D01000F0305F2088E0205100305F2D18DB400000001E60006030000010502007F8DE10A7F8D621907718D0E0106050024000A02007F8D4100080301718D420208030C718D4338040207718D005002FE020B718D720106054102000201718D4200000204718D4302000207718D000F00FE0A0EBF8D141400FE050EA58A0A018B0101718A9001060680FE0C0F3790010500FE0C0F3790020A00FE0C0F3790030F00FE0C0F3790041400FE0C0F3790051900FE0C0F3790B801016E8AB801060280FE0C02AD8D91FE05FFA48A0A01C201078AB6CC0105CC0106060100000206F2BF8D52060004FAA58A1001030A00FE06FAAD8D74050A848A0201651400088E661900D18DEF010351B6");

        //c9
        $c9 = array("id"=>9,"address"=>"136d9", "data"=>"000007C5BC000002028FB800000604C1060C7087C203FECD874303010605AD884403010608AD88270006036300F42F8764270C2F87450A010308AD885A000606E1F9E9864204000203CD87631EF92F876406011D8A450F05020CAD880723080503021A8A8C000606E1F9E9864204000203CD87631EF92F87642009AD8865200BAD8866200DAD88B4000604E107E98642040002077087631E072F8764060C1D8ADC000604E107E98642040002077087631E072F87440A0203FFD1880E01060441000503003388420514030C1A8A43081103051A8A440D0D03021A8A40010603E1F5E98642040008FFCD87032600020308AD885401060344001A020AE986450411020A708746111A020A2F878601060244000605091D8A6526F52F87C2010603E10033886411F6E986650A0AE986D00106044207000700CD87430000060A70876662F62F87674E0A2F8712020602040008FB02061A8A05060C0202091A8A26020602440005030EBB88450F05020CBB8844020606010000FE080EBB88020A02FE080EAD8863140EBB88041E00FE030ABB88051F04FE070E1A8A062800FE040EBB88710207B8B876020576020603010000FE0402BB88020000FE020EBB88630A0EBB88A8020371B8");

        //c19
        $c19 = array("id"=>19,"address"=>"13b0f", "data"=>"00000483850000018E85000007C5BC00000202FFBC00000605411E010400BB884212010303BB884306010606BB884412010209BB88451E01040CBB8841000606C102FCD188C2020AD1880328050A03FDD188644307BB88051404030307AD8806140403020AAD889100060841460202FCD188422812020AD188631401AD88641404AD884508000307AD884618000307AD8867140AAD8860140DAD88FA000604E107AD88420A0403FF8889430E0403008889400A00020A9689220106034200040303888943040404048889040A00FE020DAD884001060342000404088889430404030A888944190003F5C2897C010604C1030BEE8942180003F6C289631605AD88042400FB020C8889B8010607C1030BEE8942180003F6C289631403BB88442400020BD188453C04030C88894637000200D1884737000206D1881202060441000D0409EE89420A0D030BEE89430610030C8889441010030D88891C020604050005020300AD88062105020300AD88671902AD88004105FE0309AD8876020603410A000605D18842130004F6C289441901040BEE89A802008000B70207F2BCBC0205BC020605610000D188620009D188630805D188441E1104008889451E11040C88891603039FBC");

        //c10
        $c10 = array("id"=>10,"address"=>"138d1", "data"=>"000000000200000483850000018E85000007C5BC00000202B9BA00000606E1FA6186E20B6186630BFAD386640B0BD386050A02FE04057D89060A020204087D892300060540050404017D89410004080E7D89642FFE7D8965150D7D8946080804061D8A5E000608000001FE050A7D896128037D890202100702031D8A033001050202D188643C087D896534047D89060317FC02055486070516FC0205D3868C0000000AA4000605E100548640020000006186421E2705049C8843322505039C88443250020D6186DA01060342001A05049288430A190505928804280501040A7D895E020601E000D38670020602E105548640020000056186D40206046166099C8842004805FED18843049D03FF7D8946D2010401BB88EE020000022A040601E005D3863C040606030006FC0206338804080BFC02062F87601EFB548641200009FB61866283FBD386072328FB020B2B8AA004060563000B5486440200090B618665650BD3860628140702022B8A47960005F961865E05060341190E02001D8A42140E02061D8A431A12020C1D8A9A0506046109011D8A62090C1D8A0300010204007D89040001FE030C7D89AA0507B8B8B40505B40506046700FA5486660E0C548640020006FA6186411000060C6186D2050606670003548646020003036186652303D3866226FAD386633F0CD38664340C618609060374BA");

        //c20
        $c20 = array("id"=>20,"address"=>"13d42", "data"=>"00000483850000019185000007C5BC0000020222BE00000605C406000B894032001BFE0B8901490AF9020D7D8902650A0802007D89637F0C2E8A8D00060341170A02067D89423304040D7D89634DFE2E8AFA000602E102E9866204022F870401060840048204FE2F8741008204FEE986075A04FE030DEE8905C804FE03F9C289620F022B8A63910A2B8A043214FD020C1A8A06AA080102011A8A1C0206080000080402062B8A455A1B020E1A8A0644170103011A8A075516FF03041A8A418C0202FD2E8A428C02020B2E8A63A0F7C28964A40AEE89D00207F2BCD50205D5020602C003003388611A0C7D890203030FBE");


        //c21
        $c21 = array("id"=>21,"address"=>"13e6a", "data"=>"00000000020000048385000001918500000602C1030C7087C204FECD87000007C2BF00000202E6BF27000603630DF42F8764000C2F87450A010308AD883A0005403C000603E1F9E9864204000203CD87631EF92F876400099BBE640007CBBF82000603E107E98642040003077087632B072F8796000523B4000604E107E98662040770876311072F8744000203FFD188D20009D1BED20007D1BFDC000525E600060241000A040033886232003388400109F8BE400107D7BF4001052F5E010603E0F5E98641040002FFCD87621EF52F877C01060364000AE98665040A708766110A2F879A010916BF9A0107DDBF9A010548A401060641001A02F5E98642041102FFCD8743111A02F52F8744001A020AE986450411020A708746111A020A2F87E001094BBFE00107E3BFE001054DEA010604E0F6E986E10AE9864204000000CD87430400000A708715020602C20300CD87C3030A7087220209A1BF2F020602640DF62F87650D0A2F87");

        //it
        $it = array("id"=>22,"address"=>"10521", "data"=>"00000000810000016183000004FD8400000202748500000705850000060840000800049B8441040F00089B8442062B000B9E8443010A00079B84440C0C000D9B84451E2100029E84460911000A9B8447060B00059B846400057800008000E803036A85");

        //escape
        $escape = array("id"=>30,"address"=>"1027d", "data"=>"00000000840000016483000004598300000200000006044000030600D3834200030608D38344000206FDA083450002060BA0830000000010300006064200012000D3834300012008D38344000020FBA083450000200DA08346120002FEA083472400010AA0837E00060246120001FEA083470000030AA083C600060246000602FEA083470006020AA083ED00016183FA0006050414140702049E84000004FF08079B840100040108089B84020A07FF08079B84030A070108089B842C0106050000190703009E8404000AFF08079B8405000A0108089B84060A0DFF08079B84070A0D0108089B847C0103FC82");

        //transform
        $transform = array("id"=>31,"address"=>"101fd", "data"=>"00000000860000016483000004598300000200000006064000001B04A18441020009FCA083420200090CA0834510240007B3844614100006B3844718240005B384380006034001000504A18441010002FCA083420100020CA083450006034000000204A1846300FDA08364000BA0834B00032E82");

        $inputData = array($c0,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13,$c14,$c15,$c16,$c17,$c18,$c19,$c20,$c21,$transform,$escape,$it);
        //$inputData = array($c16);

        foreach($inputData as $corridor)
        {
            $tokenized = CorridorShuffler::tokenizeCorridor($corridor["data"], $corridor["id"],$bosses);
            if($shuffleInternals&&$corridor["id"]!=21&&$corridor["id"]!=22)
            {
                $shuffledTokens = CorridorShuffler::shuffleIndividualCorridorInternals($tokenized);
                $outputHex = CorridorShuffler::writeToHex($shuffledTokens);
            }
            else
            {
                $outputHex = CorridorShuffler::writeToHex($tokenized);
            }
            $patcher->addChange($outputHex,$corridor["address"]);
        }



    }


    public static function shuffleIndividualCorridorInternals($inputArray) : array
    {
        $sections = array();
        $currentSection = null;
        foreach($inputArray as $entry)
        {

            #go through and place each command 6 in an array, when you make the array it should also mark the time of the first thing
            if($entry["command"]=="06")
            {
                if(array_key_exists("static", $entry))
                {
                    if(!is_null($currentSection))
                    {
                        $sections[]=$currentSection;
                        $currentSection = null;
                    }
                }
                else
                {
                    if(is_null($currentSection))
                    {
                        $currentSection=array("time"=>$entry["time"],"data"=>array());

                    }
                    $currentSection["data"][]=$entry;


                }
            }
        }
        if(!is_null($currentSection))
        {
            $sections[]=$currentSection;
        }


        $sectionscount = count($sections);
        for ($x = 0; $x < $sectionscount; $x++){
            shuffle( $sections[$x]["data"]);
        }



        $index = 0;
        foreach($sections as $section)
        {
           $time = 0;
           foreach($section["data"] as $entry)
           {
                while($inputArray[$index]["command"]!="06" || array_key_exists("static",$inputArray[$index]))
                {
                    $index++;
                }
                $entry_time = dechex(hexdec($section["time"])+$time);
                while(strlen($entry_time) < 4)
                {
                    $entry_time="0".$entry_time;
                }
                $entry["time"]=$entry_time;
                if(array_key_exists("time_to_next_important_command",$entry))
                {
                    $time = $time + $entry["time_to_next_important_command"];
                }
                $inputArray[$index]=$entry;

                $index++;
           }
        }

        #sort the new array by time
        $sortCrap = function($a, $b){
            if (hexdec($a["time"]) == hexdec($b["time"])) {
                return 0;
            }
            return (hexdec($a["time"]) < hexdec($b["time"])) ? -1 : 1;
        };

        usort($inputArray, $sortCrap);

        return $inputArray;

    }



    public static function writeToHex($inputArray) : String
    {
        $returnArray = "";
        foreach ($inputArray as $node)
        {

            $returnArray.=substr($node["time"],2,2).substr($node["time"],0,2).$node["command"];
            if(is_array($node["data"]))
            {
                $enemyCount= count($node["data"]);
                $returnArray.=Helpers::inthex($enemyCount);
                foreach ($node["data"] as $enemy)
                {
                    $returnArray.=$enemy["command"];
                    if(!is_null($enemy["initialDelay"]))
                    {
                        $returnArray.=$enemy["initialDelay"];
                    }
                    if(!is_null($enemy["copySpacing"]))
                    {
                        $returnArray.=$enemy["copySpacing"];
                    }
                    if(!is_null($enemy["HStep"]))
                    {
                        $returnArray.=$enemy["HStep"];
                    }
                    if(!is_null($enemy["copies"]))
                    {
                        $returnArray.=$enemy["copies"];
                    }

                    $returnArray.=$enemy["column"];
                    $returnArray.=$enemy["addr"];
                    if(!is_null($enemy["H"]))
                    {
                        $returnArray.=$enemy["H"];
                    }
                    if(!is_null($enemy["W"]))
                    {
                        $returnArray.=$enemy["W"];
                    }
                }

            }
            else
            {
                $returnArray.=$node["data"];
            }
        }
        return $returnArray;
    }
}