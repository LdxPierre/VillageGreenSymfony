<?php

namespace App\Tests\Unit;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    function getEntity(): Address
    {
        $address = new Address();
        $address->setFullname('Nom Complet')
            ->setUser(new User())
            ->setPhone('0123456789')
            ->setAddress('17 adresse de test')
            ->setZipcode('12345')
            ->setCity('Ville de test')
            ->setInstructions('')
            ->setCountry('Pays');

        return $address;
    }

    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $address = $this->getEntity();

        $errors = $container->get('validator')->validate($address);

        $this->assertCount(0, $errors);
    }

    function testInvalidName(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $address = $this->getEntity()->setFullname('');

        $errors = $container->get('validator')->validate($address);

        $this->assertCount(2, $errors);
    }

    function testInvalidAddress(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $address = $this->getEntity()->setAddress('qwerty 123465');

        $errors = $container->get('validator')->validate($address);

        $this->assertCount(1, $errors);
    }
}
