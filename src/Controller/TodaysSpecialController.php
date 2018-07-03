<?php

namespace App\Controller;

use App\Repository\RestaurantUpdateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\RestaurantRepository;
use App\Entity\Restaurant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class TodaysSpecialController extends Controller
{
    /**
     * @Route("/todays/special", name="todays_special")
     */
    public function index()
    {
//        $this->updateMb();
//        $this->updateLeK();
//        $this->updateLPP();
//        $this->updateLH();
        $this->updateAllRestaurants();

        $restaurants = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();
        ;
        $weekday = strtolower(RestaurantRepository::getDay());

        return $this->render('todays_special/index.html.twig', [
            'controller_name' => 'TodaysSpecialController',
            'restaurants' => $restaurants,
            'weekDay' => $weekday,
        ]);
    }

    public function updateAllRestaurants()
    {
        $restaurantTab = ['Le K','Les Hirondelles','La Petite Pause','Le K', "Air Bagel", "Papa Ciccio"];

        foreach($restaurantTab as $restaurantFromTab) {

            unset($restaurant);
            $entityManager = $this->getDoctrine()->getManager();
            $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => $restaurantFromTab]);;

            if (!$restaurant) {
                throw $this->createNotFoundException(
                    'No product found for' . $restaurantFromTab
                );
            }

            $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
            $secondDate = new \DateTime;
            $secondDate = $secondDate->format('Y-m-d');

            if ((!($firstDate == $secondDate)) || @$_POST['refresh']) {
                switch ($restaurantFromTab){
                    case 'Le K' :
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(RestaurantRepository::leK());
                        $entityManager->flush();
                        break;
                    case 'Les Hirondelles':
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(RestaurantRepository::lesHirondelles()[0]);
                        $entityManager->flush();
                        break;
                    case 'La Petite Pause':
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(RestaurantRepository::laPetitePause()[0]);
                        $restaurant->setPrice(RestaurantRepository::laPetitePausePrice());
                        $entityManager->flush();
                        break;
                    case 'Marché Biot':
                        $restaurant->setLastUpdate(New \DateTime());
                        $restaurant->setTodaySpecial(RestaurantRepository::marcheBiot()[0]);
                        $restaurant->setVeganTodaySpecial(RestaurantRepository::marcheBiotVege()[0]);
                        $restaurant->setPrice(RestaurantRepository::marcheBiotPrice());
                        $entityManager->flush();
                        break;
                    case 'Air Bagel' :
                        $restaurant->setLastUpdate(New \DateTime());
                        $entityManager->flush();
                        break;
                    case 'Papa Ciccio' :
                        $restaurant->setLastUpdate(New \DateTime());
                        $entityManager->flush();
                        break;
                }


            } else {
                return null;
            }
        }
    }

    public function updateMb()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'Marché Biot']);;

        if (!$restaurant) {
            throw $this->createNotFoundException(
                'No product found for "Marché Biot" '
            );
        }

        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
        $secondDate = new \DateTime;
        $secondDate = $secondDate->format('Y-m-d');

        if (!($firstDate == $secondDate)){
            $restaurant->setLastUpdate(new \DateTime());
            $restaurant->setTodaySpecial(RestaurantRepository::marcheBiot()[0]);
            $restaurant->setVeganTodaySpecial(RestaurantRepository::marcheBiotVege()[0]);
            $restaurant->setPrice(RestaurantRepository::marcheBiotPrice());
            $entityManager->flush();
        } else {
            return null;
        }

    }

    public function updateLeK()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'Le K']);;

        if (!$restaurant) {
            throw $this->createNotFoundException(
                'No product found for "Le K" '
            );
        }

        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
        $secondDate = new \DateTime;
        $secondDate = $secondDate->format('Y-m-d');
        if (!($firstDate == $secondDate)){
            $restaurant->setLastUpdate(new \DateTime());
            $restaurant->setTodaySpecial(RestaurantRepository::leK()[0]);
            $entityManager->flush();
        } else {
            return null;
        }

    }

    public function updateLPP()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'La Petite Pause']);;

        if (!$restaurant) {
            throw $this->createNotFoundException(
                'No product found for "La Petite Pause" '
            );
        }

        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
        $secondDate = new \DateTime;
        $secondDate = $secondDate->format('Y-m-d');
        if (!($firstDate == $secondDate)){
            $restaurant->setLastUpdate(new \DateTime());
            $restaurant->setTodaySpecial(RestaurantRepository::laPetitePause()[0]);
            $restaurant->setPrice(RestaurantRepository::laPetitePausePrice());
            $entityManager->flush();
        } else {
            return null;
        }

    }

    public function updateLH()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => 'Les Hirondelles']);;

        if (!$restaurant) {
            throw $this->createNotFoundException(
                'No product found for "Les Hirondelles" '
            );
        }

        $firstDate = $restaurant->getLastUpdate()->format('Y-m-d');
        $secondDate = new \DateTime;
        $secondDate = $secondDate->format('Y-m-d');
        if (!($firstDate == $secondDate)){
            $restaurant->setLastUpdate(new \DateTime());
            $restaurant->setTodaySpecial(RestaurantRepository::lesHirondelles()[0]);
            $entityManager->flush();
        } else {
            return null;
        }

    }



}
