<?php


namespace TGL\MapGen;

abstract class Entity
{
    const EyegoreBlue = 24;
    const EyegoreRed = 25;

    const Zibzub = 35;
    const ClawbotGreen = 36;
    const ClawbotBlue = 37;
    const ClawbotRed = 38;

    const OptomonGreen = 45;
    const OptomonBlue = 46;
    const OptomonRed = 47;

    const FleepaBlue = 64;
    const FleepaRed = 65;

    const Crawdaddy = 69;
    const Terramute = 70;
    const Glider = 71;
    const GrimgrinBlue = 72;
    const GrimgrinRed = 73;

    const BombarderBlue = 76;
    const BombarderRed = 77;

    const It = 79;

    const SpiderGreen = 29;
    const SpiderBlue = 30;
    const SpiderRed = 31;
    const CrabGreen = 85;
    const CrabBlue = 86;
    const CrabReb=87;
    const Carpet = 88;
    const BouncerGreen = 90;
    const BouncerBlue = 91;
    const BouncerRed = 92;
    const CrystalStar = 93;
    const Skull = 94;



    const defSmall = 54;
    const defLarge = 55;

    const groundEyeSmall = 126;
    const groundEyeBig = 127;

    const blueSkullEnemy = 22;
    const redSkullEnemy = 23;
}

abstract class Health
{
    const EyegoreBlue = 32 / 2;
    const EyegoreRed = 48 / 2;

    const Zibzub = 14;
    const ClawbotGreen = 12;
    const ClawbotBlue = 16 * 2;
    const ClawbotRed = 20 / 4;

    const OptomonGreen = 10 * 4;
    const OptomonBlue = 16 * 2;
    const OptomonRed = 20;

    const FleepaBlue = 5 * 4;
    const FleepaRed = 8 * 4;

    const Crawdaddy = 9 * 4;
    const Terramute = 16 * 2;
    const Glider = 6;
    const GrimgrinBlue = 56 / 2;
    const GrimgrinRed = 88 / 2;

    const BombarderBlue = 12 * 2;
    const BombarderRed = 15 / 4;

    const It = 136 / 4;

    const SpiderGreen = 1 * 4;
    const SpiderBlue = 3 * 4;
    const SpiderRed = 6 / 2;
    const CrabGreen = 2 * 4;
    const CrabBlue = 4 * 4;
    const CrabReb=6;
    const Carpet = 6;
    const BouncerGreen = 4 * 2;
    const BouncerBlue = 7 * 2;
    const BouncerRed = 16 / 2;
    const CrystalStar = 13;
    const Skull = 18 / 2;

    const defSmall = 4 * 8;
    const defLarge = 8 * 8;

    const groundEyeSmall = 24 / 2;
    const groundEyeBig = 24 / 2;

    const blueSkullEnemy = 16 / 4;
    const redSkullEnemy = 16 / 4;
}


abstract class Damage
{
    const EyegoreBlue = 96 / 2;
    const EyegoreRed = 112 / 2;

    const Zibzub = 48;
    const ClawbotGreen = 16;
    const ClawbotBlue = 36 * 2;
    const ClawbotRed = 80 / 4;

    const OptomonGreen = 48 * 4;
    const OptomonBlue = 64 * 2;
    const OptomonRed = 96;

    const FleepaBlue = 8 * 4;
    const FleepaRed = 16 * 4;

    const Crawdaddy = 12 * 4;
    const Terramute = 16 * 2;
    const Glider = 48;
    const GrimgrinBlue = 64 / 2;
    const GrimgrinRed = 96 / 2;

    const BombarderBlue = 32 * 2;
    const BombarderRed = 48 / 4;

    const It = 32 / 4;

    const SpiderGreen = 16 * 4;
    const SpiderBlue = 48 * 4;
    const SpiderRed = 80 / 2;
    const CrabGreen = 48 * 4;
    const CrabBlue = 64 * 4;
    const CrabReb = 96;
    const Carpet = 32;
    const BouncerGreen = 24 * 2;
    const BouncerBlue = 40 * 2;
    const BouncerRed = 56 / 2;
    const CrystalStar = 48;
    const Skull = 64 / 2;


    //const defSmall = 32;
    //const defLarge = 32;

    //const groundEyeSmall = 32;
    //const groundEyeBig = 32;

    const blueSkullEnemy = 96 / 4;
    const redSkullEnemy = 96 / 4;

}


class EnemyBalancer
{



    static function rebalanceAll(Patcher $patcher, bool $randomizeHealth, bool $randomizeDamage)
    {
        $patcher->addChange("606060","1c172");
        $patcher->addChange("20a9ff","1cfd0");
        $patcher->addChange("9d20062088fe60","1ffb9");

        EnemyBalancer::shiftDamage($patcher);
        EnemyBalancer::shiftHealth($patcher, $randomizeHealth);

        EnemyBalancer::shiftProjectiles($patcher);
    }

    static function shiftProjectiles(Patcher $patcher)
    {
        /*        //bandaid patch for nails and homing eyes, fix later
        $patcher->addChange("0606","1a22a");*/


        //patch the health and damage tables to a normalized value
        $patcher->addChange("1010101010101010","1A227");
        $patcher->addChange("0505050505050505","1A21F");



        //then we need to patch the code to be balanced
        //patch the code we'll be jumping to
        //jsr $c0a2
        //20a2c0
        //jmp $FE88 , jmp because it saves me writing a return here as i can just piggy back fe88s ret
        //4c88fe
        $patcher->addChange("20a2c04c88fe","1ffc0");

        //patch the jump to this code, which is at
        $patcher->addChange("20b0ff","1a11a");




    }

    static function shiftDamage(Patcher $patcher)
    {

        $damageOffset = 119098;

        $patcher->addChange(Helpers::inthex(Damage::EyegoreBlue),dechex($damageOffset+Entity::EyegoreBlue));
        $patcher->addChange(Helpers::inthex(Damage::EyegoreRed),dechex($damageOffset+Entity::EyegoreRed));
        $patcher->addChange(Helpers::inthex(Damage::Zibzub),dechex($damageOffset+Entity::Zibzub));
        $patcher->addChange(Helpers::inthex(Damage::ClawbotGreen),dechex($damageOffset+Entity::ClawbotGreen));
        $patcher->addChange(Helpers::inthex(Damage::ClawbotBlue),dechex($damageOffset+Entity::ClawbotBlue));
        $patcher->addChange(Helpers::inthex(Damage::BombarderRed),dechex($damageOffset+Entity::BombarderRed));
        $patcher->addChange(Helpers::inthex(Damage::OptomonGreen),dechex($damageOffset+Entity::OptomonGreen));
        $patcher->addChange(Helpers::inthex(Damage::OptomonBlue),dechex($damageOffset+Entity::OptomonBlue));
        $patcher->addChange(Helpers::inthex(Damage::OptomonRed),dechex($damageOffset+Entity::OptomonRed));
        $patcher->addChange(Helpers::inthex(Damage::FleepaBlue),dechex($damageOffset+Entity::FleepaBlue));
        $patcher->addChange(Helpers::inthex(Damage::FleepaRed),dechex($damageOffset+Entity::FleepaRed));
        $patcher->addChange(Helpers::inthex(Damage::Crawdaddy),dechex($damageOffset+Entity::Crawdaddy));
        $patcher->addChange(Helpers::inthex(Damage::Terramute),dechex($damageOffset+Entity::Terramute));
        $patcher->addChange(Helpers::inthex(Damage::Glider),dechex($damageOffset+Entity::Glider));
        $patcher->addChange(Helpers::inthex(Damage::GrimgrinBlue),dechex($damageOffset+Entity::GrimgrinBlue));
        $patcher->addChange(Helpers::inthex(Damage::GrimgrinRed),dechex($damageOffset+Entity::GrimgrinRed));
        $patcher->addChange(Helpers::inthex(Damage::BombarderBlue),dechex($damageOffset+Entity::BombarderBlue));
        $patcher->addChange(Helpers::inthex(Damage::ClawbotRed),dechex($damageOffset+Entity::ClawbotRed));
        $patcher->addChange(Helpers::inthex(Damage::It),dechex($damageOffset+Entity::It));
        $patcher->addChange(Helpers::inthex(Damage::SpiderGreen),dechex($damageOffset+Entity::SpiderGreen));
        $patcher->addChange(Helpers::inthex(Damage::SpiderBlue),dechex($damageOffset+Entity::SpiderBlue));
        $patcher->addChange(Helpers::inthex(Damage::SpiderRed),dechex($damageOffset+Entity::SpiderRed));
        $patcher->addChange(Helpers::inthex(Damage::CrabGreen),dechex($damageOffset+Entity::CrabGreen));
        $patcher->addChange(Helpers::inthex(Damage::CrabBlue),dechex($damageOffset+Entity::CrabBlue));
        $patcher->addChange(Helpers::inthex(Damage::CrabReb),dechex($damageOffset+Entity::CrabReb));
        $patcher->addChange(Helpers::inthex(Damage::Carpet),dechex($damageOffset+Entity::Carpet));
        $patcher->addChange(Helpers::inthex(Damage::BouncerGreen),dechex($damageOffset+Entity::BouncerGreen));
        $patcher->addChange(Helpers::inthex(Damage::BouncerBlue),dechex($damageOffset+Entity::BouncerBlue));
        $patcher->addChange(Helpers::inthex(Damage::BouncerRed),dechex($damageOffset+Entity::BouncerRed));
        $patcher->addChange(Helpers::inthex(Damage::CrystalStar),dechex($damageOffset+Entity::CrystalStar));
        $patcher->addChange(Helpers::inthex(Damage::Skull),dechex($damageOffset+Entity::Skull));


        $patcher->addChange(Helpers::inthex(Damage::redSkullEnemy),dechex($damageOffset+Damage::redSkullEnemy));
        $patcher->addChange(Helpers::inthex(Damage::blueSkullEnemy),dechex($damageOffset+Damage::blueSkullEnemy));

    }

    static function shiftHealth(Patcher $patcher, bool $randomizeHealth)
    {
        $healthOffset = 118971;

        if($randomizeHealth)
        {
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::EyegoreBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::EyegoreRed));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::Zibzub));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::ClawbotGreen));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::ClawbotBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::ClawbotRed));

            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::OptomonGreen));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::OptomonBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::OptomonRed));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::FleepaBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::FleepaRed));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::Crawdaddy));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::Terramute));
            $patcher->addChange(Helpers::inthex(rand(4,8)),dechex($healthOffset+Entity::Glider));
            $patcher->addChange(Helpers::inthex(rand(18,30)),dechex($healthOffset+Entity::GrimgrinBlue));
            $patcher->addChange(Helpers::inthex(rand(20,32)),dechex($healthOffset+Entity::GrimgrinRed));
            $patcher->addChange(Helpers::inthex(rand(8,12)),dechex($healthOffset+Entity::BombarderBlue));
            $patcher->addChange(Helpers::inthex(rand(8,12)),dechex($healthOffset+Entity::BombarderRed));

            $patcher->addChange(Helpers::inthex(rand(30,40)),dechex($healthOffset+Entity::It));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::SpiderGreen));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::SpiderBlue));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::SpiderRed));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::CrabGreen));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::CrabBlue));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::CrabReb));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::Carpet));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::BouncerGreen));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::BouncerBlue));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::BouncerRed));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::CrystalStar));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Entity::Skull));


            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::groundEyeBig));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Entity::groundEyeSmall));

        }
        else
        {
            $patcher->addChange(Helpers::inthex(Health::EyegoreBlue),dechex($healthOffset+Entity::EyegoreBlue));
            $patcher->addChange(Helpers::inthex(Health::EyegoreRed),dechex($healthOffset+Entity::EyegoreRed));
            $patcher->addChange(Helpers::inthex(Health::Zibzub),dechex($healthOffset+Entity::Zibzub));
            $patcher->addChange(Helpers::inthex(Health::ClawbotGreen),dechex($healthOffset+Entity::ClawbotGreen));
            $patcher->addChange(Helpers::inthex(Health::ClawbotBlue),dechex($healthOffset+Entity::ClawbotBlue));
            $patcher->addChange(Helpers::inthex(Health::BombarderRed),dechex($healthOffset+Entity::BombarderRed));
            $patcher->addChange(Helpers::inthex(Health::OptomonGreen),dechex($healthOffset+Entity::OptomonGreen));
            $patcher->addChange(Helpers::inthex(Health::OptomonBlue),dechex($healthOffset+Entity::OptomonBlue));
            $patcher->addChange(Helpers::inthex(Health::OptomonRed),dechex($healthOffset+Entity::OptomonRed));
            $patcher->addChange(Helpers::inthex(Health::FleepaBlue),dechex($healthOffset+Entity::FleepaBlue));
            $patcher->addChange(Helpers::inthex(Health::FleepaRed),dechex($healthOffset+Entity::FleepaRed));
            $patcher->addChange(Helpers::inthex(Health::Crawdaddy),dechex($healthOffset+Entity::Crawdaddy));
            $patcher->addChange(Helpers::inthex(Health::Terramute),dechex($healthOffset+Entity::Terramute));
            $patcher->addChange(Helpers::inthex(Health::Glider),dechex($healthOffset+Entity::Glider));
            $patcher->addChange(Helpers::inthex(Health::GrimgrinBlue),dechex($healthOffset+Entity::GrimgrinBlue));
            $patcher->addChange(Helpers::inthex(Health::GrimgrinRed),dechex($healthOffset+Entity::GrimgrinRed));
            $patcher->addChange(Helpers::inthex(Health::BombarderBlue),dechex($healthOffset+Entity::BombarderBlue));
            $patcher->addChange(Helpers::inthex(Health::ClawbotRed),dechex($healthOffset+Entity::ClawbotRed));
            $patcher->addChange(Helpers::inthex(Health::It),dechex($healthOffset+Entity::It));
            $patcher->addChange(Helpers::inthex(Health::SpiderGreen),dechex($healthOffset+Entity::SpiderGreen));
            $patcher->addChange(Helpers::inthex(Health::SpiderBlue),dechex($healthOffset+Entity::SpiderBlue));
            $patcher->addChange(Helpers::inthex(Health::SpiderRed),dechex($healthOffset+Entity::SpiderRed));
            $patcher->addChange(Helpers::inthex(Health::CrabGreen),dechex($healthOffset+Entity::CrabGreen));
            $patcher->addChange(Helpers::inthex(Health::CrabBlue),dechex($healthOffset+Entity::CrabBlue));
            $patcher->addChange(Helpers::inthex(Health::CrabReb),dechex($healthOffset+Entity::CrabReb));
            $patcher->addChange(Helpers::inthex(Health::Carpet),dechex($healthOffset+Entity::Carpet));
            $patcher->addChange(Helpers::inthex(Health::BouncerGreen),dechex($healthOffset+Entity::BouncerGreen));
            $patcher->addChange(Helpers::inthex(Health::BouncerBlue),dechex($healthOffset+Entity::BouncerBlue));
            $patcher->addChange(Helpers::inthex(Health::BouncerRed),dechex($healthOffset+Entity::BouncerRed));
            $patcher->addChange(Helpers::inthex(Health::CrystalStar),dechex($healthOffset+Entity::CrystalStar));
            $patcher->addChange(Helpers::inthex(Health::Skull),dechex($healthOffset+Entity::Skull));

            $patcher->addChange(Helpers::inthex(Health::groundEyeBig),dechex($healthOffset+Entity::groundEyeBig));
            $patcher->addChange(Helpers::inthex(Health::groundEyeSmall),dechex($healthOffset+Entity::groundEyeSmall));

        }

        //set the health of the a0 boss, we don't shuffle this area so no randomization
        $patcher->addChange(Helpers::inthex(Health::defLarge),dechex($healthOffset+Entity::defLarge));
        $patcher->addChange(Helpers::inthex(Health::defSmall),dechex($healthOffset+Entity::defSmall));

        //fix the health of red and blue skull enemies
        $patcher->addChange(Helpers::inthex(Health::redSkullEnemy),dechex($healthOffset+Entity::redSkullEnemy));
        $patcher->addChange(Helpers::inthex(Health::blueSkullEnemy),dechex($healthOffset+Entity::blueSkullEnemy));

    }

    static function printStatistics()
    {
        //printHealth
        echo "\n\nHealth Values\n";
        echo "".floor(Health::EyegoreBlue/4)."\t".floor(Health::EyegoreBlue/2)."\t".floor(Health::EyegoreBlue)."\t".floor(Health::EyegoreBlue*2)."\t".floor(Health::EyegoreBlue*4)."\t"."EyegoreBlue\n";
        echo "".floor(Health::EyegoreRed/4)."\t".floor(Health::EyegoreRed/2)."\t".floor(Health::EyegoreRed)."\t".floor(Health::EyegoreRed*2)."\t".floor(Health::EyegoreRed*4)."\t"."EyegoreRed\n";
        echo "".floor(Health::Zibzub/4)."\t".floor(Health::Zibzub/2)."\t".floor(Health::Zibzub)."\t".floor(Health::Zibzub*2)."\t".floor(Health::Zibzub*4)."\t"."Zibzub\n";
        echo "".floor(Health::ClawbotGreen/4)."\t".floor(Health::ClawbotGreen/2)."\t".floor(Health::ClawbotGreen)."\t".floor(Health::ClawbotGreen*2)."\t".floor(Health::ClawbotGreen*4)."\t"."ClawbotGreen\n";
        echo "".floor(Health::ClawbotBlue/4)."\t".floor(Health::ClawbotBlue/2)."\t".floor(Health::ClawbotBlue)."\t".floor(Health::ClawbotBlue*2)."\t".floor(Health::ClawbotBlue*4)."\t"."ClawbotBlue\n";
        echo "".floor(Health::ClawbotRed/4)."\t".floor(Health::ClawbotRed/2)."\t".floor(Health::ClawbotRed)."\t".floor(Health::ClawbotRed*2)."\t".floor(Health::ClawbotRed*4)."\t"."BombarderBlue\n";
        echo "".floor(Health::OptomonGreen/4)."\t".floor(Health::OptomonGreen/2)."\t".floor(Health::OptomonGreen)."\t".floor(Health::OptomonGreen*2)."\t".floor(Health::OptomonGreen*4)."\t"."OptomonGreen\n";
        echo "".floor(Health::OptomonBlue/4)."\t".floor(Health::OptomonBlue/2)."\t".floor(Health::OptomonBlue)."\t".floor(Health::OptomonBlue*2)."\t".floor(Health::OptomonBlue*4)."\t"."OptomonBlue\n";
        echo "".floor(Health::OptomonRed/4)."\t".floor(Health::OptomonRed/2)."\t".floor(Health::OptomonRed)."\t".floor(Health::OptomonRed*2)."\t".floor(Health::OptomonRed*4)."\t"."OptomonRed\n";
        echo "".floor(Health::FleepaBlue/4)."\t".floor(Health::FleepaBlue/2)."\t".floor(Health::FleepaBlue)."\t".floor(Health::FleepaBlue*2)."\t".floor(Health::FleepaBlue*4)."\t"."FleepaBlue\n";
        echo "".floor(Health::FleepaRed/4)."\t".floor(Health::FleepaRed/2)."\t".floor(Health::FleepaRed)."\t".floor(Health::FleepaRed*2)."\t".floor(Health::FleepaRed*4)."\t"."FleepaRed\n";
        echo "".floor(Health::Crawdaddy/4)."\t".floor(Health::Crawdaddy/2)."\t".floor(Health::Crawdaddy)."\t".floor(Health::Crawdaddy*2)."\t".floor(Health::Crawdaddy*4)."\t"."Crawdaddy\n";
        echo "".floor(Health::Terramute/4)."\t".floor(Health::Terramute/2)."\t".floor(Health::Terramute)."\t".floor(Health::Terramute*2)."\t".floor(Health::Terramute*4)."\t"."Terramute\n";
        echo "".floor(Health::Glider/4)."\t".floor(Health::Glider/2)."\t".floor(Health::Glider)."\t".floor(Health::Glider*2)."\t".floor(Health::Glider*4)."\t"."Glider\n";
        echo "".floor(Health::GrimgrinBlue/4)."\t".floor(Health::GrimgrinBlue/2)."\t".floor(Health::GrimgrinBlue)."\t".floor(Health::GrimgrinBlue*2)."\t".floor(Health::GrimgrinBlue*4)."\t"."GrimgrinBlue\n";
        echo "".floor(Health::GrimgrinRed/4)."\t".floor(Health::GrimgrinRed/2)."\t".floor(Health::GrimgrinRed)."\t".floor(Health::GrimgrinRed*2)."\t".floor(Health::GrimgrinRed*4)."\t"."GrimgrinRed\n";
        echo "".floor(Health::BombarderBlue/4)."\t".floor(Health::BombarderBlue/2)."\t".floor(Health::BombarderBlue)."\t".floor(Health::BombarderBlue*2)."\t".floor(Health::BombarderBlue*4)."\t"."BombarderBlue\n";
        echo "".floor(Health::BombarderRed/4)."\t".floor(Health::BombarderRed/2)."\t".floor(Health::BombarderRed)."\t".floor(Health::BombarderRed*2)."\t".floor(Health::BombarderRed*4)."\t"."BombarderRed\n";
        echo "".floor(Health::It/4)."\t".floor(Health::It/2)."\t".floor(Health::It)."\t".floor(Health::It*2)."\t".floor(Health::It*4)."\t"."It\n";
        echo "".floor(Health::SpiderGreen/4)."\t".floor(Health::SpiderGreen/2)."\t".floor(Health::SpiderGreen)."\t".floor(Health::SpiderGreen*2)."\t".floor(Health::SpiderGreen*4)."\t"."SpiderGreen\n";
        echo "".floor(Health::SpiderBlue/4)."\t".floor(Health::SpiderBlue/2)."\t".floor(Health::SpiderBlue)."\t".floor(Health::SpiderBlue*2)."\t".floor(Health::SpiderBlue*4)."\t"."SpiderBlue\n";
        echo "".floor(Health::SpiderRed/4)."\t".floor(Health::SpiderRed/2)."\t".floor(Health::SpiderRed)."\t".floor(Health::SpiderRed*2)."\t".floor(Health::SpiderRed*4)."\t"."SpiderRed\n";
        echo "".floor(Health::CrabGreen/4)."\t".floor(Health::CrabGreen/2)."\t".floor(Health::CrabGreen)."\t".floor(Health::CrabGreen*2)."\t".floor(Health::CrabGreen*4)."\t"."CrabGreen\n";
        echo "".floor(Health::CrabBlue/4)."\t".floor(Health::CrabBlue/2)."\t".floor(Health::CrabBlue)."\t".floor(Health::CrabBlue*2)."\t".floor(Health::CrabBlue*4)."\t"."CrabBlue\n";
        echo "".floor(Health::CrabReb/4)."\t".floor(Health::CrabReb/2)."\t".floor(Health::CrabReb)."\t".floor(Health::CrabReb*2)."\t".floor(Health::CrabReb*4)."\t"."CrabReb\n";
        echo "".floor(Health::Carpet/4)."\t".floor(Health::Carpet/2)."\t".floor(Health::Carpet)."\t".floor(Health::Carpet*2)."\t".floor(Health::Carpet*4)."\t"."Carpet\n";
        echo "".floor(Health::BouncerGreen/4)."\t".floor(Health::BouncerGreen/2)."\t".floor(Health::BouncerGreen)."\t".floor(Health::BouncerGreen*2)."\t".floor(Health::BouncerGreen*4)."\t"."BouncerGreen\n";
        echo "".floor(Health::BouncerBlue/4)."\t".floor(Health::BouncerBlue/2)."\t".floor(Health::BouncerBlue)."\t".floor(Health::BouncerBlue*2)."\t".floor(Health::BouncerBlue*4)."\t"."BouncerBlue\n";
        echo "".floor(Health::BouncerRed/4)."\t".floor(Health::BouncerRed/2)."\t".floor(Health::BouncerRed)."\t".floor(Health::BouncerRed*2)."\t".floor(Health::BouncerRed*4)."\t"."BouncerRed\n";
        echo "".floor(Health::CrystalStar/4)."\t".floor(Health::CrystalStar/2)."\t".floor(Health::CrystalStar)."\t".floor(Health::CrystalStar*2)."\t".floor(Health::CrystalStar*4)."\t"."CrystalStar\n";
        echo "".floor(Health::Skull/4)."\t".floor(Health::Skull/2)."\t".floor(Health::Skull)."\t".floor(Health::Skull*2)."\t".floor(Health::Skull*4)."\t"."Skull\n";




        //printHealth
        echo "\n\nDamage Values\n";
        echo "".floor(Damage::EyegoreBlue/4)."\t".floor(Damage::EyegoreBlue/2)."\t".floor(Damage::EyegoreBlue)."\t".floor(Damage::EyegoreBlue*2)."\t".floor(Damage::EyegoreBlue*4)."\t"."EyegoreBlue\n";
        echo "".floor(Damage::EyegoreRed/4)."\t".floor(Damage::EyegoreRed/2)."\t".floor(Damage::EyegoreRed)."\t".floor(Damage::EyegoreRed*2)."\t".floor(Damage::EyegoreRed*4)."\t"."EyegoreRed\n";
        echo "".floor(Damage::Zibzub/4)."\t".floor(Damage::Zibzub/2)."\t".floor(Damage::Zibzub)."\t".floor(Damage::Zibzub*2)."\t".floor(Damage::Zibzub*4)."\t"."Zibzub\n";
        echo "".floor(Damage::ClawbotGreen/4)."\t".floor(Damage::ClawbotGreen/2)."\t".floor(Damage::ClawbotGreen)."\t".floor(Damage::ClawbotGreen*2)."\t".floor(Damage::ClawbotGreen*4)."\t"."ClawbotGreen\n";
        echo "".floor(Damage::ClawbotBlue/4)."\t".floor(Damage::ClawbotBlue/2)."\t".floor(Damage::ClawbotBlue)."\t".floor(Damage::ClawbotBlue*2)."\t".floor(Damage::ClawbotBlue*4)."\t"."ClawbotBlue\n";
        echo "".floor(Damage::ClawbotRed/4)."\t".floor(Damage::ClawbotRed/2)."\t".floor(Damage::ClawbotRed)."\t".floor(Damage::ClawbotRed*2)."\t".floor(Damage::ClawbotRed*4)."\t"."BombarderBlue\n";
        echo "".floor(Damage::OptomonGreen/4)."\t".floor(Damage::OptomonGreen/2)."\t".floor(Damage::OptomonGreen)."\t".floor(Damage::OptomonGreen*2)."\t".floor(Damage::OptomonGreen*4)."\t"."OptomonGreen\n";
        echo "".floor(Damage::OptomonBlue/4)."\t".floor(Damage::OptomonBlue/2)."\t".floor(Damage::OptomonBlue)."\t".floor(Damage::OptomonBlue*2)."\t".floor(Damage::OptomonBlue*4)."\t"."OptomonBlue\n";
        echo "".floor(Damage::OptomonRed/4)."\t".floor(Damage::OptomonRed/2)."\t".floor(Damage::OptomonRed)."\t".floor(Damage::OptomonRed*2)."\t".floor(Damage::OptomonRed*4)."\t"."OptomonRed\n";
        echo "".floor(Damage::FleepaBlue/4)."\t".floor(Damage::FleepaBlue/2)."\t".floor(Damage::FleepaBlue)."\t".floor(Damage::FleepaBlue*2)."\t".floor(Damage::FleepaBlue*4)."\t"."FleepaBlue\n";
        echo "".floor(Damage::FleepaRed/4)."\t".floor(Damage::FleepaRed/2)."\t".floor(Damage::FleepaRed)."\t".floor(Damage::FleepaRed*2)."\t".floor(Damage::FleepaRed*4)."\t"."FleepaRed\n";
        echo "".floor(Damage::Crawdaddy/4)."\t".floor(Damage::Crawdaddy/2)."\t".floor(Damage::Crawdaddy)."\t".floor(Damage::Crawdaddy*2)."\t".floor(Damage::Crawdaddy*4)."\t"."Crawdaddy\n";
        echo "".floor(Damage::Terramute/4)."\t".floor(Damage::Terramute/2)."\t".floor(Damage::Terramute)."\t".floor(Damage::Terramute*2)."\t".floor(Damage::Terramute*4)."\t"."Terramute\n";
        echo "".floor(Damage::Glider/4)."\t".floor(Damage::Glider/2)."\t".floor(Damage::Glider)."\t".floor(Damage::Glider*2)."\t".floor(Damage::Glider*4)."\t"."Glider\n";
        echo "".floor(Damage::GrimgrinBlue/4)."\t".floor(Damage::GrimgrinBlue/2)."\t".floor(Damage::GrimgrinBlue)."\t".floor(Damage::GrimgrinBlue*2)."\t".floor(Damage::GrimgrinBlue*4)."\t"."GrimgrinBlue\n";
        echo "".floor(Damage::GrimgrinRed/4)."\t".floor(Damage::GrimgrinRed/2)."\t".floor(Damage::GrimgrinRed)."\t".floor(Damage::GrimgrinRed*2)."\t".floor(Damage::GrimgrinRed*4)."\t"."GrimgrinRed\n";
        echo "".floor(Damage::BombarderBlue/4)."\t".floor(Damage::BombarderBlue/2)."\t".floor(Damage::BombarderBlue)."\t".floor(Damage::BombarderBlue*2)."\t".floor(Damage::BombarderBlue*4)."\t"."BombarderBlue\n";
        echo "".floor(Damage::BombarderRed/4)."\t".floor(Damage::BombarderRed/2)."\t".floor(Damage::BombarderRed)."\t".floor(Damage::BombarderRed*2)."\t".floor(Damage::BombarderRed*4)."\t"."BombarderRed\n";
        echo "".floor(Damage::It/4)."\t".floor(Damage::It/2)."\t".floor(Damage::It)."\t".floor(Damage::It*2)."\t".floor(Damage::It*4)."\t"."It\n";
        echo "".floor(Damage::SpiderGreen/4)."\t".floor(Damage::SpiderGreen/2)."\t".floor(Damage::SpiderGreen)."\t".floor(Damage::SpiderGreen*2)."\t".floor(Damage::SpiderGreen*4)."\t"."SpiderGreen\n";
        echo "".floor(Damage::SpiderBlue/4)."\t".floor(Damage::SpiderBlue/2)."\t".floor(Damage::SpiderBlue)."\t".floor(Damage::SpiderBlue*2)."\t".floor(Damage::SpiderBlue*4)."\t"."SpiderBlue\n";
        echo "".floor(Damage::SpiderRed/4)."\t".floor(Damage::SpiderRed/2)."\t".floor(Damage::SpiderRed)."\t".floor(Damage::SpiderRed*2)."\t".floor(Damage::SpiderRed*4)."\t"."SpiderRed\n";
        echo "".floor(Damage::CrabGreen/4)."\t".floor(Damage::CrabGreen/2)."\t".floor(Damage::CrabGreen)."\t".floor(Damage::CrabGreen*2)."\t".floor(Damage::CrabGreen*4)."\t"."CrabGreen\n";
        echo "".floor(Damage::CrabBlue/4)."\t".floor(Damage::CrabBlue/2)."\t".floor(Damage::CrabBlue)."\t".floor(Damage::CrabBlue*2)."\t".floor(Damage::CrabBlue*4)."\t"."CrabBlue\n";
        echo "".floor(Damage::CrabReb/4)."\t".floor(Damage::CrabReb/2)."\t".floor(Damage::CrabReb)."\t".floor(Damage::CrabReb*2)."\t".floor(Damage::CrabReb*4)."\t"."CrabReb\n";
        echo "".floor(Damage::Carpet/4)."\t".floor(Damage::Carpet/2)."\t".floor(Damage::Carpet)."\t".floor(Damage::Carpet*2)."\t".floor(Damage::Carpet*4)."\t"."Carpet\n";
        echo "".floor(Damage::BouncerGreen/4)."\t".floor(Damage::BouncerGreen/2)."\t".floor(Damage::BouncerGreen)."\t".floor(Damage::BouncerGreen*2)."\t".floor(Damage::BouncerGreen*4)."\t"."BouncerGreen\n";
        echo "".floor(Damage::BouncerBlue/4)."\t".floor(Damage::BouncerBlue/2)."\t".floor(Damage::BouncerBlue)."\t".floor(Damage::BouncerBlue*2)."\t".floor(Damage::BouncerBlue*4)."\t"."BouncerBlue\n";
        echo "".floor(Damage::BouncerRed/4)."\t".floor(Damage::BouncerRed/2)."\t".floor(Damage::BouncerRed)."\t".floor(Damage::BouncerRed*2)."\t".floor(Damage::BouncerRed*4)."\t"."BouncerRed\n";
        echo "".floor(Damage::CrystalStar/4)."\t".floor(Damage::CrystalStar/2)."\t".floor(Damage::CrystalStar)."\t".floor(Damage::CrystalStar*2)."\t".floor(Damage::CrystalStar*4)."\t"."CrystalStar\n";
        echo "".floor(Damage::Skull/4)."\t".floor(Damage::Skull/2)."\t".floor(Damage::Skull)."\t".floor(Damage::Skull*2)."\t".floor(Damage::Skull*4)."\t"."Skull\n";

    }


}