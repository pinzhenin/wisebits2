<?php

namespace app\models;

use app\components\validators\EmailStopDomainsValidator;
use app\components\validators\NameStopWordsValidator;
use Yii;
use yii\base\Event;
use yii\behaviors\OptimisticLockBehavior;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "Users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $created
 * @property string|null $deleted
 * @property string|null $notes
 * @property int $version
 */
class Users extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'Users';
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        Event::on(self::class, self::EVENT_AFTER_UPDATE, function (AfterSaveEvent $event) {
            Yii::info(json_encode([
                'id' => $event->sender->id,
                'oldAttributes' => $event->changedAttributes,
                'newAttributes' => array_intersect_key($event->sender->getAttributes(), $event->changedAttributes)
            ]));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [OptimisticLockBehavior::class];
    }

    /**
     * {@inheritdoc}
     */
    public function optimisticLock(): string
    {
        return 'version';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['name', 'required', 'message' => 'Name required'],
            ['name', 'trim'],
            ['name', 'string', 'length' => [8, 64], 'message' => 'The name must be between 8 and 64 characters long'],
            ['name', 'match', 'pattern' => '/^[a-z0-9]+$/', 'message' => 'The name must contain only the characters a-z, 0-9'],
            ['name', NameStopWordsValidator::class],
            ['name', 'unique', 'message' => 'This name is already in use'],
            ['email', 'required', 'message' => 'Email required'],
            ['email', 'trim'],
            ['email', 'email', 'message' => 'This email is incorrect'],
            ['email', EmailStopDomainsValidator::class],
            ['email', 'unique', 'message' => 'This email is already in use'],
            ['created', 'default', 'value' => date('Y-m-d H:i:s')],
            ['created', 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],
            ['deleted', 'default', 'value' => null],
            ['deleted', 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss', 'skipOnEmpty' => true],
            ['deleted', 'compare', 'compareAttribute' => 'created', 'operator' => '>=', 'skipOnEmpty' => true],
            ['notes', 'default', 'value' => null],
            ['notes', 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'User id',
            'name' => 'Name',
            'email' => 'Email',
            'created' => 'Created datetime',
            'deleted' => 'Deleted datetime',
            'notes' => 'Notes',
        ];
    }

    /**
     * @return bool
     */
    public function softDelete(): bool
    {
        $this->deleted = date('Y-m-d H:i:s');
        return $this->save(true, ['deleted']);
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null): bool
    {
        try {
            return parent::save($runValidation, $attributeNames);
        } catch (StaleObjectException $exception) {
            $this->addError('version', $exception->getMessage());
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(): bool
    {
        try {
            return parent::delete();
        } catch (StaleObjectException $exception) {
            $this->addError('version', $exception->getMessage());
            return false;
        }
    }
}
