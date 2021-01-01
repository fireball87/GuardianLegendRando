<?php


namespace TGL\MapGen;


class SecretLibrary
{
    public static function getItems(int $area)
    {
        switch ($area) {
            case 0:
                return ["35","05","00"];
            case 1:
                return ["39","2b"];
            case 2:
                return ["07","04","38"];
            case 3:
                return ["32","25","24"];
            case 4:
                return ["09","36"];
            case 5:
                return ["01","23","30"];
            case 6:
                return ["02","34"];
            case 7:
                return ["2a","06","0a"];
            case 8:
                return ["2c","33"];
            case 9:
                return ["2e","26"];
            case 10:
                return ["22"];
            default:
                $error = 'Requested Invalid Area';
                throw new Exception($error);

        }
    }





    public static function getTextBlock(int $area)
    {
        switch ($area) {
            case 0:
                return ["01","02","03"]; //removed "00" because we'll place it manually earlier
            case 1:
                return [];
            case 2:
                return ["0f"];
            case 3:
                return ["10"];
            case 4:
                return [];//text 12 will be generated with c4
            case 5:
                return ["13"];
            case 6:
                return ["0e"];
            case 7:
                return ["0c"];
            case 8:
                return ["0d"];
            case 9:
                return ["14"];
            case 10:
                return ["11"];
            default:
                $error = 'Requested Invalid Area';
                throw new Exception($error);

        }
    }


    public static function getMultiShop(int $area)
    {
        switch ($area) {
            case 2:
                return ["3f"];
            case 3:
                return ["40"];
            case 5:
                return ["41"];
            case 6:
                return ["42"];
            case 10:
                return ["43"];
            case 0:
            case 1:
            case 4:
            case 7:
            case 8:
            case 9:
                return [];



            default:
                $error = 'Requested Invalid Area';
                throw new Exception($error);

        }

    }
    public static function getSingleShop(int $area)
    {
        switch ($area) {
            case 0:
                return ["3d","3a","3b"];
            case 2:
                return["3c"];
            case 4:
                return["3e"];
            case 1:
            case 3:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
                return [];



            default:
                $error = 'Requested Invalid Area';
                throw new Exception($error);

        }
    }

}