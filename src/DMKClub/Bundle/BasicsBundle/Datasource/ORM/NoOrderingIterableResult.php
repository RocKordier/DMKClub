<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Datasource\ORM;

use Oro\Bundle\BatchBundle\ORM\Query\ResultIterator\IdentifierWithoutOrderByIterationStrategy;
use Oro\Bundle\BatchBundle\ORM\Query\ResultIterator\IdentityIterationStrategyInterface;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResult;

/**
 * Ermöglicht die Iteration über komplexe Datagrids. Dabei wird aber auf die Sortierung verzichtet.
 */
class NoOrderingIterableResult extends IterableResult
{
    protected function getIterationStrategy(): IdentityIterationStrategyInterface
    {
        if (null === $this->iterationStrategy) {
            $this->iterationStrategy = new IdentifierWithoutOrderByIterationStrategy();
        }

        return $this->iterationStrategy;
    }
}
