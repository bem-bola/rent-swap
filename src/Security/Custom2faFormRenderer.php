<?php
namespace App\Security;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Custom2faFormRenderer implements TwoFactorFormRendererInterface
{
    public function __construct(private Environment $twig) {}

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function renderForm(Request $request, array $templateVars): Response
    {
        return new Response(
            $this->twig->render('security/2fa_form.html.twig')
        );
    }
}
