<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 05/07/18
 * Time: 11:18
 */

namespace App\Service;
use App\Entity\LastUpdate;
use App\Entity\Restaurant;
use App\Repository\LastUpdateRepository;
use App\Repository\RestaurantRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Doctrine\RegistryInterface;


class UpdateRestaurantsService
{
    CONST DAYS = [
        1 => "Lundi",
        2 => "Mardi",
        3 => "Mercredi",
        4 => "Jeudi",
        5 => "Vendredi",
        6 => "Samedi",
        7 => "Dimanche"
    ];

    /**
     * @var RestaurantRepository
     */
    protected $restaurantRepository;

    /**
     * @var LastUpdateRepository
     */
    protected $lastUpdateRepository;

    public function __construct(RestaurantRepository $restaurantRepository, LastUpdateRepository $lastUpdateRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->lastUpdateRepository = $lastUpdateRepository;
    }

    /**
     * @return void
     * @throws EntityNotFoundException if some restaurant were not found
     */
    public function updateAllRestaurants($caseWeekEnd, $toRefresh = false)
    {
        //Important : these names and methods in CurlRestaurantsService must be the same (and without spaces!)
        $restaurantTab = [
            'Le K',
            'Les Hirondelles',
            'La Petite Pause',
            'Marché Biot',
            'Air Bagel',
            'Papa Ciccio',
            'La Cave Profonde'
        ];

        foreach($restaurantTab as $restaurantFromTab) {

            if(!$caseWeekEnd){
                unset($restaurant);
                $restaurant = $this->restaurantRepository->findOneByName($restaurantFromTab);
                if (!$restaurant) {
                    $restaurant = new Restaurant();
                    $restaurantFromTab = str_replace(' ','', $restaurantFromTab);
                    $functionName = "getCurlMenu" . $restaurantFromTab;

                    //            $name, [0]
                    //            $cleanMenu), // [1]
                    //            $url, // [2]
                    //            $mappy, // [3]
                    //            $price, // [4]
                    $restaurant->setName(CurlRestaurantsService::$functionName()[0]);
                    $restaurant->setTodaySpecial(CurlRestaurantsService::$functionName()[1]);
                    $restaurant->setUrl(CurlRestaurantsService::$functionName()[2]);
                    $restaurant->setMappy(CurlRestaurantsService::$functionName()[3]);
                    $restaurant->setPrice(CurlRestaurantsService::$functionName()[4]);
                    $restaurant->setLastUpdate(New \DateTime());

                    $this->restaurantRepository->save($restaurant);
                }

                $lastGlobalUpdate = $this->lastUpdateRepository->findAll();
                $firstDate = $lastGlobalUpdate[0]->getLastGlobalRefresh()->format('Y-m-d');
                $secondDate = new \DateTime;
                $secondDate = $secondDate->format('Y-m-d');

                if ((!($firstDate  == $secondDate)) || $toRefresh) {
                    $lastGlobalUpdate[0]->setLastGlobalRefresh(New \DateTime());

                    switch ($restaurantFromTab) {
                        case 'Le K' :
                            $restaurant->setLastUpdate(New \DateTime());
                            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuLeK());
                            break;
                        case 'Les Hirondelles':
                            $restaurant->setLastUpdate(New \DateTime());
                            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuLesHirondelles()[0]);
                            break;
                        case 'La PetitePause':
                            $restaurant->setLastUpdate(New \DateTime());
                            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuLaPetitePause()[0]);
                            $restaurant->setPrice(CurlRestaurantsService::getCurlMenuLaPetitePausePrice());
                            break;
                        case 'Marché Biot':
                            $restaurant->setLastUpdate(New \DateTime());
                            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuMarcheBiot()[0]);
                            $restaurant->setVeganTodaySpecial(CurlRestaurantsService::getCurlMenuMarcheBiotVege());
                            $restaurant->setPrice(CurlRestaurantsService::getCurlMarcheBiotPrice());
                            break;
                        case 'Air Bagel' :
                            $restaurant->setTodaySpecial("
                                Salade ou Bagel.
                                Plus de détails sur le site...
                                ");
                            $restaurant->setLastUpdate(New \DateTime());
                            break;
                        case 'Papa Ciccio' :
                            $restaurant->setLastUpdate(New \DateTime());
                            break;
                        case 'La Cave Profonde':
                            $restaurant->setLastUpdate(New \DateTime());
                            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuLaCaveProfonde()[1]);
                            break;
                    }
                    $this->restaurantRepository->save($restaurant);
                }
            } else {
                $lastGlobalUpdate = $this->lastUpdateRepository->findAll();
                $lastGlobalUpdate[0]->setLastGlobalRefresh(New \DateTime());

                $restaurant = $this->restaurantRepository->findOneByName($restaurantFromTab);
                $restaurant->setTodaySpecial("Pas de plat du jour le week-end ! Revenez nous voir lundi !");
                $this->restaurantRepository->save($restaurant);
            }
        }
    }

    /**
     * @return LastUpdateRepository
     */
    public function getLastUpdateToDisplay()
    {
        $lastGlobalUpdate = $this->lastUpdateRepository->findAll();


        $lastUpdate = $lastGlobalUpdate[0]->getLastGlobalRefresh();
        $now = new \DateTime;
        $nowCalendar = $now->format('Y-m-d');

        $interval = $lastUpdate->diff($now);

//        //Returns "Aujourd'hui"
        if ($lastUpdate->format('Y-m-d') == $nowCalendar) {
            return "aujourd'hui à " . $lastUpdate->format('H:i');;
        }

        //Returns "Hier"
        if ($interval->format('%a') == 1){
            return "Hier";
        }

        //Returns "$day dernier"
        if ($interval->format('%a') <= 7){
            $dayInLetters = $lastUpdate->format('N');
            return self::DAYS[$dayInLetters] . " dernier";
        }

        //Returns "la semaine dernière"
        if ($interval->format('%a') > 7){
            return "Il y a plus d'une smeaine";
        }


    }

//    Methods to update one restaurant :

//    public function updateMb()
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'Marché Biot']);;
//
//        if (!$restaurant) {
//            throw $this->createNotFoundException(
//                'No product found for "Marché Biot" '
//            );
//        }
//
//        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
//        $secondDate = new \DateTime;
//        $secondDate = $secondDate->format('Y-m-d');
//
//        if (!($firstDate == $secondDate)){
//            $restaurant->setLastUpdate(new \DateTime());
//            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuMarcheBiot()[0]);
//            $restaurant->setVeganTodaySpecial(CurlRestaurantsService::getCurlMenuMarcheBiotVege()[0]);
//            $restaurant->setPrice(CurlRestaurantsService::getCurlMarcheBiotPrice());
//            $entityManager->flush();
//        } else {
//            return null;
//        }
//
//    }
//
//    public function updateLeK()
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'Le K']);;
//
//        if (!$restaurant) {
//            throw $this->createNotFoundException(
//                'No product found for "Le K" '
//            );
//        }
//
//        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
//        $secondDate = new \DateTime;
//        $secondDate = $secondDate->format('Y-m-d');
//        if (!($firstDate == $secondDate)){
//            $restaurant->setLastUpdate(new \DateTime());
//            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuLeK()[0]);
//            $entityManager->flush();
//        } else {
//            return null;
//        }
//
//    }
//
//    public function updateLPP()
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'La Petite Pause']);;
//
//        if (!$restaurant) {
//            throw $this->createNotFoundException(
//                'No product found for "La Petite Pause" '
//            );
//        }
//
//        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
//        $secondDate = new \DateTime;
//        $secondDate = $secondDate->format('Y-m-d');
//        if (!($firstDate == $secondDate)){
//            $restaurant->setLastUpdate(new \DateTime());
//            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuLaPetitePause()[0]);
//            $restaurant->setPrice(CurlRestaurantsService::getCurlMenuLaPetitePausePrice());
//            $entityManager->flush();
//        } else {
//            return null;
//        }
//
//    }
//
//    public function updateLH()
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'Les Hirondelles']);;
//
//        if (!$restaurant) {
//            throw $this->createNotFoundException(
//                'No product found for "Les Hirondelles" '
//            );
//        }
//
//        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
//        $secondDate = new \DateTime;
//        $secondDate = $secondDate->format('Y-m-d');
//        if (!($firstDate == $secondDate)){
//            $restaurant->setLastUpdate(new \DateTime());
//            $restaurant->setTodaySpecial(CurlRestaurantsService::getCurlMenuLesHirondelles()[0]);
//            $entityManager->flush();
//        } else {
//            return null;
//        }
//
//    }



}