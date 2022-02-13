<?php

namespace app\tests\unit\models;

use app\models\Users;
use Codeception\Test\Unit;

class UsersValidateTest extends Unit
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        Users::deleteAll();
        $user = new Users();
        $user->name = 'administrator';
        $user->email = 'administrator@administrator.com';
        $user->created = date('Y-m-d H:i:s');
        $user->save();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Users::deleteAll();
    }

    /**
     * @dataProvider validateProvider
     * @param string $attribute
     * @param mixed $value
     * @param bool $expected
     * @param string|null $message
     * @return void
     */
    public function testValidate(string $attribute, $value, bool $expected, string $message = null): void
    {
        $user = new Users();
        $user->$attribute = $value;
        $actual = $user->validate($attribute);
        $this->assertEquals($expected, $actual);

        if ($message) {
            $this->assertEquals($message, $user->getFirstError($attribute));
        }
    }

    /**
     * @return array[]
     */
    public function validateProvider(): array
    {
        return [
            'Name nullable' => ['name', null, false, 'Name required'],
            'Name untrimmed short' => ['name', '   name   ', false, 'Name should contain at least 8 characters.'],
            'Name untrimmed' => ['name', ' namename ', true],
            'Name short' => ['name', 'name', false, 'Name should contain at least 8 characters.'],
            'Name long' => ['name', str_repeat('name', 20), false, 'Name should contain at most 64 characters.'],
            'Name matched' => ['name', 'abcdef0123456', true],
            'Name unmatched' => ['name', 'aBcDeF0-2+4*6', false, 'The name must contain only the characters a-z, 0-9'],
            'Name unwanted' => ['name', 'stopwordone', false, 'This name cannot be used'],
            'Name not unique' => ['name', 'administrator', false, 'This name is already in use'],

            'Email nullable' => ['email', null, false, 'Email required'],
            'Email untrimmed' => ['email', '   email@email.com   ', true],
            'Email correct' => ['email', 'a@pinzhenin.ru', true],
            'Email incorrect-1' => ['email', '', false, 'Email required'],
            'Email incorrect-2' => ['email', 'a^a@a^a^a^a.a^a', false, 'This email is incorrect'],
            'Email unwanted' => ['email', 'name@stop-domain-one.com', false, 'This domain cannot be used'],
            'Email not unique' => ['email', 'administrator@administrator.com', false, 'This email is already in use'],

            'Created nullable' => ['created', null, true],
            'Created correct' => ['created', '2021-01-01 00:00:00', true],
            'Created incorrect' => ['created', '2021-01-01 24:00:00', false, 'The format of Created datetime is invalid.'],

            'Deleted nullable' => ['deleted', null, true],
            'Deleted correct' => ['deleted', '2021-01-01 00:00:00', true],
            'Deleted incorrect' => ['deleted', '2021-01-01 24:00:00', false, 'The format of Deleted datetime is invalid.'],
        ];
    }
}
