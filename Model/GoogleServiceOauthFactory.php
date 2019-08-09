<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model;

use Google_Client;
use Google_Service_Oauth2;

class GoogleServiceOauthFactory
{
    /**
     * Create a new Google_Service_Oauth2 instance
     *
     * @param Google_Client $client
     * @return Google_Service_Oauth2
     */
    public function create(Google_Client $client): Google_Service_Oauth2
    {
        return new Google_Service_Oauth2($client);
    }
}
