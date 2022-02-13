<?php

namespace app\tests\mocks\services;

use app\components\services\StopDomainsService;

class StopDomainsServiceMock extends StopDomainsService
{
    /**
     * @param string $domain
     * @return bool
     */
    public function matchOne(string $domain): bool
    {
        return in_array(strtolower($domain), ['stop-domain-one.com', 'stop-domain-two.com', 'stop-domain-three.com']);
    }
}
