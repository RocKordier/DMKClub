<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\Action;

use Oro\Bundle\DataGridBundle\Extension\Action\ActionConfiguration;
use Oro\Bundle\DataGridBundle\Extension\Action\Actions\AbstractAction;

class GetPdfAction extends AbstractAction
{
    protected $requiredOptions = ['link'];

    public function setOptions(ActionConfiguration $options): self|AbstractAction
    {
        if (empty($options['frontend_type'])) {
            // Der Wert bezieht sich auf den Key in der requirejs.yml
            $options['frontend_type'] = 'dmkgetpdf';
        }

        $options['confirmation'] = false;

        return parent::setOptions($options);
    }
}
