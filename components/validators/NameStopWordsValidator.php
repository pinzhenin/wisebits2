<?php

namespace app\components\validators;

use app\components\interfaces\StopWordsInterface;
use yii\base\Model;
use yii\validators\Validator;

class NameStopWordsValidator extends Validator
{
    /**
     * @var StopWordsInterface
     */
    private $stopWords;

    /**
     * @param StopWordsInterface $stopWords
     * @param array $config
     */
    public function __construct(StopWordsInterface $stopWords, array $config = [])
    {
        parent::__construct($config);
        $this->stopWords = $stopWords;
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @return void
     */
    public function validateAttribute(Model $model, string $attribute): void
    {
        if ($this->stopWords->matchOne($model->{$attribute})) {
            $this->addError($model, $attribute, 'This name cannot be used');
        }
    }
}
