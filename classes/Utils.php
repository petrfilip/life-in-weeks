<?php


class Utils
{
    public static function loadData($pathToFile, $clazz)
    {
        $string = file_get_contents($pathToFile);
        $json = json_decode($string, true);

        $newArray = [];
        foreach ($json as $jsonItem) {
            $class = (new ReflectionClass($clazz))->newInstance();
            foreach ($jsonItem as $key => $value) $class->{$key} = $value;
            $newArray[$class->yearWeek] = $class;
//        array_push($newArray, $class);
        }
        return $newArray;
    }

    public static function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= (1024 * 1024 * 1024); //1073741824
                break;
            case 'm':
                $val *= (1024 * 1024); //1048576
                break;
            case 'k':
                $val *= 1024;
                break;
        }

        return $val;
    }



}