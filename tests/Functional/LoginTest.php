<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    /**
     * Teste que la connexion fonctionne avec des identifiants valides.
     */
    public function testLoginIsSuccessful(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        // Accès au formulaire de login
        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));

        // Remplissage du formulaire avec identifiants valides
        $form = $crawler->filter('form[name=login]')->form([
            '_username' => 'test@test.com',
            '_password' => 'password123!',
        ]);

        // Soumission du formulaire
        $client->submit($form);

        // Vérifie que l'utilisateur est redirigé (302/Found)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Suit la redirection après login
        $client->followRedirect();

        // Vérifie que l'on atterrit sur la page d'accueil
        $this->assertRouteSame('app_home');
    }

    /**
     * Teste que la connexion échoue si le mot de passe est incorrect.
     */
    public function testLoginFailsWithWrongPassword(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        // Accès au formulaire de login
        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));

        // Remplissage du formulaire avec mot de passe incorrect
        $form = $crawler->filter('form[name=login]')->form([
            '_username' => 'test@test.com',
            '_password' => 'wrongpassword',
        ]);

        // Soumission du formulaire
        $client->submit($form);

        // Redirection attendue vers la même page (échec de login)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        // Vérifie que l'on reste sur la page de login
        $this->assertRouteSame('app_login');

        // Vérifie la présence du message d'erreur
        $this->assertSelectorTextContains('div.text-danger', 'Identifiants invalides.');
    }
}
