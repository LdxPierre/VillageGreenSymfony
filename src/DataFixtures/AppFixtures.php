<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $mainCat1 = new Category();
        $mainCat1->setName('Guitares & Basses');
        $mainCat1->setUrl('guitares-basses');
        $manager->persist($mainCat1);

        $firstSubCat1 = new Category();
        $firstSubCat1->setName('Guitares Electriques');
        $firstSubCat1->setUrl('guitares-electriques');
        $firstSubCat1->setParent($mainCat1);
        $manager->persist($firstSubCat1);

        $secondSubCat1 = new Category();
        $secondSubCat1->setName('Sets Guitare');
        $secondSubCat1->setUrl('sets-guitare');
        $secondSubCat1->setParent($firstSubCat1);
        $manager->persist($secondSubCat1);

        $product1 = new Product();
        $product1->setName('Harley Benton ST-20 BK Standard Serie Bundle');
        $product1->setUrl('harley-benton-st-20-bk-stanfard-serie-bundle');
        $product1->setCategory($secondSubCat1);
        $manager->persist($product1);

        $mainCat2 = new Category();
        $mainCat2->setName('Batteries & Percussions');
        $mainCat2->setUrl('batteries-percussions');
        $manager->persist($mainCat2);

        $firstSubCat2 = new Category();
        $firstSubCat2->setName('Batteries Acoustiques');
        $firstSubCat2->setUrl('batteries-acoustiques');
        $firstSubCat2->setParent($mainCat2);
        $manager->persist($firstSubCat2);

        $secondSubCat2 = new Category();
        $secondSubCat2->setName('Batteries Acoustiques Complètes');
        $secondSubCat2->setUrl('batteries-acoustiques-complete');
        $secondSubCat2->setParent($firstSubCat2);
        $manager->persist($secondSubCat2);

        $product2 = new Product();
        $product2->setName('Millenium Focus Junior Drum Set Black');
        $product2->setUrl('millenium-focus-junior-drum-set-black');
        $product2->setCategory($secondSubCat2);
        $manager->persist($product2);

        $mainCat3 = new Category();
        $mainCat3->setName('Pianos & Claviers');
        $mainCat3->setUrl('pianos-claviers');
        $manager->persist($mainCat3);

        $firstSubCat3 = new Category();
        $firstSubCat3->setName('Claviers Arrangeurs');
        $firstSubCat3->setUrl('claviers-arrangeurs');
        $firstSubCat3->setParent($mainCat3);
        $manager->persist($firstSubCat3);

        $secondSubCat3 = new Category();
        $secondSubCat3->setName('Claviers d\'initiation');
        $secondSubCat3->setUrl('claviers-initiation');
        $secondSubCat3->setParent($firstSubCat3);
        $manager->persist($secondSubCat3);

        $product3 = new Product();
        $product3->setName('Startone MK-300');
        $product3->setUrl('startone-mk-300');
        $product3->setCategory($secondSubCat3);
        $manager->persist($product3);

        $manager->flush();
    }
}
