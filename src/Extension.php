<?php

declare(strict_types=1);

namespace Bolt\BoltFormsExtraRecipients;

use Bolt\Extension\BaseExtension;

class Extension extends BaseExtension
{
    /**
     * Return the full name of the extension
     */
    public function getName(): string
    {
        return 'Bolt Forms Extra Recipients';
    }
}
