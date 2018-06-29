<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;



/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{

    /* INT */
    public $dw;

    CONST DAYWEEK = [
        1 => "Lundi",
        2 => "Mardi",
        3 => "Mercredi",
        4 => "Jeudi",
        5 => "Vendredi",
        6 => "Plats" //Exception pour la fin de semaine chez La Petite Pause
    ];

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

//    /**
//     * @return Restaurant[] Returns an array of Restaurant objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Restaurant
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

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
            var_dump($dw);
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
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Hi ! We\'re some guys around here...');
        $curlResult = curl_exec($ch);
        curl_close($ch);
        return $curlResult;
    }


    public function marcheBiot(){
        $url = 'http://sbiot.fr/accueil/plats-jour-de-semaine/';
        $curlResult = self::getUrlInfo($url);
        $dw = self::getDay();

        $pregMatch = "/(?<=" . self::getDayMinusOneDay() . "<\/div><\/div><\/li><li class='odd'><div><p class='item-text'>)(.*?)(<\/p>)/";

        if($dw == "Lundi"){
            preg_match_all("/(?<=<li class='odd'><div><p class='item-text'>)(.*?)(<\/p><p class='desc'>)/", $curlResult, $menu);
            return(array($menu[1][0], $curlResult));
        } else {
            preg_match_all($pregMatch, $curlResult, $menu);
            return(array($menu[0][0], $curlResult));
        }
    }

    public function marcheBiotVege(){
        $curlResult = self::marcheBiot()[1];
        $dw = self::getDay();

        $pregMatch = "/(?=". $dw ."<\/div><\/div><\/li><li class='even'><div><p class='item-text'>).+?(?=<\/p><p class='desc'><img src=)/";
        $trim = ":" . $dw . "</div></div></li><li class='even'><div><p class='item-text'>";

        preg_match_all($pregMatch,$curlResult, $menu);
        return(trim($menu[0][0], $trim));
    }

    public function marcheBiotPrice(){
        $curlResult = self::marcheBiot()[1];
        $dw = self::getDay();

        preg_match_all(
        "/(?<=div class='value-col value-1'>)(.*?)(?=<\/div><div class='value-col value-2'>)/",
        $curlResult, $menu);
            return($menu[0][0]);
    }

    public function lesHirondelles(){
        $url = 'https://www.leshirondelles.fr/';
        $curlResult = self::getUrlInfo($url);

        $firstPregMatch = '/(?<=<h2>Le Menu du jour<\/h2>)([^)]+)(?=a href="https:\/\/www.leshirondelles.fr)/';
        preg_match_all($firstPregMatch, $curlResult, $menu);

        $secondPregMatch = "/(?<=<p>)(.*)(?=)/";
        preg_match_all($secondPregMatch, $menu[0][0], $menu);

        return(array($menu[0][0], $curlResult));
    }

    public function leK(){
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
        return implode($newArray, " ");

    }

    public function laPetitePause(){

        $url = 'http://www.lapetitepause.fr/';
        $curlResult = self::getUrlInfo($url);
        $dw = self::getIntDay();
        $thisDay = self::getDayOfTheWeek($dw);
        $end = self::getDayOfTheWeek($dw+1);

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
        return array(implode($newArray, " "), $curlResult);

    }

    public function laPetitePausePrice(){
        $curlResult = self::laPetitePause()[1];
        $dw = self::getDay();

        preg_match_all("/(?<=<h2>Notre Chef vous propose ses Plats du Jour à )(.*?)(?=<\/h2>)/", $curlResult, $menu);
            return($menu[0][0]);
    }
}
