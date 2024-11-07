<?php

use Monarch\Components\ComponentManager;
use Monarch\Helpers\Reflection;

beforeEach(function () {
    $this->componentManager = ComponentManager::instance()
        ->forComponentDirectories([
            'x' => TESTPATH . '_support/components/base',
            'm' => TESTPATH . '_support/components/mail',
        ]);
    $this->componentManager->discover();
});

describe('ComponentManager', function () {
    test('creates a single instance', function () {
        $instance1 = ComponentManager::instance();
        $instance2 = ComponentManager::instance();

        expect($instance1)->toBeInstanceOf(ComponentManager::class);
        expect($instance2)->toBe($instance1);
    });

    test('render finds basic components', function () {
        $tagName = 'simple-button';
        $content = 'Click Me!';

        $result = $this->componentManager->render('x', $tagName, [], $content);

        $expected = '<button type="button" class="btn btn-primary">
    Click Me!
</button>
';

        expect($result)->toBeString();
        expect($result)->toBe($expected);
    });

    test('render passes attributes array', function () {
        $tagName = 'attribute-button';
        $attributes = ['class' => 'btn-sm'];
        $content = 'Click Me!';

        $result = $this->componentManager->render('x', $tagName, $attributes, $content);

        $expected = '<button type="button" class="btn btn-primary btn-sm">
    Click Me!
</button>
';

        expect($result)->toBeString();
        expect($result)->toBe($expected);
    });

    test('integrates original attributes', function () {
        $tagName = 'attribute-button';
        $attributes = ['id' => 'foo', 'class' => 'btn-sm'];
        $content = 'Click Me!';

        $result = $this->componentManager->render('x', $tagName, $attributes, $content);

        $expected = '<button type="button" id="foo" class="btn btn-primary btn-sm">
    Click Me!
</button>
';

        expect($result)->toBeString();
        expect($result)->toBe($expected);
    });

    test('can find components in sub-directory', function () {
        $tagName = 'nested.button';
        $content = 'Click Me!';

        $result = $this->componentManager->render('x', $tagName, [], $content);

        $expected = '<button type="button" class="nested btn btn-primary">
    Click Me!
</button>
';

        expect($result)->toBeString();
        expect($result)->toBe($expected);
    });

    test('registers component directories', function () {
        $directories = ['path/to/components', 'another/path/to/components'];

        $this->componentManager->forComponentDirectories($directories);

        $componentDirectories = Reflection::getProperty($this->componentManager, 'componentDirectories');

        expect($componentDirectories)->toBe($directories);
    });

    test('renders a component with attributes and content', function () {
        $tagName = 'attribute-button';
        $attributes = ['class' => 'btn-sm'];
        $content = 'Click Me!';
        $expected = '<button type="button" class="btn btn-primary btn-sm">
    Click Me!
</button>
';

        $result = $this->componentManager->render('x', $tagName, $attributes, $content);

        expect($result)->toBeString();
        expect($result)->toBe($expected);
    });

    test('throws exception when rendering an unregistered component', function () {
        $tagName = 'unknown-component';

        expect(function () use ($tagName) {
            $this->componentManager->render('x', $tagName);
        })->toThrow(Exception::class, "Component $tagName not registered.");
    });

    test('parses slots in a component', function () {
        $component = '<div>
    <x-slot>Default Slot Content</x-slot>
    <x-slot name="header">Default Header Slot Content</x-slot>
    <x-slot name="footer">Default Footer Slot Content</x-slot>
</div>';
        $viewContent = 'Custom Slot Content
    <x-slot name="header">Custom Header Slot Content</x-slot>
    <x-slot name="footer">Custom Footer Slot Content</x-slot>
';
        $expected = '<div>
    Custom Slot Content

    Custom Header Slot Content
    Custom Footer Slot Content
</div>';

        $result = $this->componentManager->parseSlots($component, $viewContent);

        expect($result)->toBeString();
        expect($result)->toBe($expected);
    });
});
