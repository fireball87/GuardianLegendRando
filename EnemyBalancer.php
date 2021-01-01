<?php


namespace TGL\MapGen;

abstract class Boss
{
    const EyegoreBlue = 24;
    const EyegoreRed = 25;

    const Zibzub = 35;
    const ClawbotGreen = 36;
    const ClawbotBlue = 37;
    const BombarderRed = 38;

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
    const ClawbotRed = 77;

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
    const BombarderRed = 20 / 4;

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
    const ClawbotRed = 15;

    const It = 136 / 2;

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
    const BombarderRed = 80 / 4;

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
    const ClawbotRed = 48;

    const It = 32 / 2;

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

    static function rebalanceAll(Patcher $patcher)
    {
        EnemyBalancer::shiftDamage($patcher);
        EnemyBalancer::shiftHealth($patcher);
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

    static function shiftHealth(Patcher $patcher)
    {
        $healthOffset = 118971;

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