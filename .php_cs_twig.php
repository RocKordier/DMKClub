<?php

declare(strict_types=1);

use Oro\Bundle\NavigationBundle\Twig\TitleSetTokenParser;
use Oro\Bundle\UIBundle\Twig\Parser\PlaceholderTokenParser;
use TwigCsFixer\Config\Config;

$config = new Config();
$config->addTokenParser(new TitleSetTokenParser());
$config->addTokenParser(new PlaceholderTokenParser());

return $config;
