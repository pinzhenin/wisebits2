<?php

namespace app\components\services;

use app\components\interfaces\StopWordsInterface;
use yii\base\Component;

class StopWordsService extends Component implements StopWordsInterface
{
    /**
     * @return string[]
     */
    public function getWords(): array
    {
        return ['stopwordone', 'stopwordtwo', 'stopwordthree'];
    }

    /**
     * @param string $word
     * @return bool
     */
    public function matchOne(string $word): bool
    {
        return in_array(strtolower($word), $this->getWords());
    }
}
