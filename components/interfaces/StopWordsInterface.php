<?php

namespace app\components\interfaces;

interface StopWordsInterface
{
    /**
     * @return string[]
     */
    public function getWords(): array;

    /**
     * @param string $word
     * @return bool
     */
    public function matchOne(string $word): bool;
}
