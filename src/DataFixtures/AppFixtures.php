<?php

namespace App\DataFixtures;

use App\Entity\Owner;
use App\Entity\Region;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    // définit un nom de référence pour une instance de Region
    public const IDF_REGION_REFERENCE = 'idf-region.back';
    public const INT = "9 rue Charles Fourrier";

    public function load(ObjectManager $manager)
    {
        //...
        $owner = new Owner();
        $owner->setCountry("FR");
        $owner->setFamilyName("Martin");
        $owner->setFirstname("Jean");
        $owner->setAddress(self::INT);
        $manager->persist($owner);
        $manager->flush();

        $region = new Region();
        $region->setCountry("FR");
        $region->setName("Ile de France");
        $region->setPresentation("La région française capitale");
        $manager->persist($region);

        $manager->flush();
        // Une fois l'instance de Region sauvée en base de données,
        // elle dispose d'un identifiant généré par Doctrine, et peut
        // donc être sauvegardée comme future référence.
        $this->addReference(self::IDF_REGION_REFERENCE, $region);

        // ...

        $room = new Room();
        $room->setSummary("Beau poulailler ancien à Évry");
        $room->setDescription("très joli espace sur paille");
        //$room.back->addRegion($region.back);
        // On peut plutôt faire une référence explicite à la référence
        // enregistrée précédamment, ce qui permet d'éviter de se
        // tromper d'instance de Region :
        $room->addRegion($this->getReference(self::IDF_REGION_REFERENCE));
        $room->setOwner($owner);
        $room->setCapacity(1);
        $room->setPrice(50);
        $room->setAddress(self::INT);
        $manager->persist($room);

        $manager->flush();

        //...
    }

    //...
}
