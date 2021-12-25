<?php

namespace TGL\MapGen;

use TGL\MapGen\Items\ItemGenerator;

require_once("Generator.php");
require_once("SplitMap.php");
require_once("Patcher.php");
require_once("Helpers.php");
require_once("EnemyBalancer.php");
require_once("CorridorShuffler.php");

require_once("./Items/ItemGenerator.php");



$log = true;
$generator = new Generator();

$writefiles = true;

$secret = false;
$fasterStartingFire = true;
$eesAlwaysFarmable = true;
$removeFlash = true;

$generateItems=true;
$patcher = new Patcher();

$patchBalance = true; //can not be on if secret is on, must be on if corridor miniboss or boss shuffling is on
$shuffleCorridors = true;
$shuffleCorridorInternals = true;
$randomizeMinibosses = true;

$shuffleBosses = true;
$shuffleFinalBoss = true;

$forceShields = true; //requires generateItems
if($log)
    echo "start\n";


if($generateItems)
{
    $itemLibraries = ItemGenerator::prepareItems($patcher,5,5,4,9,10,6, $forceShields,5,5,3,5,$log);
}
else if(secret)
{
    $itemLibraries = [SecretLibrary::getItemLibrary(),SecretLibrary::getSingleShopLibrary(),SecretLibrary::getMultiShopLibrary()];
}
else
{
    $itemLibraries = [ItemLibrary::getItemLibrary(),Itemlibrary::getSingleShopLibrary(),ItemLibrary::getMultiShopLibrary()];
}




$shuffledBosses = null;
if($patchBalance && !$secret)
{
    if($shuffleBosses)
    {
        $shuffledBosses = CorridorShuffler::randomizeBosses($patcher,$shuffleFinalBoss);

        if($shuffleFinalBoss && $shuffledBosses["final"]["id"]!="4f")
        {
            //change it's graphics
            $patcher->addChange("00070F0E1935393400070E0D1F3B373B72797C7CFEE6E6ED7D7E7F7FCFDFDFFEFE8E7880000E32D8FEF0F880000E3CE0408080A6B9D74F4FB07060667F7BFFFF110010113E4EB86040CEB2D83E72F8E0C0CEBCE000070F0E1935393400070E0D1F3B373B7A5D4476F2E6EEFF7D7E7F7FCFDFDFFEC08E7FFEC39FF2DFFFF1FCFFFFFFFDE0EFDFB77F0DEFEDB6FB777F9FFFFFF7FF3DBAE97BC69C39E1FEFDF7873F7FFFFFFF7B319ACD6B663CFBF7FEFDFBF7FFFFFE8E7FFEC39FF2C7FEF1FCFFFFFFFDF81E60CF7F71F74FDFE19F3FBEBE7BFF7F7F3D0C1C26337B2B7F3F0B0B393C7C3D3B3F7D72377FFCAE3F3E7E7F7F7FFFFFD7FBFFF97FEFFFFFBEBFFFEFEE7FF7F5EFDFB77F0DEFED36FB777F9FFFFFF7FF3DFAE97BC79F3FFDFEFDF7873F7FFFFFEFDFB77D0CECEEB3FB777F9FFFFFF7FFEACB972895BCDFFF1F3F7FFFFAF3E8C0B9F3F3B7F3FBDDD7C78F8FCFCFE77B7E3F3F0F06370C0A062F2F173B3E0F0D070F1B3B674F5D371F0F172F5F7F7F3F1FBFBFF7F775DD9FBFFFFF7F7FFFFFFEFEDA9BF96161418100FBFBF9E1E1C18100555ABEF97FEFE9EABEBFFFEEEE7FF7F5FDF5CDEDEEEFBBB5F7FEFEDE9FDFFFFB87F01F6DEDE58FB7F8FFFF9F9FDFFFFFEFFE773FB9AADAFAFFFFBFFFDFDDFDFDB0FCE84A1E7EF4E4F0BCFC7E7EFEFCFCD46C58D8D8FC7E16EC7C78F8B8FC7E1EF475652A361E0B0FFB7A7A3F3F1F0F0D030311000603020006FF017f02112FAB5AEEE711ff0611FFFBC1FFD65D320E0000FF7FEF663F0F1100121100000303030F0F0E00000303040E030D03060D091F3F2F6003070F0F1F23317FDBB7AEF675BFBEE7E7CFDFCFEE7CFFE730FC68CA9E7EF4E4F03CF8FAFEFEFCFCD4ECD85858B4E6C6ECDCB8B8B87C7EFE061B3D3F3F77379F1F273B3F5F6FFF7FCF6676F67E4D6D7B3F5F6FFFFFBF9F9FE1E1E0C0C0C0E0E0E1E1E0C0C0C0E0E0E0F0D8C840000000E0F0F8F8780C06002ACB5A1212142C14FDFD7C1E1E1E3C1C1A161638081060C01E1A1A3010204080CCCAFA774B392A47FCBE9E5F7F776D04035000081E050000723030180E031100071180C0601100051180C0E0B0B868340A6AAB0570D8583C0E6E6F07","bad6");
            $patcher->addChange("2c1c0c3d1707","17380");
        }
    }
    EnemyBalancer::rebalanceAll($patcher, true, true);

    if($shuffleCorridors||$shuffleBosses)
    {
        CorridorShuffler::shuffleCorridors($patcher,$shuffleCorridors,$shuffledBosses, $log);
    }

    if($randomizeMinibosses)
    {
        CorridorShuffler::randomizeMinibosses($patcher, false);
    }
}

#shuffle corridor internals
if($shuffleCorridorInternals)
{
    CorridorShuffler::shuffleCorridorInternals($patcher,true,$shuffledBosses,$log);//this call MUST come before boss shuffling
}
else if(!is_null($shuffledBosses))
{
    CorridorShuffler::shuffleCorridorInternals($patcher,false,$shuffledBosses,$log);//this call MUST come before boss shuffling
}


$map = $generator->run($itemLibraries[0],$itemLibraries[1],$itemLibraries[2],$secret,18,25, 3,0, false,6, 3,10, $log);

if($log) {
    $map->printAreas();
    $map->drawExits();
    echo "done\n";
}
$hex = $map->writeHex($log);


if($writefiles)
{
	if($secret)
	{
    		$rawdata = file_get_contents("./sourceroms/secret4rando.nes");
	}
	else
	{
    		$rawdata = file_get_contents("./sourceroms/tgl.nes");
	}
	$rom = bin2hex($rawdata);
}
//patch the consecutive fire default value
if($fasterStartingFire)
    $patcher->addChange("07","087DE");

if($eesAlwaysFarmable)
    $patcher->addChange("ff","4206");
if($removeFlash)
{
    //instead of patching the calls i could just make the function not set anything, but this is already done
    $patcher->addChange("0f","18bbd");//ee flash
    $patcher->addChange("0f","894c");//end flash
    $patcher->addChange("EAEAEA","d375"); //boss flash
}



#add the map change
{
    $patcher->addChange($hex, "14A7E");
}



if($writefiles) {

    $filetag = "tgl";
    if($secret)
    {
        $filetag = "secret";
    }

    $patcher->writeRom("./output/" . $filetag . date("Y-m-d-H-i-s") . ".nes", $rom);


    $byte_array =$patcher->writeIPS();
    $file = fopen("./output/" . $filetag . date("Y-m-d-H-i-s") . ".ips", "w") or die("Unable to open file!");
    fwrite($file, $byte_array);
    fclose($file);


    $csvfile = fopen("./output/" . $filetag . date("Y-m-d-H-i-s") . ".csv", "w") or die("Unable to open file!");
    fwrite($csvfile, SplitMap::split($hex));
    fclose($csvfile);
}
else
{
    $byte_array =$patcher->writeIPS();
    echo base64_encode($byte_array);
}

if($log) {
    EnemyBalancer::printStatistics();
}

