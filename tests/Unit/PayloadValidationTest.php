<?php namespace Tests\Unit;

use Hampel\KnownBots\SubContainer\Api;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SubContainer\Api::isValid() gates every bot payload before it reaches the code cache. An
 * invalid payload is rejected whole - bots are never partially updated.
 */
class PayloadValidationTest extends TestCase
{
    #[DataProvider('validPayloads')]
    public function test_accepts_a_well_formed_payload(array $payload)
    {
        $this->assertTrue($this->isValid($payload));
    }

    #[DataProvider('invalidPayloads')]
    public function test_rejects_a_malformed_payload(array $payload)
    {
        $this->assertFalse($this->isValid($payload));
    }

    public static function validPayloads() : array
    {
        $valid = self::valid();

        return [
            'full payload' => [$valid],
            'every list empty' => [array_replace($valid, [
                'maps' => [], 'bots' => [], 'complex' => [], 'ignored' => [], 'browsers' => [],
            ])],
            'bot with a null link' => [array_replace($valid, [
                'bots' => ['foobot' => ['title' => 'Foo Bot', 'link' => null]],
            ])],
        ];
    }

    public static function invalidPayloads() : array
    {
        $valid = self::valid();
        $without = function ($key) use ($valid) {
            return array_diff_key($valid, [$key => true]);
        };

        return [
            'version 2' => [array_replace($valid, ['version' => 2])],
            'version as a string' => [array_replace($valid, ['version' => '3'])],
            'no version' => [$without('version')],
            'no built' => [$without('built')],
            'no maps' => [$without('maps')],
            'no bots' => [$without('bots')],
            'no complex' => [$without('complex')],
            'no ignored' => [$without('ignored')],
            'no browsers' => [$without('browsers')],
            'built as a string' => [array_replace($valid, ['built' => '1700000000'])],
            'built null' => [array_replace($valid, ['built' => null])],
            'maps not an array' => [array_replace($valid, ['maps' => 'foobot'])],
            'maps value not a string' => [array_replace($valid, ['maps' => ['foobot' => 123]])],
            'maps as a list' => [array_replace($valid, ['maps' => ['foobot']])],
            'bots entry not an array' => [array_replace($valid, ['bots' => ['foobot' => 'Foo Bot']])],
            'bots field not a string' => [array_replace($valid, ['bots' => ['foobot' => ['title' => 123]]])],
            'complex value not a string' => [array_replace($valid, ['complex' => ['foo' => ['foobot']]])],
            'ignored keyed by name' => [array_replace($valid, ['ignored' => ['java' => '^Java/']])],
            'browsers keyed by name' => [array_replace($valid, ['browsers' => ['firefox' => 'Firefox/']])],
            'browsers value not a string' => [array_replace($valid, ['browsers' => [123]])],
        ];
    }

    // ------------------------------------------------------------------

    private static function valid() : array
    {
        return [
            'version' => 3,
            'built' => 1700000000,
            'maps' => ['foobot' => 'foobot'],
            'bots' => ['foobot' => ['title' => 'Foo Bot', 'link' => 'https://example.com/bot']],
            'complex' => ['foo\s?crawler' => 'foobot'],
            'ignored' => ['^Java/'],
            'browsers' => ['Mozilla/\d\.\d'],
        ];
    }

    private function isValid(array $payload) : bool
    {
        // isValid() is protected; it is a pure function, so exercising it directly is clearer
        // than routing a payload through the filesystem to reach it
        $method = new \ReflectionMethod(Api::class, 'isValid');

        return $method->invoke($this->app()['knownbots.api'], $payload);
    }
}
