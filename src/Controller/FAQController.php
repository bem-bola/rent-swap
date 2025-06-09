<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/faq', name: 'app_faq_')]
final class FAQController extends AbstractController
{
    #[Route('/modifier-mon-de-passe', name: 'reset_password')]
    public function resetPassword(): Response
    {
        return $this->render('faq/reset_password.html.twig');
    }

    #[Route('/general', name: 'general')]
    public function general(): Response
    {

        $data = [
            [
                "question" => "Qu’est-ce que Reusiix ?",
                "answer" => "Reusiix est une plateforme qui permet aux utilisateurs de proposer ou rechercher des biens à louer. Elle facilite la mise en relation entre particuliers ou professionnels, sans intermédiaire, ni système de paiement intégré."
            ],
            [
                "question" => "Qui peut utiliser Reusiix ?",
                "answer" => "Toute personne majeure ou mineure avec autorisation parentale. Les entreprises ou associations peuvent également s’inscrire via un représentant légal."
            ],
            [
                "question" => "Est-ce que c’est payant ?",
                "answer" => "Non, Reusiix est entièrement gratuit. Il n’y a aucun frais de commission ou d’abonnement."
            ],
            [
                "question" => "Reusiix intervient-il dans les échanges ?",
                "answer" => "Non. Les utilisateurs sont seuls responsables de leurs échanges, réservations, et transactions. Reusiix ne garantit ni la qualité des biens ni les engagements pris."
            ],
            [
                "question" => "Que faire en cas de problème avec un autre utilisateur ?",
                "answer" => "Nous vous conseillons de tenter un règlement à l’amiable. En cas de comportement abusif ou douteux, vous pouvez signaler l’utilisateur via la plateforme ou nous écrire à : contact@reusiix.com"
            ],
            [
                "question" => "Est-ce que mes données sont sécurisées ?",
                "answer" => "Oui. Nous appliquons une politique stricte de confidentialité et ne partageons pas vos données avec des tiers. Aucun outil publicitaire ou service externe (type Google, Facebook) n’est utilisé."
            ],
            [
                "question" => "Quels types de biens puis-je proposer ?",
                "answer" => "Tout type de bien personnel ou professionnel autorisé par la loi et conforme aux CGVU : outils, véhicules, matériel événementiel, équipements divers, etc."
            ],
            [
                "question" => "Combien de temps durent les annonces ?",
                "answer" => "Il n’y a pas de durée limite fixe. Toutefois, Reusiix peut supprimer les annonces obsolètes ou les comptes inactifs depuis plus de 36 mois."
            ],
            [
                "question" => "Comment supprimer mon compte ?",
                "answer" => "Vous pouvez supprimer votre compte à tout moment dans les paramètres ou en contactant l’équipe support à l’adresse : contact@reusiix.com"
            ],
            [
                "question" => "Quels types de contenus sont interdits ?",
                "answer" => "Tout contenu illégal, discriminatoire, haineux, sexuel ou trompeur est strictement interdit. Reusiix se réserve le droit de supprimer un contenu non conforme."
            ],
            [
                "question" => "Comment protéger mon bien durant la location ?",
                "answer" => "Reusiix recommande d'établir un accord écrit ou une preuve de dépôt. Vous pouvez également prendre des photos avant/après pour éviter les litiges."
            ],
            [
                "question" => "Y a-t-il une application mobile Reusiix ?",
                "answer" => "Pas pour le moment, mais la plateforme est entièrement responsive et accessible depuis tout smartphone ou tablette."
            ],
            [
                "question" => "Puis-je modifier ou supprimer mon annonce ?",
                "answer" => "Oui, à tout moment depuis votre tableau de bord. Vous pouvez également la republier si vous souhaitez la remettre en avant."
            ],
        ];

        return $this->render('faq/general.html.twig', ['data' => $data]
        );
    }
}
