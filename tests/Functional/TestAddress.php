<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestOrder extends WebTestCase
{
    public function testNewAddress(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/address/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Ajouter une adresse');

        // post form
        $submitButton = $crawler->selectButton('Ajouter');
        $form = $submitButton->form();

        $form['address[fullname'] = 'John Doe';
        $form['address[phone]'] = '0123456789';
        $form['address[address]'] = '11 rue something';
        $form['address[zipcode]'] = '12345';
        $form['address[city]'] = 'City';
        $form['address[Country]'] = 'Country';
        $form['address[instructions]'] = '';

        // submit
        $client->submit($form);

        // redirect
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('h1', 'Vos informations personnelles');
    }
}
