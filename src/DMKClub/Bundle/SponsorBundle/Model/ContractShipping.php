<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Model;

class ContractShipping
{
    public const string INTERNAL_ENUM_CODE = 'dmkclb_contrshipping';

    public const string NONE = 'none';
    public const string POSTAL = 'postal';
    public const string EMAIL = 'email';
}
