<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        $restaurant = new Restaurant();
        $restaurant->setName('Marché Biot');
        $restaurant->setTodaySpecial(RestaurantRepository::marcheBiot()[0]);
        $restaurant->setVeganTodaySpecial(RestaurantRepository::marcheBiotVege()[0]);
        $restaurant->setPrice(RestaurantRepository::marcheBiotPrice());
        $restaurant->setLastUpdate(new \DateTime());
        $restaurant->setUrl('http://sbiot.fr/accueil/plats-jour-de-semaine/');
        $restaurant->setMappy('https://www.google.fr/maps/dir/48.6144167,7.7087412/March%C3%A9+Frais+de+St%C3%A9phane+Biot,+51+Rue+de+Strasbourg,+67206+Mittelhausbergen/@48.6120922,7.7009841,17z/data=!3m1!4b1!4m10!4m9!1m1!4e1!1m5!1m1!1s0x4796b7e8b96b27a5:0x5629eff44b35cd22!2m2!1d7.6987196!2d48.6097677!3e3!5m1!1e1');
        $manager->persist($restaurant);

        $restaurant = new Restaurant();
        $restaurant->setName('Le K');
        $restaurant->setTodaySpecial(RestaurantRepository::leK());
        $restaurant->setPrice('12,80 €');
        $restaurant->setLastUpdate(new \DateTime());
        $restaurant->setUrl('https://www.restaurant-le-k.com/a-table/');
        $restaurant->setMappy('https://www.google.fr/maps/dir/48.6144167,7.7087412/Le+K,+2+Avenue+de+l\'Europe,+67300+Schiltigheim/@48.6128001,7.709134,17z/data=!3m1!4b1!4m17!1m6!3m5!1s0x4796b7e35e6cb0bd:0xacbe350ad43d37cb!2sLe+K!8m2!3d48.6119471!4d7.7135983!4m9!1m1!4e1!1m5!1m1!1s0x4796b7e35e6cb0bd:0xacbe350ad43d37cb!2m2!1d7.7135983!2d48.6119471!3e3!5m1!1e1');
        $manager->persist($restaurant);

        $restaurant = new Restaurant();
        $restaurant->setName('La Petite Pause');
        $restaurant->setTodaySpecial(RestaurantRepository::laPetitePause()[0]);
        $restaurant->setPrice(RestaurantRepository::laPetitePausePrice());
        $restaurant->setLastUpdate(new \DateTime());
        $restaurant->setUrl('http://www.lapetitepause.fr/');
        $restaurant->setMappy('https://www.google.fr/maps/dir/48.6144167,7.7087412/La+Petite+Pause+Schiltigheim,+Rue+Alfred+Kastler,+Schiltigheim/@48.6124495,7.7124531,17z/data=!3m1!4b1!4m10!4m9!1m1!4e1!1m5!1m1!1s0x4796c81d207fde15:0x47ddda0880f8dd3!2m2!1d7.720553!2d48.610507!3e3!5m1!1e1');
        $manager->persist($restaurant);

        $restaurant = new Restaurant();
        $restaurant->setName('Les Hirondelles');
        $restaurant->setTodaySpecial(RestaurantRepository::lesHirondelles()[0]);
        $restaurant->setPrice("7,40€");
        $restaurant->setLastUpdate(new \DateTime());
        $restaurant->setUrl('https://www.leshirondelles.fr/');
        $manager->persist($restaurant);

        $restaurant = new Restaurant();
        $restaurant->setName('Papa Ciccio');
        $restaurant->setTodaySpecial("
                      <span>Les pâtes :</span><p>Farfale, Fusilli, Penne, Spaghetti</p><br/><br/>
                      <span>Les sauces :</span>
                      <p>3 fromages<br/>
                      Arrabiatta<br/>
                      Aurora<br/>
                      Bolognaise<br/>
                      Carbonara Thon / Boeuf / Lardons / Pesto Verde<br/>
                      Champignon crème<br/>
                      Olive (sauce tomate)<br/>
                      Pesto Verde / Rosso<br/>
                      Piccantina<br/>
                      Poulet Curry / Oignon / Poivre / Moutarde<br/>
                      Poulet Méditerranéene (don't ask) / Champignons noirs<br/>
                      Saumon crème</p><br/><br/>
                      <span>Les fromages :</span><p> emmental, parmigiano</p><br/><br/>

                      03 88 83 20 08
                  </p>
                  ");
        $restaurant->setPrice("6,50€");
        $restaurant->setLastUpdate(new \DateTime());
        $restaurant->setUrl('https://www.facebook.com/Papaciccio2007/');
        $restaurant->setMappy('https://www.google.fr/maps/dir/48.6144167,7.7087412/Papa+Ciccio,+Route+du+G%C3%A9n%C3%A9ral+de+Gaulle,+Schiltigheim/@48.6095721,7.7147292,15z/data=!3m1!4b1!4m10!4m9!1m1!4e1!1m5!1m1!1s0x4796c814f00a4191:0x8c4b5b01a1ce1582!2m2!1d7.7378317!2d48.603201!3e3!5m1!1e1');
        $manager->persist($restaurant);

        $restaurant = new Restaurant();
        $restaurant->setName('Air Bagel');
        $restaurant->setTodaySpecial("
                Salade ou Bagel. <br/><br/>
                Plus de détails sur le site...
                ");
        $restaurant->setPrice("de 6,70€ à 10,50€");
        $restaurant->setLastUpdate(new \DateTime());
        $restaurant->setUrl('https://airbagels-schiltigheim.miam-express.com/');
        $restaurant->setMappy('https://www.google.fr/maps/dir/48.6144167,7.7087412/Air+Bagels,+2+Rue+de+Dublin,+67300+Schiltigheim/@48.6137486,7.7076986,17z/data=!3m1!4b1!4m10!4m9!1m1!4e1!1m5!1m1!1s0x4796b7fb3e83d977:0xa6c87baeb69c99a9!2m2!1d7.7100167!2d48.6130662!3e3!5m1!1e1');
        $manager->persist($restaurant);

        $manager->flush();
    }
}