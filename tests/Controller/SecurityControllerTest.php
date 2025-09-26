<?php

namespace App\Tests\Controller;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    public function testAccesRegistration(): void
    {
        $client = static::createClient();

        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    public function testRegistrationSuccess(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form();

        $form['registration_form[email]'] = "testeur01@gmail.com";
        $form['registration_form[plainPassword][first]'] = "M0tdePasseSup3rFor!";
        $form['registration_form[plainPassword][second]'] = "M0tdePasseSup3rFor!";
        $form['registration_form[firstName]'] = "Testeur";
        $form['registration_form[lastName]'] = "Unitaire";
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        $this->assertResponseRedirects('');
    }

    public function testRegistrationWithWeakPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form();

        $form['registration_form[email]'] = "testeur01@gmail.com";
        $form['registration_form[plainPassword][first]'] = "Id3456!";
        $form['registration_form[plainPassword][second]'] = "Id3456!";
        $form['registration_form[firstName]'] = "Testeur";
        $form['registration_form[lastName]'] = "Fonctionnel";
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        $this->assertAnySelectorTextContains("li", "Votre mot de passe doit contenir au moins");
    }
}
