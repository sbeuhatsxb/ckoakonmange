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
use Symfony\Component\HttpFoundation\Request;


class TodaysSpecialController extends Controller
{
    /**
     * @Route("/todays/special", name="todays_special")
     */
    public function index(UpdateRestaurantsService $restaurantsService, Request $request)
    {
//        $this->updateMb();
//        $this->updateLeK();
//        $this->updateLPP();
//        $this->updateLH();
        $locale = $request->getLocale();
        $intl = new \IntlDateFormatter($request->getLocale(), \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, 'd LLLL y');
        $date = $intl->format(new \DateTime('now'));
        $lastUpdateToDisplay = $restaurantsService->getLastUpdateToDisplay();

        $caseWeekEnd = CurlRestaurantsService::caseWeekend();
        if(@$_POST['refresh'] == "refreshed" && $caseWeekEnd == false){
            $toRefresh = true;
            $restaurantsService->updateAllRestaurants($caseWeekEnd, $toRefresh, $date);
        } else {
            $toRefresh = false;
            $restaurantsService->updateAllRestaurants($caseWeekEnd, $toRefresh, $date);
        }

        $restaurants = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();
        ;

        $weekday = strtolower(CurlRestaurantsService::getDay());

        return $this->render('todays_special/index.html.twig', [
            'controller_name' => 'TodaysSpecialController',
            'restaurants' => $restaurants,
            'weekDay' => $weekday,
            'lastUpdateToDisplay' => $lastUpdateToDisplay,
        ]);
    }







}
