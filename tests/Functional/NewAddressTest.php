<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestAddress extends WebTestCase
{
    public function testFormIsValid(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/address/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Ajouter une adresse');

        // post form
        $submitButton = $crawler->selectButton('Ajouter');
        $form = $submitButton->form();

        $form['address[fullname]'] = 'John Doe';
        $form['address[phone]'] = '0123456789';
        $form['address[address]'] = '11 rue something';
        $form['address[zipcode]'] = '12345';
        $form['address[city]'] = 'City';
        $form['address[country]'] = 'Country';
        $form['address[instructions]'] = '';

        // submit
        $client->submit($form);

        // redirect
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('h1', 'Vos informations personnelles');
    }

    function testFullnameIsInvalid(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/address/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Ajouter une adresse');

        $submitBtn = $crawler->selectButton('Ajouter');
        $form = $submitBtn->form();

        $form['address[fullname]'] = 'John1234';
        $form['address[phone]'] = '01234567890';
        $form['address[address]'] = '11 rue something';
        $form['address[zipcode]'] = '12345';
        $form['address[city]'] = 'City';
        $form['address[country]'] = 'Country';
        $form['address[instructions]'] = '';

        // submit
        $client->submit($form);

        // redirect
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('div.invalid-feedback.d-block', 'Votre nom complet ne peut pas contenir de caractères spéciaux ou chiffres');
    }
}
