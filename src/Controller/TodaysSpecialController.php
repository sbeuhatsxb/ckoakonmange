<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\RestaurantRepository;
use App\Entity\Restaurant;
use App\Entity\LastUpdate;
use App\Service\CurlRestaurantsService;
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
        RestaurantRepository::updateAllRestaurants();

        $restaurants = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();
        ;

        $weekday = strtolower(CurlRestaurantsService::getDay());

        $lastGlobalUpdate = $this->getDoctrine()
            ->getRepository(LastUpdate::class)
            ->findAll();
        ;

        $entityManager = $this->getDoctrine()->getManager();
        $lastGlobalUpdate = $entityManager->getRepository(LastUpdate::class)->findAll();
        $lastGlobalUpdate = $lastGlobalUpdate[0]->getLastGlobalRefresh()->format('H:i');

        dump($lastGlobalUpdate);

        return $this->render('todays_special/index.html.twig', [
            'controller_name' => 'TodaysSpecialController',
            'restaurants' => $restaurants,
            'weekDay' => $weekday,
            'lastGlobalUpdate' => $lastGlobalUpdate,
        ]);
    }




}
