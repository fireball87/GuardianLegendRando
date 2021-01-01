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



echo "start\n";
$generator = new Generator();

$secret = false;
$fasterStartingFire = true;
$generateItems=true;
$patcher = new Patcher();

$patchBalance = true; //can not be on if secret is on, must be on if corridor or boss shuffling is on, not required for miniboss shuffling, but certainly reccomended
$shuffleCorridors = true;


if($generateItems)
{
    $itemLibraries = ItemGenerator::prepareItems($patcher,5,5,4,9,10,9,5,5,0,5);
}
else if(secret)
{
    $itemLibraries = [SecretLibrary::getItemLibrary(),SecretLibrary::getSingleShopLibrary(),SecretLibrary::getMultiShopLibrary()];
}
else
{
    $itemLibraries = [ItemLibrary::getItemLibrary(),Itemlibrary::getSingleShopLibrary(),ItemLibrary::getMultiShopLibrary()];
}


if($patchBalance && !$secret)
{
    $patcher->addChange("606060","1c172");
    $patcher->addChange("20a9ff","1cfd0");
    $patcher->addChange("9d20062088fe60","1ffb9");

    EnemyBalancer::rebalanceAll($patcher);
    if($shuffleCorridors)
    {
        CorridorShuffler::shuffleCorridors($patcher);
    }
}




$map = $generator->run($itemLibraries[0],$itemLibraries[1],$itemLibraries[2],$secret,18,25, 3,0, false,6, 3,10);

$map->printAreas();
$map->drawExits();
echo "done\n";
$hex = $map->writeHex();

if($secret)
{
    $rawdata = file_get_contents("./sourceroms/secret4rando.nes");
}
else
{
    $rawdata = file_get_contents("./sourceroms/tgl.nes");
}
$rom = bin2hex($rawdata);

//patch the consecutive fire default value
if($fasterStartingFire)
    $patcher->addChange("07","087DE");





$patcher->addChange($hex,"14A7E");

$filetag = "tgl";
if($secret)
{
    $filetag = "secret";
}

$patcher->writeRom("./output/".$filetag.date("Y-m-d-H-i-s").".nes",$rom);



$csvfile = fopen("./output/".$filetag.date("Y-m-d-H-i-s").".csv", "w") or die("Unable to open file!");
fwrite($csvfile, SplitMap::split($hex));
fclose($csvfile);


