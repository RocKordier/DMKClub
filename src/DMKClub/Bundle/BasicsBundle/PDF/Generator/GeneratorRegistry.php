<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\PDF\Generator;

final class GeneratorRegistry implements GeneratorRegistryInterface
{
    private array $generators = [];

    public function __construct(iterable $generators)
    {
        foreach ($generators as $generator) {
            $this->register($generator);
        }
    }

    #[\Override]
    public function get(string $name): GeneratorInterface
    {
        if (false === \array_key_exists($name, $this->generators)) {
            throw new GeneratorNotFoundException(sprintf('Generator %s was not registered before.', $name));
        }

        return $this->generators[$name];
    }

    #[\Override]
    public function register(GeneratorInterface $generator): void
    {
        $name = $generator->getName();

        if (\array_key_exists($name, $this->generators)) {
            throw new \LogicException(sprintf('%s already taken as identifier', $name));
        }

        $this->generators[$name] = $generator;
    }

    #[\Override]
    public function unregister(GeneratorInterface $generator): void
    {
        if (false === \array_key_exists($generator->getName(), $this->generators)) {
            throw new \LogicException(sprintf('Handler %s was not registered before.', $generator->getName()));
        }

        unset($this->generators[$generator->getName()]);
    }
}
