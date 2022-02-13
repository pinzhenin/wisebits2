<?php

namespace app\tests\unit\models;

use app\models\Users;
use Codeception\Test\Unit;
use DateTime;
use Exception;
use yii\db\IntegrityException;

class UsersTest extends Unit
{
    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        Users::deleteAll();
        $user = new Users();
        $user->id = 1;
        $user->name = 'administrator';
        $user->email = 'admin@admin.com';
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
     * @return array[]
     */
    public function findValidateProvider(): array
    {
        return [
            'Find existence by id' => ['id', 1, true],
            'Find existence by name' => ['name', 'administrator', true],
            'Find existence by email' => ['email', 'admin@admin.com', true],
            'Find non-existence by id' => ['id', 2, false],
            'Find non-existence by name' => ['name', 'administrator1', false],
            'Find non-existence by email' => ['email', 'admin1@admin1.com', false],
        ];
    }

    /**
     * @dataProvider findValidateProvider
     * @param string $attribute
     * @param int|string $value
     * @param bool $expected
     * @return void
     */
    public function testFindUser(string $attribute, $value, bool $expected): void
    {
        $user = Users::findOne([$attribute => $value]);
        if ($expected) {
            $this->assertIsObject($user);
        } else {
            $this->assertNull($user);
        }
    }

    /**
     * @return array[]
     */
    public function insertValidateProvider(): array
    {
        $attributes = ['name' => 'administrator1', 'email' => 'admin1@admin1.com', 'created' => date('Y-m-d H:i:s')];
        return [
            'Insert unique' => [$attributes, true],
            'Insert non-unique name' => [['name' => 'administrator'] + $attributes, IntegrityException::class],
            'Insert non-unique email' => [['email' => 'admin@admin.com'] + $attributes, IntegrityException::class],
            'Insert empty created' => [['created' => null] + $attributes, IntegrityException::class],
        ];
    }

    /**
     * @dataProvider insertValidateProvider
     * @param string[] $attributes
     * @param bool|string $expected
     * @return void
     */
    public function testInsertUser(array $attributes, $expected): void
    {
        $user = new Users();
        $user->setAttributes($attributes);
        if (is_bool($expected)) {
            $actual = $user->save(false);
            $this->assertEquals($expected, $actual);
        } elseif (is_string($expected)) {
            $this->expectException(IntegrityException::class);
            $user->save(false);
        }
    }

    /**
     * @return array[]
     */
    public function updateValidateProvider(): array
    {
        return [
            'Update unique' => [['name' => 'administrator2', 'email' => 'admin2@admin2.com'], true],
            'Update non-unique name' => [['name' => 'administrator'], IntegrityException::class],
            'Update non-unique email' => [['email' => 'admin@admin.com'], IntegrityException::class],
            'Update empty created' => [['created' => null], IntegrityException::class],
        ];
    }

    /**
     * @dataProvider updateValidateProvider
     * @param string[] $attributes
     * @param bool|string $expected
     * @return void
     */
    public function testUpdateUser(array $attributes, $expected): void
    {
        $user = new Users();
        $user->name = 'administrator1';
        $user->email = 'admin1@admin1.com';
        $result = $user->save();
        $this->assertTrue($result);

        $user->setAttributes($attributes);
        if (is_bool($expected)) {
            $actual = $user->save(false);
            $this->assertEquals($expected, $actual);
        } elseif (is_string($expected)) {
            $this->expectException(IntegrityException::class);
            $user->save(false);
        }
    }

    /**
     * @return void
     */
    public function testUpdateUserWithOptimisticLock(): void
    {
        $oldUser = Users::findOne(1);
        $newUser = new Users();
        $newUser->setAttributes($oldUser->getAttributes());

        $oldUser->name = 'administrator1';
        $result = $oldUser->save();
        $this->assertTrue($result);

        $newUser->name = 'administrator2';
        $result = $newUser->save();
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testDeleteUser(): void
    {
        $user = Users::findOne(1);
        $user->delete();
        $user = Users::findOne(1);
        $this->assertNull($user);
    }

    /**
     * @return void
     */
    public function testDeleteUserWithOptimisticLock(): void
    {
        $oldUser = Users::findOne(1);
        $newUser = new Users();
        $newUser->setAttributes($oldUser->getAttributes());

        $oldUser->name = 'administrator1';
        $result = $oldUser->save();
        $this->assertTrue($result);

        $result = $newUser->delete();
        $this->assertFalse($result);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSoftDeleteUser(): void
    {
        $user = Users::findOne(1);
        $user->softDelete();
        $this->assertInstanceOf(DateTime::class, new DateTime($user->deleted));
    }
}
