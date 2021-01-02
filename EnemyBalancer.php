<?php


namespace TGL\MapGen;

abstract class Boss
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
}


class EnemyBalancer
{



    static function rebalanceAll(Patcher $patcher, bool $randomizeHealth, bool $randomizeDamage)
    {
        EnemyBalancer::shiftDamage($patcher);
        EnemyBalancer::shiftHealth($patcher, $randomizeHealth);
    }
    static function shiftDamage(Patcher $patcher)
    {

        $damageOffset = 119098;

        $patcher->addChange(Helpers::inthex(Damage::EyegoreBlue),dechex($damageOffset+Boss::EyegoreBlue));
        $patcher->addChange(Helpers::inthex(Damage::EyegoreRed),dechex($damageOffset+Boss::EyegoreRed));
        $patcher->addChange(Helpers::inthex(Damage::Zibzub),dechex($damageOffset+Boss::Zibzub));
        $patcher->addChange(Helpers::inthex(Damage::ClawbotGreen),dechex($damageOffset+Boss::ClawbotGreen));
        $patcher->addChange(Helpers::inthex(Damage::ClawbotBlue),dechex($damageOffset+Boss::ClawbotBlue));
        $patcher->addChange(Helpers::inthex(Damage::BombarderRed),dechex($damageOffset+Boss::BombarderRed));
        $patcher->addChange(Helpers::inthex(Damage::OptomonGreen),dechex($damageOffset+Boss::OptomonGreen));
        $patcher->addChange(Helpers::inthex(Damage::OptomonBlue),dechex($damageOffset+Boss::OptomonBlue));
        $patcher->addChange(Helpers::inthex(Damage::OptomonRed),dechex($damageOffset+Boss::OptomonRed));
        $patcher->addChange(Helpers::inthex(Damage::FleepaBlue),dechex($damageOffset+Boss::FleepaBlue));
        $patcher->addChange(Helpers::inthex(Damage::FleepaRed),dechex($damageOffset+Boss::FleepaRed));
        $patcher->addChange(Helpers::inthex(Damage::Crawdaddy),dechex($damageOffset+Boss::Crawdaddy));
        $patcher->addChange(Helpers::inthex(Damage::Terramute),dechex($damageOffset+Boss::Terramute));
        $patcher->addChange(Helpers::inthex(Damage::Glider),dechex($damageOffset+Boss::Glider));
        $patcher->addChange(Helpers::inthex(Damage::GrimgrinBlue),dechex($damageOffset+Boss::GrimgrinBlue));
        $patcher->addChange(Helpers::inthex(Damage::GrimgrinRed),dechex($damageOffset+Boss::GrimgrinRed));
        $patcher->addChange(Helpers::inthex(Damage::BombarderBlue),dechex($damageOffset+Boss::BombarderBlue));
        $patcher->addChange(Helpers::inthex(Damage::ClawbotRed),dechex($damageOffset+Boss::ClawbotRed));
        $patcher->addChange(Helpers::inthex(Damage::It),dechex($damageOffset+Boss::It));
        $patcher->addChange(Helpers::inthex(Damage::SpiderGreen),dechex($damageOffset+Boss::SpiderGreen));
        $patcher->addChange(Helpers::inthex(Damage::SpiderBlue),dechex($damageOffset+Boss::SpiderBlue));
        $patcher->addChange(Helpers::inthex(Damage::SpiderRed),dechex($damageOffset+Boss::SpiderRed));
        $patcher->addChange(Helpers::inthex(Damage::CrabGreen),dechex($damageOffset+Boss::CrabGreen));
        $patcher->addChange(Helpers::inthex(Damage::CrabBlue),dechex($damageOffset+Boss::CrabBlue));
        $patcher->addChange(Helpers::inthex(Damage::CrabReb),dechex($damageOffset+Boss::CrabReb));
        $patcher->addChange(Helpers::inthex(Damage::Carpet),dechex($damageOffset+Boss::Carpet));
        $patcher->addChange(Helpers::inthex(Damage::BouncerGreen),dechex($damageOffset+Boss::BouncerGreen));
        $patcher->addChange(Helpers::inthex(Damage::BouncerBlue),dechex($damageOffset+Boss::BouncerBlue));
        $patcher->addChange(Helpers::inthex(Damage::BouncerRed),dechex($damageOffset+Boss::BouncerRed));
        $patcher->addChange(Helpers::inthex(Damage::CrystalStar),dechex($damageOffset+Boss::CrystalStar));
        $patcher->addChange(Helpers::inthex(Damage::Skull),dechex($damageOffset+Boss::Skull));
    }

    static function shiftHealth(Patcher $patcher, bool $randomizeHealth)
    {
        $healthOffset = 118971;

        if($randomizeHealth)
        {
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::EyegoreBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::EyegoreRed));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::Zibzub));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::ClawbotGreen));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::ClawbotBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::ClawbotRed));

            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::OptomonGreen));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::OptomonBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::OptomonRed));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::FleepaBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::FleepaRed));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::Crawdaddy));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::Terramute));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::Glider));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::GrimgrinBlue));
            $patcher->addChange(Helpers::inthex(rand(12,24)),dechex($healthOffset+Boss::GrimgrinRed));
            $patcher->addChange(Helpers::inthex(rand(8,16)),dechex($healthOffset+Boss::BombarderBlue));
            $patcher->addChange(Helpers::inthex(rand(8,16)),dechex($healthOffset+Boss::BombarderRed));

            $patcher->addChange(Helpers::inthex(rand(28,36)),dechex($healthOffset+Boss::It));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::SpiderGreen));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::SpiderBlue));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::SpiderRed));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::CrabGreen));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::CrabBlue));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::CrabReb));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::Carpet));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::BouncerGreen));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::BouncerBlue));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::BouncerRed));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::CrystalStar));
            $patcher->addChange(Helpers::inthex(rand(4,12)),dechex($healthOffset+Boss::Skull));
        }
        else
        {
            $patcher->addChange(Helpers::inthex(Health::EyegoreBlue),dechex($healthOffset+Boss::EyegoreBlue));
            $patcher->addChange(Helpers::inthex(Health::EyegoreRed),dechex($healthOffset+Boss::EyegoreRed));
            $patcher->addChange(Helpers::inthex(Health::Zibzub),dechex($healthOffset+Boss::Zibzub));
            $patcher->addChange(Helpers::inthex(Health::ClawbotGreen),dechex($healthOffset+Boss::ClawbotGreen));
            $patcher->addChange(Helpers::inthex(Health::ClawbotBlue),dechex($healthOffset+Boss::ClawbotBlue));
            $patcher->addChange(Helpers::inthex(Health::BombarderRed),dechex($healthOffset+Boss::BombarderRed));
            $patcher->addChange(Helpers::inthex(Health::OptomonGreen),dechex($healthOffset+Boss::OptomonGreen));
            $patcher->addChange(Helpers::inthex(Health::OptomonBlue),dechex($healthOffset+Boss::OptomonBlue));
            $patcher->addChange(Helpers::inthex(Health::OptomonRed),dechex($healthOffset+Boss::OptomonRed));
            $patcher->addChange(Helpers::inthex(Health::FleepaBlue),dechex($healthOffset+Boss::FleepaBlue));
            $patcher->addChange(Helpers::inthex(Health::FleepaRed),dechex($healthOffset+Boss::FleepaRed));
            $patcher->addChange(Helpers::inthex(Health::Crawdaddy),dechex($healthOffset+Boss::Crawdaddy));
            $patcher->addChange(Helpers::inthex(Health::Terramute),dechex($healthOffset+Boss::Terramute));
            $patcher->addChange(Helpers::inthex(Health::Glider),dechex($healthOffset+Boss::Glider));
            $patcher->addChange(Helpers::inthex(Health::GrimgrinBlue),dechex($healthOffset+Boss::GrimgrinBlue));
            $patcher->addChange(Helpers::inthex(Health::GrimgrinRed),dechex($healthOffset+Boss::GrimgrinRed));
            $patcher->addChange(Helpers::inthex(Health::BombarderBlue),dechex($healthOffset+Boss::BombarderBlue));
            $patcher->addChange(Helpers::inthex(Health::ClawbotRed),dechex($healthOffset+Boss::ClawbotRed));
            $patcher->addChange(Helpers::inthex(Health::It),dechex($healthOffset+Boss::It));
            $patcher->addChange(Helpers::inthex(Health::SpiderGreen),dechex($healthOffset+Boss::SpiderGreen));
            $patcher->addChange(Helpers::inthex(Health::SpiderBlue),dechex($healthOffset+Boss::SpiderBlue));
            $patcher->addChange(Helpers::inthex(Health::SpiderRed),dechex($healthOffset+Boss::SpiderRed));
            $patcher->addChange(Helpers::inthex(Health::CrabGreen),dechex($healthOffset+Boss::CrabGreen));
            $patcher->addChange(Helpers::inthex(Health::CrabBlue),dechex($healthOffset+Boss::CrabBlue));
            $patcher->addChange(Helpers::inthex(Health::CrabReb),dechex($healthOffset+Boss::CrabReb));
            $patcher->addChange(Helpers::inthex(Health::Carpet),dechex($healthOffset+Boss::Carpet));
            $patcher->addChange(Helpers::inthex(Health::BouncerGreen),dechex($healthOffset+Boss::BouncerGreen));
            $patcher->addChange(Helpers::inthex(Health::BouncerBlue),dechex($healthOffset+Boss::BouncerBlue));
            $patcher->addChange(Helpers::inthex(Health::BouncerRed),dechex($healthOffset+Boss::BouncerRed));
            $patcher->addChange(Helpers::inthex(Health::CrystalStar),dechex($healthOffset+Boss::CrystalStar));
            $patcher->addChange(Helpers::inthex(Health::Skull),dechex($healthOffset+Boss::Skull));
        }


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