<?php

use Monarch\Components\Component;
use Monarch\Components\TagParser;
use Monarch\Components\ComponentManager;

beforeEach(function () {
    $this->componentManager = ComponentManager::instance();
    $this->componentManager->forComponentDirectories([
        'x' => TESTPATH . '_support/components/base',
        'm' => TESTPATH . '_support/components/mail',
    ]);
    $this->componentManager->discover();
});

test('singleton instance', function () {
    $instance1 = TagParser::instance($this->componentManager);
    $instance2 = TagParser::instance($this->componentManager);

    expect($instance2)->toBe($instance1);
});

test('parse with base tags', function () {
    $tagParser = TagParser::instance($this->componentManager);
    $html = '<x-simple-button>Content</x-simple-button>';
    $expected = '<button type="button" class="btn btn-primary">
    Content
</button>
';

    expect($tagParser->parse($html))->toEqual($expected);
});

test('parse with mail tags', function () {
    $tagParser = TagParser::instance($this->componentManager);
    $html = '<m-simple-button>Content</m-simple-button>';
    $expected = '<button type="button" class="mail btn btn-primary">
    Content
</button>
';

    expect($tagParser->parse($html))->toEqual($expected);
});

test('parse with multiple custom tags', function () {
    $tagParser = TagParser::instance($this->componentManager);
    $html = '<m-simple-button>Content1</m-simple-button><m-simple-button>Content2</m-simple-button>';
    $expected = '<button type="button" class="mail btn btn-primary">
    Content1
</button>
<button type="button" class="mail btn btn-primary">
    Content2
</button>
';

    expect($tagParser->parse($html))->toEqual($expected);
});

test('parse nested components', function() {
    $tagParser = TagParser::instance($this->componentManager);
    $html = '<x-nested.button>Content</x-nested.button>';
    $expected = '<button type="button" class="nested btn btn-primary">
    Content
</button>
';
    expect($tagParser->parse($html))->toEqual($expected);
});

test('parse attributes', function () {
    $tagParser = TagParser::instance($this->componentManager);

    $reflection = new ReflectionClass($tagParser);
    $method = $reflection->getMethod('parseAttributes');
    $method->setAccessible(true);

    $attributesString = 'attr1="value1" attr2="value2"';
    $expected = [
        'attr1' => 'value1',
        'attr2' => 'value2',
    ];

    expect($method->invokeArgs($tagParser, [$attributesString]))->toEqual($expected);
});
