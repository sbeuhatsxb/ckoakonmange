<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Repository\RestaurantUpdateRepository;



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

    /**
     * @param string $name
     * @return Restaurant
     */
    public function findOneByName(string $name)
    {
        return $this->findOneBy(['name'=>$name]);
    }

    public function save(Restaurant $restaurant, $andFlush = true)
    {
        $this->getEntityManager()->persist($restaurant);
        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }
}
