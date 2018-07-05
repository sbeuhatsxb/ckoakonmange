<?php

namespace App\Repository;

use App\Entity\Restaurant;
use App\Entity\LastUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Service\CurlRestaurantsService;
use App\Repository\RestaurantUpdateRepository;
use Doctrine\ORM\EntityRepository;



/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{

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

    /**
     * @param $price
     * @return Product[]
     */
    public function findOneRestaurantByName($restaurantFromTab): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('name' == $restaurantFromTab)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
        return $qb->execute();
    }

    public function findLastGlobalUpdate(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
        return $qb->execute();
    }

    public function updateAllRestaurants()
    {
        $restaurantTab = ['Le K','Les Hirondelles','La Petite Pause','Le K', "Air Bagel", "Papa Ciccio"];

        foreach($restaurantTab as $restaurantFromTab) {

            unset($restaurant);

            $restaurant = RestaurantRepository::findOneRestaurantByName($restaurantFromTab);

            if (!$restaurant) {
                throw $this->createNotFoundException(
                    'No product found for' . $restaurantFromTab
                );
            }

            $lastGlobalUpdate = $this->findLastGlobalUpdate();
            $firstDate = $lastGlobalUpdate[0]->getLastGlobalRefresh()->format('Y-m-d');
            $secondDate = new \DateTime;
            $secondDate = $secondDate->format('Y-m-d');

            if ((!($firstDate  == $secondDate)) || @$_POST['refresh'] == "refreshed") {

                $lastGlobalUpdate[0]->setLastGlobalRefresh(New \DateTime());

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
            $restaurant->setTodaySpecial(CurlRestaurantsService::marcheBiot()[0]);
            $restaurant->setVeganTodaySpecial(CurlRestaurantsService::marcheBiotVege()[0]);
            $restaurant->setPrice(CurlRestaurantsService::marcheBiotPrice());
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
            $restaurant->setTodaySpecial(CurlRestaurantsService::leK()[0]);
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
            $restaurant->setTodaySpecial(CurlRestaurantsService::laPetitePause()[0]);
            $restaurant->setPrice(CurlRestaurantsService::laPetitePausePrice());
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
            $restaurant->setTodaySpecial(CurlRestaurantsService::lesHirondelles()[0]);
            $entityManager->flush();
        } else {
            return null;
        }

    }
}
