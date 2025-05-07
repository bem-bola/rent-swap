<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Symfony\Component\Yaml\Yaml;

class AppItemsMenu extends AbstractExtension implements GlobalsInterface
{

    public function __construct(private readonly string $pathItemsMenu){}

    public function getGlobals(): array
    {
        return [
            'menu' => Yaml::parseFile($this->pathItemsMenu)
        ];
    }
}