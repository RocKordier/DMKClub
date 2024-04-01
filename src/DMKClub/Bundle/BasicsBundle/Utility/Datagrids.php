<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Utility;

use Oro\Bundle\DataGridBundle\Datasource\ResultRecordInterface;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyInterface;

readonly class Datagrids
{
    public function cent2float(string $gridName, string $keyName, array $node): \Closure
    {
        $dataName = $node[PropertyInterface::DATA_NAME_KEY];

        return function (ResultRecordInterface $record) use ($dataName) {
            $result = $record->getValue($dataName);
            $result /= 100;

            return number_format($result, 2);
        };
    }
}
