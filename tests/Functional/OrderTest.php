<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderTest extends WebTestCase
{
    public function testOrderIsSuccessfully(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.fr');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/order/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Passer une commande');

        $newAddressBtn = $crawler->selectButton('Ajouter');
        $addressForm = $newAddressBtn->form();

        $addressForm['address[fullname]'] = 'John Doe';
        $addressForm['address[phone]'] = '0123456789';
        $addressForm['address[address]'] = '11 rue something';
        $addressForm['address[zipcode]'] = '12345';
        $addressForm['address[city]'] = 'City';
        $addressForm['address[country]'] = 'Country';
        $addressForm['address[instructions]'] = '';

        $client->submit($addressForm);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('h1', 'Passer une commande');

        $orderBtn = $crawler->selectButton('Confirmer la commande');
        $orderForm = $orderBtn->form();
    }
}
