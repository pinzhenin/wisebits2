<?php

namespace app\components\validators;

use app\components\interfaces\StopDomainsInterface;
use yii\base\Model;
use yii\validators\Validator;

class EmailStopDomainsValidator extends Validator
{
    /**
     * @var StopDomainsInterface
     */
    private $stopDomains;

    /**
     * @param StopDomainsInterface $stopDomains
     * @param array $config
     */
    public function __construct(StopDomainsInterface $stopDomains, array $config = [])
    {
        parent::__construct($config);
        $this->stopDomains = $stopDomains;
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @return void
     */
    public function validateAttribute(Model $model, string $attribute): void
    {
        if ($this->stopDomains->matchOne(explode('@', $model->{$attribute}, 2)[1])) {
            $this->addError($model, $attribute, 'This domain cannot be used');
        }
    }
}
