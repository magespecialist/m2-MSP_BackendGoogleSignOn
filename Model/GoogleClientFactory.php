<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model;

use Google_Client;

class GoogleClientFactory
{
    /**
     * Create a new Google Client instance
     *
     * @return Google_Client
     */
    public function create(): Google_Client
    {
        return new Google_Client(['approval_prompt' => 'force']);
    }
}
