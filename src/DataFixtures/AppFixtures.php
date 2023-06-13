<?php

namespace App\DataFixtures;

use App\Entity\CartItem;
use DateTime;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // categories

        $mainCat1 = new Category();
        $mainCat1->setName('Guitares & Basses');
        $mainCat1->setUrl('guitares-basses');
        $manager->persist($mainCat1);

        $mainCat2 = new Category();
        $mainCat2->setName('Batteries & Percussions');
        $mainCat2->setUrl('batteries-percussions');
        $manager->persist($mainCat2);

        $mainCat3 = new Category();
        $mainCat3->setName('Pianos & Claviers');
        $mainCat3->setUrl('pianos-claviers');
        $manager->persist($mainCat3);

        $sub1 = new Category();
        $sub1->setName('Guitares Electriques');
        $sub1->setUrl('guitares-electriques');
        $sub1->setParent($mainCat1);
        $manager->persist($sub1);

        $sub2 = new Category();
        $sub2->setName('Sets Guitare');
        $sub2->setUrl('sets-guitare');
        $sub2->setParent($sub1);
        $manager->persist($sub2);

        $sub3 = new Category();
        $sub3->setName('Batteries Acoustiques');
        $sub3->setUrl('batteries-acoustiques');
        $sub3->setParent($mainCat2);
        $manager->persist($sub3);

        $sub4 = new Category();
        $sub4->setName('Batteries Acoustiques Complètes');
        $sub4->setUrl('batteries-acoustiques-complete');
        $sub4->setParent($sub3);
        $manager->persist($sub4);

        $sub5 = new Category();
        $sub5->setName('Claviers Arrangeurs');
        $sub5->setUrl('claviers-arrangeurs');
        $sub5->setParent($mainCat3);
        $manager->persist($sub5);

        $sub6 = new Category();
        $sub6->setName('Claviers d\'initiation');
        $sub6->setUrl('claviers-initiation');
        $sub6->setParent($sub5);
        $manager->persist($sub6);

        // Product

        $product1 = new Product();
        $product1->setBrand('Millenium Focus');
        $product1->setName('Junior Drum Set Black');
        $product1->setUrl('millenium-focus-junior-drum-set-black');
        $product1->setPrice('179');
        $product1->setStock(200);
        $product1->setCategory($sub4);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setBrand('Harley Benton');
        $product2->setName('ST-20 BK Standard Serie Bundle');
        $product2->setUrl('harley-benton-st-20-bk-stanfard-serie-bundle');
        $product2->setPrice('129');
        $product2->setStock(10);
        $product2->setCategory($sub2);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setBrand('Squier');
        $product3->setName('Aff.Strat HSS PACK CFM');
        $product3->setUrl('squier-aff-strat-hss-pack-cfm');
        $product3->setPrice('325');
        $product3->setStock(0);
        $product3->setCategory($sub2);
        $manager->persist($product3);

        $product4 = new Product();
        $product4->setBrand('Epiphone');
        $product4->setName('Slash AFD LP Performance Pack');
        $product4->setUrl('epiphone-slash-afd-lp-performance-pack');
        $product4->setPrice('333');
        $product4->setStock(500);
        $product4->setCategory($sub2);
        $manager->persist($product4);

        $product5 = new Product();
        $product5->setBrand('Startone');
        $product5->setName('MK-300');
        $product5->setUrl('startone-mk-300');
        $product5->setPrice('135');
        $product5->setStock(1000);
        $product5->setCategory($sub6);
        $manager->persist($product5);

        $product6 = new Product();
        $product6->setBrand('Yamaha');
        $product6->setName('PSR-E473');
        $product6->setUrl('yamaha-psr-e473');
        $product6->setPrice('375');
        $product6->setStock(78);
        $product6->setCategory($sub6);
        $manager->persist($product6);

        // User

        $user = new User();
        $user->setEmail('test@test.fr')
            ->setPassword('$2y$13$duZgvLpAoBhgcnjNKhM2Zul4qww8nTLQsYwJAGuK7Pz6F0EdF2YFS')
            ->setRegisteryDate(new DateTime('now'));
        $manager->persist($user);

        // CartItems

        $cartItem = new CartItem();
        $cartItem->setUser($user)
            ->setProduct($product1)
            ->setQuantity(5);
        $manager->persist($cartItem);

        $cartItem = new CartItem();
        $cartItem->setUser($user)
            ->setProduct($product2)
            ->setQuantity(10);
        $manager->persist($cartItem);

        $manager->flush();
    }
}
