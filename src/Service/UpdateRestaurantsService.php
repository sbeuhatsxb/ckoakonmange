<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 05/07/18
 * Time: 11:18
 */

namespace App\Service;
use App\Entity\LastUpdate;
use App\Repository\LastUpdateRepository;
use App\Repository\RestaurantRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Doctrine\RegistryInterface;


class UpdateRestaurantsService
{
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
    public function updateAllRestaurants($toRefresh = false)
    {
        $restaurantTab = ['Le K','Les Hirondelles','La Petite Pause','Le K', "Air Bagel", "Papa Ciccio"];

        foreach($restaurantTab as $restaurantFromTab) {

            unset($restaurant);
            $restaurant = $this->restaurantRepository->findOneByName($restaurantFromTab) ;

            if (!$restaurant) {
                throw new EntityNotFoundException('No product found for' . $restaurantFromTab);
            }

            $lastGlobalUpdate = $this->lastUpdateRepository->findAll();
            $firstDate = $lastGlobalUpdate[0]->getLastGlobalRefresh()->format('Y-m-d');
            $secondDate = new \DateTime;
            $secondDate = $secondDate->format('Y-m-d');

            if ((!($firstDate  == $secondDate)) || $toRefresh) { // TODO: $toRefresh = (@$_POST['refresh'] == "refreshed")

                $lastGlobalUpdate[0]->setLastGlobalRefresh(New \DateTime());

                switch ($restaurantFromTab){
                    case 'Le K' :
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(CurlRestaurantsService::leK());
                        break;
                    case 'Les Hirondelles':
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(CurlRestaurantsService::lesHirondelles()[0]);
                        break;
                    case 'La Petite Pause':
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(CurlRestaurantsService::laPetitePause()[0]);
                        $restaurant->setPrice(CurlRestaurantsService::laPetitePausePrice());
                        break;
                    case 'Marché Biot':
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(CurlRestaurantsService::marcheBiot()[0]);
                        $restaurant->setVeganTodaySpecial(CurlRestaurantsService::marcheBiotVege()[0]);
                        $restaurant->setPrice(CurlRestaurantsService::marcheBiotPrice());
                        break;
                    case 'Air Bagel' :
                        $restaurant->setLastUpdate(New \DateTime());
                        break;
                    case 'Papa Ciccio' :
                        $restaurant->setLastUpdate(New \DateTime());

                        break;
                }

                $this->restaurantRepository->save($restaurant);
            }
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
//            $restaurant->setTodaySpecial(CurlRestaurantsService::marcheBiot()[0]);
//            $restaurant->setVeganTodaySpecial(CurlRestaurantsService::marcheBiotVege()[0]);
//            $restaurant->setPrice(CurlRestaurantsService::marcheBiotPrice());
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
//            $restaurant->setTodaySpecial(CurlRestaurantsService::leK()[0]);
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
//            $restaurant->setTodaySpecial(CurlRestaurantsService::laPetitePause()[0]);
//            $restaurant->setPrice(CurlRestaurantsService::laPetitePausePrice());
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
//            $restaurant->setTodaySpecial(CurlRestaurantsService::lesHirondelles()[0]);
//            $entityManager->flush();
//        } else {
//            return null;
//        }
//
//    }



}