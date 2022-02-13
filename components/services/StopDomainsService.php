<?php

namespace app\components\services;

use app\components\interfaces\StopDomainsInterface;
use yii\base\Component;

class StopDomainsService extends Component implements StopDomainsInterface
{
    /**
     * @return string[]
     */
    public function getDomains(): array
    {
        return ['stop-domain-one.com', 'stop-domain-two.com', 'stop-domain-three.com'];
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function matchOne(string $domain): bool
    {
        return in_array(strtolower($domain), $this->getDomains());
    }
}
