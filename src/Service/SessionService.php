<?php

// src/Service/NominatimService.php
namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class SessionService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
    )
    {}

    /**
     * @param string $route
     * @param string|null $flashType
     * @param string|null $flashMessage
     * @param array $params
     * @return RedirectResponse
     */
    public function redirectWithFlash(string $route, ?string $flashType = null, ?string $flashMessage = null, array $params = []): RedirectResponse
    {
        if($flashType !== null && $flashMessage !== null){
            // Ajouter un message flash
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add($flashType, $flashMessage);
        }

        // Générer l'URL
        $url = $this->router->generate($route, $params);

        // Retourner une redirection
        return new RedirectResponse($url);
    }
}


