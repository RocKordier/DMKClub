<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\PDF\Generator;

interface GeneratorRegistryInterface
{
    public function get(string $name): GeneratorInterface;

    public function register(GeneratorInterface $generator): void;

    public function unregister(GeneratorInterface $generator): void;
}
