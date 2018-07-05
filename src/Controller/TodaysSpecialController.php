<?php

namespace App\Controller;

use App\Service\UpdateRestaurantsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\RestaurantRepository;
use App\Entity\Restaurant;
use App\Entity\LastUpdate;
use App\Service\CurlRestaurantsService;
use App\Controller\UpdateRestaurantController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class TodaysSpecialController extends Controller
{
    /**
     * @Route("/todays/special", name="todays_special")
     */
    public function index(UpdateRestaurantsService $restaurantsService)
    {
//        $this->updateMb();
//        $this->updateLeK();
//        $this->updateLPP();
//        $this->updateLH();
        if(@$_POST['refresh'] == "refreshed"){
            $toRefresh = true;
            $restaurantsService->updateAllRestaurants($toRefresh);
        } else {
            $restaurantsService->updateAllRestaurants();
        }

        $restaurants = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();
        ;

        $weekday = strtolower(CurlRestaurantsService::getDay());

        $entityManager = $this->getDoctrine()->getManager();
        $lastGlobalUpdate = $entityManager->getRepository(LastUpdate::class)->findAll();
        $lastGlobalUpdate = $lastGlobalUpdate[0]->getLastGlobalRefresh()->format('H:i');


        return $this->render('todays_special/index.html.twig', [
            'controller_name' => 'TodaysSpecialController',
            'restaurants' => $restaurants,
            'weekDay' => $weekday,
            'lastGlobalUpdate' => $lastGlobalUpdate,
        ]);
    }







}
