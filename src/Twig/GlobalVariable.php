<?php

namespace App\Twig;

use App\Service\Constances;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Symfony\Component\Yaml\Yaml;

class GlobalVariable extends AbstractExtension implements GlobalsInterface
{

    public function __construct(private readonly string $pathItemsMenu){}

    public function getGlobals(): array
    {
        return [
            'menu' => Yaml::parseFile($this->pathItemsMenu),
            'statusGlobals' => [
                Constances::PENDING => 'En attente',
                Constances::VALIDED => 'Valider',
                Constances::REJECTED => 'Rejeter',
                Constances::DELETED => 'Supprimer',
            ]
        ];
    }
}