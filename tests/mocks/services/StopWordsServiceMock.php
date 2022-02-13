<?php

namespace app\tests\mocks\services;

use app\components\services\StopWordsService;

class StopWordsServiceMock extends StopWordsService
{
    /**
     * @param string $word
     * @return bool
     */
    public function matchOne(string $word): bool
    {
        return in_array(strtolower($word), ['stopwordone', 'stopwordtwo', 'stopwordthree']);
    }
}
