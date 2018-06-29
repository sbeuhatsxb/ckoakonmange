<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TodaysSpecialController extends Controller
{
    /**
     * @Route("/todays/special", name="todays_special")
     */
    public function index()
    {
        return $this->render('todays_special/index.html.twig', [
            'controller_name' => 'TodaysSpecialController',
        ]);
    }
}
