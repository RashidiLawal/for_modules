<?php

declare(strict_types=1);

namespace BitCore\Tests\Application\Config;

use BitCore\Foundation\Events\Dispatcher;
use BitCore\Tests\TestCase;

class HooksTest extends TestCase
{
    /** @var Dispatcher $hooks */
    private Dispatcher $hooks;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the hooks system
        $this->hooks = hooks();
    }

    protected function tearDown(): void
    {
        // Remove any event listeners to ensure no side-effects for other tests
        $this->hooks->forget('after_load_settings');

        parent::tearDown();
    }


    public function testListenerModifiesArrayPayload(): void
    {
        $this->hooks->listen('after_load_settings', function (&$settings) {
            $settings['app.hook'] = 'filtered';
        });

        // Initialize settings
        $settings = [];

        // Dispatch the event, passing settings by reference
        $this->hooks->dispatch('after_load_settings', [&$settings]);

        // Assert that the listener modified the settings array
        $this->assertArrayHasKey('app.hook', $settings);
        $this->assertEquals('filtered', $settings['app.hook']);
    }

    public function testListenerModifiesInstancePayload(): void
    {
        $this->hooks->listen('after_load_settings', function ($settingsObject) {
            $settingsObject->{'app.hook'} = 'filtered';
        });

        // Initialize settings
        $settings = (object)[];

        // Dispatch the event, passing settings by reference
        $this->hooks->dispatch('after_load_settings', $settings);

        // Assert that the listener modified the settings array
        $this->assertObjectHasProperty('app.hook', $settings);
        $this->assertEquals('filtered', $settings->{'app.hook'});
    }

    public function testMultipleListenersModifyPayload(): void
    {
        // Register multiple listeners for the 'after_load_settings' hook
        $this->hooks->listen('after_load_settings', function (&$settings) {
            $settings['listener_one'] = 'value_one';
        });

        $this->hooks->listen('after_load_settings', function (&$settings) {
            $settings['listener_two'] = 'value_two';
        });

        // Initialize settings
        $settings = [];

        // Dispatch the event
        $this->hooks->dispatch('after_load_settings', [&$settings]);

        // Assert that all listeners modified the settings array
        $this->assertArrayHasKey('listener_one', $settings);
        $this->assertArrayHasKey('listener_two', $settings);
        $this->assertEquals('value_one', $settings['listener_one']);
        $this->assertEquals('value_two', $settings['listener_two']);
    }

    public function testListenerDoesNotModifyPayloadWhenNotRegistered(): void
    {
        // Initialize settings
        $settings = ['existing_key' => 'existing_value'];

        // Dispatch the event without registering any listeners
        $this->hooks->dispatch('after_load_settings', [&$settings]);

        // Assert that the settings remain unchanged
        $this->assertArrayHasKey('existing_key', $settings);
        $this->assertEquals('existing_value', $settings['existing_key']);
    }

    public function testListenerOrderMatters(): void
    {
        // Register multiple listeners, where the order of execution matters
        $this->hooks->listen('after_load_settings', function (&$settings) {
            $settings['key'] = 'first_listener';
        });

        $this->hooks->listen('after_load_settings', function (&$settings) {
            $settings['key'] = 'second_listener';
        });

        // Initialize settings
        $settings = [];

        // Dispatch the event
        $this->hooks->dispatch('after_load_settings', [&$settings]);

        // Assert that the last listener overwrote the value
        $this->assertArrayHasKey('key', $settings);
        $this->assertEquals('second_listener', $settings['key']);
    }

    public function testListenerExecutionOrderBasedOnPriority(): void
    {
        $executionOrder = [];
        $settings = ['existing_key' => 'existing_value'];

        // Register listeners with different priorities
        $this->hooks->listen('after_load_settings', function (&$settings) use (&$executionOrder) {
            $executionOrder[] = 'listener_high_priority';
            $settings['priority'] = 'high';
            return $settings;
        }, 5); // High priority

        $this->hooks->listen('after_load_settings', function (&$settings) use (&$executionOrder) {
            $executionOrder[] = 'listener_low_priority';
            $settings['priority'] = 'low';
            return $settings;
        }, 15); // Low priority

        // Dispatch the event
        $this->hooks->dispatch('after_load_settings', [&$settings]);

        // Assertions
        $this->assertTrue(isset($settings['priority']));
        $this->assertEquals(['listener_high_priority', 'listener_low_priority'], $executionOrder);
        $this->assertEquals('low', $settings['priority']);
    }

    public function testOverlappingPriorities(): void
    {
        $executionOrder = [];
        $settings = ['existing_key' => 'existing_value'];

        // Register listeners with the same priority
        $this->hooks->listen('after_load_settings', function ($settings) use (&$executionOrder) {
            $executionOrder[] = 'listener_1';
            return $settings;
        }, 10);

        $this->hooks->listen('after_load_settings', function ($settings) use (&$executionOrder) {
            $executionOrder[] = 'listener_2';
            return $settings;
        }, 10);

        // Dispatch the event
        $this->hooks->dispatch('after_load_settings', $settings);

        // Assertions
        $this->assertEquals(
            ['listener_1', 'listener_2'],
            $executionOrder,
            'Listeners with the same priority should execute in the order they were registered.'
        );
    }
}
