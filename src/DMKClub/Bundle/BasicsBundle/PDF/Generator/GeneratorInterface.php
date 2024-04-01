<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\PDF\Generator;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

interface GeneratorInterface
{
    public function getLabel(): string;

    public function getName(): string;

    public function execute(TwigTemplate $twigTemplate, $filename, array $context = []): void;
}
