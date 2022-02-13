<?php

namespace app\components\interfaces;

interface StopDomainsInterface
{
    /**
     * @return string[]
     */
    public function getDomains(): array;

    /**
     * @param string $domain
     * @return bool
     */
    public function matchOne(string $domain): bool;
}
