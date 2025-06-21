<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Tests;

use BitCore\Modules\Settings\Events\SettingsLoaded;
use BitCore\Modules\Settings\Repositories\SettingsRepository;
use BitCore\Tests\TestCase;

class SettingsRepositoryTest extends TestCase
{
    protected SettingsRepository $settings;
    protected $table = 'settings';

    protected function setUp(): void
    {
        parent::setUp();

        $this->getAppInstance();

        $seed = [
            ['key' => 'app.name', 'value' => 'TestApp'],
            ['key' => 'app.debug', 'value' => json_encode(true)],
            ['key' => 'app.enabled', 'value' => 'yes'],
            ['key' => 'app.disabled', 'value' => 'no'],
            ['key' => 'app.registerationEnabled', 'value' => '1'],
            ['key' => 'app.loginDisabled', 'value' => '0'],
        ];

        $seed = array_map(function ($row) {
            return array_merge($row, ['group' => 'system', 'group_id' => 'default']);
        }, $seed);

        // Set up the testing database table
        db()->table('settings')->truncate();
        db()->table('settings')->insert($seed);

        $this->settings = new SettingsRepository();
        $this->settings->load();
    }

    public function testSetAndRetrieveSetting(): void
    {
        // Act
        $this->settings->set('app.version', '1.0.0');
        $result = $this->settings->get('app.version');

        // Assert
        $this->assertEquals('1.0.0', $result);
    }

    public function testSaveSettingToDatabase(): void
    {
        // Act
        $this->settings->save('app.env', 'production');
        $result = db()->table('settings')->where('key', 'app.env')->first();

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('production', $result->value);
    }

    public function testSaveMultipleSettingsToDatabase(): void
    {
        // Arrange
        $data = [
            'app.timezone' => 'UTC',
            'app.locale' => 'en',
        ];

        // Act
        $this->settings->saveMany($data);

        $result = db()->table('settings')->where('key', 'app.timezone')->first();
        $result2 = db()->table('settings')->where('key', 'app.locale')->first();

        // Assert
        $this->assertEquals('UTC', $result->value);
        $this->assertEquals('en', $result2->value);
    }

    public function testGetAllSettings(): void
    {
        // Act
        $allSettings = $this->settings->getAll();
        $group = array_key_first($allSettings);
        $groupId = array_key_first($allSettings[$group]);

        // Assert
        $this->assertIsArray($allSettings);
        $this->assertArrayHasKey($group, $allSettings);
        $this->assertArrayHasKey($groupId, $allSettings[$group]);
        $this->assertEquals('TestApp', $allSettings[$group][$groupId]['app.name']['value']);
    }

    public function testJsonDecodingForSettingValues(): void
    {
        // Act
        $debugValue = $this->settings->get('app.debug');

        // Assert
        $this->assertEquals('true', $debugValue);
    }

    public function testBooleanValueMethod()
    {
        // Act
        $enabledValue = $this->settings->getAsBool('app.enabled');
        $disabledValue = $this->settings->getAsBool('app.disabled');
        $registerEnabled = $this->settings->getAsBool('app.registerationEnabled');
        $loginEnabled = $this->settings->getAsBool('app.loginEnabled');

        // Assert
        $this->assertIsBool($enabledValue);
        $this->assertIsBool($disabledValue);
        $this->assertIsBool($registerEnabled);
        $this->assertIsBool($loginEnabled);
        $this->assertTrue($enabledValue);
        $this->assertFalse($disabledValue);
        $this->assertTrue($registerEnabled);
        $this->assertFalse($loginEnabled);
    }

    public function testPersistDoesNotFailOnEmptyArray(): void
    {
        // Act
        $this->settings->saveMany([]);

        // Assert
        $this->assertTrue(true); // Ensure no exceptions are thrown
    }

    public function testOverwriteExistingSetting(): void
    {
        // Act
        $this->settings->save('app.name', 'NewAppName');
        $updatedValue = (new SettingsRepository())->load()->get('app.name');

        // Assert
        $this->assertEquals('NewAppName', $updatedValue);
    }

    public function testSetMultipleInMemory(): void
    {
        // Arrange
        $data = [
            'app.key' => 'base64:abc123',
            'app.secret' => 'supersecret',
        ];

        // Act
        $this->settings->setMany($data);

        // Assert
        $this->assertEquals('base64:abc123', $this->settings->get('app.key'));
        $this->assertEquals('supersecret', $this->settings->get('app.secret'));
    }

    public function testSaveAndRetrieveObject(): void
    {
        // Arrange
        $object = (object)[
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ];

        // Act
        $this->settings->save('user.profile', $object);
        $retrievedValue = $this->settings->get('user.profile');

        // Assert
        $this->assertIsObject($retrievedValue);
        $this->assertEquals('John Doe', $retrievedValue->name);
        $this->assertEquals('johndoe@example.com', $retrievedValue->email);
    }

    public function testSaveAndRetrieveArray(): void
    {
        // Arrange
        $array = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        // Act
        $this->settings->save('app.array', $array);
        $retrievedValue = $this->settings->get('app.array');

        // Assert
        $this->assertIsArray($retrievedValue);
        $this->assertArrayHasKey('key1', $retrievedValue);
        $this->assertEquals('value1', $retrievedValue['key1']);
    }

    public function testSaveAndRetrieveNestedArray(): void
    {
        // Arrange
        $nestedArray = [
            'parent' => [
                'child1' => 'value1',
                'child2' => [
                    'subchild1' => 'subvalue1',
                    'subchild2' => 'subvalue2',
                ],
            ],
        ];

        // Act
        $this->settings->save('app.nested', $nestedArray);
        $retrievedValue = $this->settings->get('app.nested');

        // Assert
        $this->assertIsArray($retrievedValue);
        $this->assertArrayHasKey('parent', $retrievedValue);
        $this->assertEquals('value1', $retrievedValue['parent']['child1']);
        $this->assertEquals('subvalue2', $retrievedValue['parent']['child2']['subchild2']);
    }

    public function testSaveAndRetrieveNestedObject(): void
    {
        // Arrange
        $nestedObject = (object)[
            'parent' => (object)[
                'child1' => 'value1',
                'child2' => (object)[
                    'subchild1' => 'subvalue1',
                    'subchild2' => 'subvalue2',
                ],
            ],
        ];

        // Act
        $this->settings->save('app.nested_object', $nestedObject);
        $retrievedValue = $this->settings->get('app.nested_object');

        // Assert
        $this->assertIsObject($retrievedValue);
        $this->assertEquals('value1', $retrievedValue->parent->child1);
        $this->assertEquals('subvalue1', $retrievedValue->parent->child2->subchild1);
    }

    public function testHandleInvalidJsonDataGracefully(): void
    {
        // Arrange
        $invalidJson = "{invalid: json}";

        // Act
        $this->settings->save('app.invalid_json', $invalidJson);
        $retrievedValue = $this->settings->get('app.invalid_json');

        // Assert
        $this->assertIsString($retrievedValue);
        $this->assertEquals($invalidJson, $retrievedValue); // Ensure invalid JSON is returned as-is
    }

    public function testDataFiltering(): void
    {
        hooks()->listen(SettingsLoaded::class, function (SettingsRepository $settings) {
            $settings->set('app.hook', 'filered');
        });

        $this->settings->setSettings($this->settings->getAll());

        $filteredValue = $this->settings->get('app.hook');

        // Assert
        $this->assertNotNull($filteredValue);
        $this->assertIsString('filtered', $filteredValue);
    }

    public function testRegisterNewSetting(): void
    {
        // Act
        $affected = $this->settings->register('app.theme', 'dark', ['visible' => true]);

        // Assert
        $this->assertTrue($affected);

        $value = $this->settings->get('app.theme');
        $this->assertEquals('dark', $value);

        $metadata = $this->settings->getMetadata('app.theme');
        $this->assertArrayHasKey('visible', $metadata);
        $this->assertTrue($metadata['visible']);
    }

    public function testRegisterManyNewSettings(): void
    {
        // Arrange
        $data = [
            'app.color' => [
                'value'    => 'blue',
                'metadata' => ['editable' => true, 'type' => 'color'],
            ],
            'app.size'  => [
                'value'    => 'large',
                'metadata' => [
                    'editable' => false,
                    'type'     => 'radio',
                    'options'  => ['small' => 'messages.setings.small', 'large' => 'messages.setings.small'],
                ],
            ],
        ];

        // Act
        $affected = $this->settings->registerMany($data);

        // Assert
        $this->assertEquals(2, $affected);

        $this->assertEquals('blue', $this->settings->get('app.color'));
        $colorMetadata = $this->settings->getMetadata('app.color');
        $this->assertTrue($colorMetadata['editable']);

        $this->assertEquals('large', $this->settings->get('app.size'));
        $sizeMetadata = $this->settings->getMetadata('app.size');
        $this->assertFalse($sizeMetadata['editable']);
        $this->assertEquals('radio', $sizeMetadata['type']);
        $this->assertArrayHasKey('options', $sizeMetadata);
    }

    public function testRegisterExistingSettingWithoutOverwrite(): void
    {
        // Arrange
        $this->settings->save('app.theme', 'light');

        // Act
        $affected = $this->settings->register('app.theme', 'dark', ['visible' => true], false);

        // Assert
        $this->assertEquals(1, $affected);

        $this->assertEquals('light', $this->settings->get('app.theme')); // should not overwrite

        $metadata = $this->settings->getMetadata('app.theme');
        $this->assertArrayHasKey('visible', $metadata);
    }

    public function testRegisterExistingSettingWithOverwrite(): void
    {
        // Arrange
        $this->settings->save('app.theme', 'light');

        // Act
        $affected = $this->settings->register('app.theme', 'dark', ['visible' => true], true);


        // Assert
        $this->assertEquals(1, $affected);

        $this->assertEquals('dark', $this->settings->get('app.theme')); // should overwrite

        $metadata = $this->settings->getMetadata('app.theme');
        $this->assertArrayHasKey('visible', $metadata);
    }
}
