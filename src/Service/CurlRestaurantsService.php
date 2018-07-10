<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 04/07/18
 * Time: 14:31
 */

namespace App\Service;


class CurlRestaurantsService
{

    /* INT */
    public $dw;

    CONST DAYWEEK = [
        1 => "Lundi",
        2 => "Mardi",
        3 => "Mercredi",
        4 => "Jeudi",
        5 => "Vendredi",
        6 => "Plats" // Exception pour la fin de semaine à La Petite Pause
    ];


    public function getDayOfTheWeek($dw){
        return self::DAYWEEK[$dw];
    }

    /* return INT */
    private function getIntDay(){
        $timestamp = time();
        $dw = date( "w", $timestamp);
        return $dw;
    }

    /* return STR */
    public function getDay(){
        $timestamp = time();
        $dw = date("w", $timestamp);
        $dw = self::DAYWEEK[$dw];
        return $dw;
    }

    /* return STR */
    public function getDayMinusOneDay(){
        if(self::getDay() != "Lundi"){
            $timestamp = time();
            $dw = date(strtotime('-1 day'), strtotime($timestamp));
            $dw = date("w", $dw);
            $dw = self::DAYWEEK[$dw];
            return $dw;
        } else {
            return "";
        }
    }

    /* return STR */
    private function getUrlInfo($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); //timeout in seconds
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Hi ! We\'re some guys from Netapsys/Anteo looking for food here...');
        $curlResult = curl_exec($ch);
        curl_close($ch);
        return $curlResult;
    }


    public function getCurlMenuMarcheBiot(){
        $dw = self::getDay();
        $url = 'http://sbiot.fr/accueil/plats-jour-de-semaine/';
        $curlResult = self::getUrlInfo($url);

//        $firstPregMatch = "/(?<=" . self::getDayMinusOneDay() . ")([^)]+)(?=" . strtolower(getDay()) . ")/";
//        $secondPregMatch = "/(?<=<li class='odd'><div><p class='item-text'>)([^)]+)(?=</p><p class='desc'>)/";
//        preg_match_all($firstPregMatch, $curlResult, $buffer);
//        preg_match_all($secondPregMatch, $buffer,$menu);

        //        "/(?<=" . self::getDayMinusOneDay() . "<\/div><\/div><\/li><li class='odd'><div><p class='item-text'>)(.*?)(<\/p>)/";

        $menu = [];

        if($dw == "Lundi"){
            preg_match_all("/(?<=<li class='odd'><div><p class='item-text'>)(.*?)(<\/p><p class='desc'>)/", $curlResult, $menu);
            if(!empty($menu)){
                return(array(iconv("UTF-8", "UTF-8//IGNORE",$menu[1][0]), $curlResult));
            } else {
                return(array($menu[1]["Une erreur est survenue lors du traitement"], $curlResult));
            }
        } else {
            $firstPregMatch = "/(?<=" . self::getDayMinusOneDay() . ")([^)]+)(?=" . self::getDay() . ")/";
            $secondPregMatch = "/(?<=<li class='odd'><div><p class='item-text'>)([^)]+)(?=<\/p><p class='desc'>)/";
            preg_match_all($firstPregMatch, $curlResult, $buffer);
            preg_match_all($secondPregMatch, $buffer[0][0],$menu);

            if(!empty($menu)){
                return(array(iconv("UTF-8", "UTF-8//IGNORE",@$menu[1][0]), $curlResult));
            } else {
                return(array($menu[0]["Une erreur est survenue lors du traitement"], $curlResult));
            }
        }
    }

    public function getCurlMenuMarcheBiotVege(){
        $dw = self::getDay();
        $curlResult = self::getCurlMenuMarcheBiot()[1];
        // Debug
        // $dw = "Jeudi";

        $pregMatch = "/(?=". $dw ."<\/div><\/div><\/li><li class='even'><div><p class='item-text'>).+?(?=<\/p><p class='desc'><img src=)/";
        $secondPregMatch = "/(?<=" . $dw . "<\/div><\/div><\/li><li class='even'><div><p class='item-text'>).*/";
        preg_match_all($pregMatch, $curlResult, $buffer);
        preg_match_all($secondPregMatch,$buffer[0][0], $menu);

        if($menu != null) {
            $cleanMenu = iconv("UTF-8", "UTF-8//IGNORE", (@$menu[0][0]));
        }
        return($cleanMenu);
    }

    public function getCurlMarcheBiotPrice(){
        $curlResult = self::getCurlMenuMarcheBiot()[1];

        preg_match_all(
            "/(?<=div class='value-col value-1'>)(.*?)(?=<\/div><div class='value-col value-2'>)/",
            $curlResult, $menu);

        $cleanMenu = iconv("UTF-8", "UTF-8//IGNORE",$menu[0][0]);

        return($cleanMenu);
    }

    public function getCurlMenuLesHirondelles(){
        $url = 'https://www.leshirondelles.fr/';
        $curlResult = self::getUrlInfo($url);

        $firstPregMatch = '/(?<=<h2>Le Menu du jour<\/h2>)([^)]+)(?=a href="https:\/\/www.leshirondelles.fr)/';
        preg_match_all($firstPregMatch, $curlResult, $menu);

        $secondPregMatch = "/(?<=<p>)(.*)(?=)/";
        //        var_export($menu[0]);
        preg_match_all($secondPregMatch, @$menu[0][0], $menu);

        $cleanMenu = @$menu[0][0];

        $cleaningMenu = ["<br />", "<br/>", "<br>", "<br...", "</p>"];
        foreach ($cleaningMenu as $cleanse){
            $cleanMenu = str_replace($cleanse,"", $cleanMenu);
        }

        return(array(iconv("UTF-8", "UTF-8//IGNORE",$cleanMenu), $curlResult));
    }

    public function getCurlMenuLeK(){
        $url = 'https://www.restaurant-le-k.com/a-table/';
        $curlResult = self::getUrlInfo($url);
        $dw = self::getIntDay();
        $thisDay = self::getDayOfTheWeek($dw);
        $end = self::getDayOfTheWeek($dw+1);

        preg_match_all("/(?<=<meta name=\"twitter:description\" content=\")(.*?)(?=\/>)/", $curlResult, $menu);
        $weekMenuArray = explode(" ", $menu[0][0]);

        /* On parse le résultat du pregmatch avec comme critère le jour de la semaine afin de récupérer le nom du plat indiqué
        entre les deux jours*/
        $newArray = [];
        $i = 0;
        $start = false;
        foreach ($weekMenuArray as $element) {
            if($start){
                $newArray[$i] = $element;
                $i++;
            }
            if($element == $thisDay){
                $start = true;
            }
            if($element == $end){
                $start = false;
            }
        }
        $totalElemInArray = count($newArray);

        unset($newArray[$totalElemInArray-1]);

        $cleanMenu = implode($newArray, " ");

        $cleaningMenu = ["//"];
        foreach ($cleaningMenu as $cleanse){
            $cleanMenu = mb_ereg_replace($cleanse,"", $cleanMenu);
        }

        return iconv("UTF-8", "UTF-8//IGNORE",$cleanMenu);

    }

    public function getCurlMenuLaPetitePause(){

        $url = 'http://www.lapetitepause.fr/';
        $curlResult = self::getUrlInfo($url);
        $dw = self::getIntDay();
        $thisDay = self::getDayOfTheWeek($dw);
        $end = self::getDayOfTheWeek($dw+1);

        $tabMenu = [];

        preg_match_all("/([A-Za-zàéèêœùïî\-\,]+)/", $curlResult, $menu);

        //reformatage du tableau $menu
        $j =0;
        foreach($menu[$j] as $key=>$value){
            $tabMenu[] = $value;
            $j++;
        }

        /*liste d'exclusion après le pregmatch*/
        $badWords = ["li", "ul", "span", "lpp-made-by", "class", "href", "main-nav",
            "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "aout", "septembre", "octobre", "décembre"];

        /* On parse le résultat du pregmatch avec comme critère le jour de la semaine afin de récupérer le nom du plat indiqué
        entre les deux jours*/
        $newArray = [];
        $i = 0;
        $start = false;
        foreach ($tabMenu as $element) {
            if($start == true && !in_array($element, $badWords)){
                $newArray[$i] = $element;
                $i++;
            }
            if($element == $thisDay || $element == strtolower($thisDay)){
                $start = true;
            }
            if($element == $end || $element == strtolower($end)){
                $start = false;
            }
        }
        $totalElemInArray = count($newArray);
        unset($newArray[$totalElemInArray-1]);
        return array(iconv("UTF-8", "UTF-8//IGNORE",implode($newArray, " ")), $curlResult);

    }

    public function getCurlMenuLaPetitePausePrice(){
        $curlResult = self::getCurlMenuLaPetitePause()[1];
        $dw = self::getDay();
        $menu = [];

        preg_match_all("/(?<=<h2>Notre Chef vous propose ses Plats du Jour à )(.*?)(?=<\/h2>)/", $curlResult, $menu);

        $cleanMenu = iconv("UTF-8", "UTF-8//IGNORE",$menu[0][0]);
        return($cleanMenu);
    }

    public function getCurlMenuLaCaveProfonde(){

    }

    public function caseWeekEnd(){
        $dw = self::getDay();
//        $dw = "Dimanche";

        if($dw == "Samedi" || $dw == "Dimanche"){
            return true;
        }
    }

}