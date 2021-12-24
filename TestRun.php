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

