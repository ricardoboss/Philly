<?php

declare(strict_types=1);

namespace test\Philly\Unit\Support;

use Philly\Support\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function testStringCanBeLimitedByWords()
    {
        static::assertSame('Taylor...', Str::words('Taylor Otwell', 1));
        static::assertSame('Taylor___', Str::words('Taylor Otwell', 1, '___'));
        static::assertSame('Taylor Otwell', Str::words('Taylor Otwell', 3));

        // test implicit limit of 100 words
        $long_text = str_repeat("Test ", 100);
        $longer_text = $long_text . "Testend";
        $expect_text = rtrim($long_text) . "...";
        static::assertSame($expect_text, Str::words($longer_text));
    }

    public function testStringTitle()
    {
        static::assertSame('Jefferson Costella', Str::title('jefferson costella'));
        static::assertSame('Jefferson Costella', Str::title('jefFErson coSTella'));
    }

    public function testStringWithoutWordsDoesntProduceError()
    {
        $nbsp = chr(0xC2) . chr(0xA0);
        static::assertSame(' ', Str::words(' '));
        static::assertEquals($nbsp, Str::words($nbsp));
    }

    public function testStartsWith()
    {
        static::assertTrue(Str::startsWith('jason', 'jas'));
        static::assertTrue(Str::startsWith('jason', 'jason'));
        static::assertTrue(Str::startsWith('jason', ['jas']));
        static::assertTrue(Str::startsWith('jason', ['day', 'jas']));
        static::assertFalse(Str::startsWith('jason', 'day'));
        static::assertFalse(Str::startsWith('jason', ['day']));
        static::assertFalse(Str::startsWith('jason', null));
        static::assertFalse(Str::startsWith('jason', [null]));
        static::assertFalse(Str::startsWith('0123', [null]));
        static::assertFalse(Str::startsWith('jason', 'J'));
        static::assertFalse(Str::startsWith('jason', ''));
        static::assertFalse(Str::startsWith('', ''));
        static::assertFalse(Str::startsWith('7', ' 7'));
        static::assertTrue(Str::startsWith('7a', '7'));
        // Test for multibyte string support
        static::assertTrue(Str::startsWith('Jönköping', 'Jö'));
        static::assertTrue(Str::startsWith('Malmö', 'Malmö'));
        static::assertFalse(Str::startsWith('Jönköping', 'Jonko'));
        static::assertFalse(Str::startsWith('Malmö', 'Malmo'));
        static::assertTrue(Str::startsWith('你好', '你'));
        static::assertFalse(Str::startsWith('你好', '好'));
        static::assertFalse(Str::startsWith('你好', 'a'));
    }

    public function testEndsWith()
    {
        static::assertTrue(Str::endsWith('jason', 'on'));
        static::assertTrue(Str::endsWith('jason', 'jason'));
        static::assertTrue(Str::endsWith('jason', ['on']));
        static::assertTrue(Str::endsWith('jason', ['no', 'on']));
        static::assertFalse(Str::endsWith('jason', 'no'));
        static::assertFalse(Str::endsWith('jason', ['no']));
        static::assertFalse(Str::endsWith('jason', ''));
        static::assertFalse(Str::endsWith('', ''));
        static::assertFalse(Str::endsWith('jason', [null]));
        static::assertFalse(Str::endsWith('jason', null));
        static::assertFalse(Str::endsWith('jason', 'N'));
        static::assertFalse(Str::endsWith('7', ' 7'));
        static::assertTrue(Str::endsWith('a7', '7'));
        // Test for multibyte string support
        static::assertTrue(Str::endsWith('Jönköping', 'öping'));
        static::assertTrue(Str::endsWith('Malmö', 'mö'));
        static::assertFalse(Str::endsWith('Jönköping', 'oping'));
        static::assertFalse(Str::endsWith('Malmö', 'mo'));
        static::assertTrue(Str::endsWith('你好', '好'));
        static::assertFalse(Str::endsWith('你好', '你'));
        static::assertFalse(Str::endsWith('你好', 'a'));
    }

    public function testStrBefore()
    {
        static::assertSame('han', Str::before('hannah', 'nah'));
        static::assertSame('ha', Str::before('hannah', 'n'));
        static::assertSame('ééé ', Str::before('ééé hannah', 'han'));
        static::assertSame('hannah', Str::before('hannah', 'xxxx'));
        static::assertSame('hannah', Str::before('hannah', ''));
        static::assertSame('han', Str::before('han0nah', '0'));
    }

    public function testStrBeforeLast()
    {
        static::assertSame('yve', Str::beforeLast('yvette', 'tte'));
        static::assertSame('yvet', Str::beforeLast('yvette', 't'));
        static::assertSame('ééé ', Str::beforeLast('ééé yvette', 'yve'));
        static::assertSame('', Str::beforeLast('yvette', 'yve'));
        static::assertSame('yvette', Str::beforeLast('yvette', 'xxxx'));
        static::assertSame('yvette', Str::beforeLast('yvette', ''));
        static::assertSame('yv0et', Str::beforeLast('yv0et0te', '0'));
    }

    public function testStrBetween()
    {
        static::assertSame('abc', Str::between('abc', '', 'c'));
        static::assertSame('abc', Str::between('abc', 'a', ''));
        static::assertSame('abc', Str::between('abc', '', ''));
        static::assertSame('b', Str::between('abc', 'a', 'c'));
        static::assertSame('b', Str::between('dddabc', 'a', 'c'));
        static::assertSame('b', Str::between('abcddd', 'a', 'c'));
        static::assertSame('b', Str::between('dddabcddd', 'a', 'c'));
        static::assertSame('nn', Str::between('hannah', 'ha', 'ah'));
        static::assertSame('a]ab[b', Str::between('[a]ab[b]', '[', ']'));
        static::assertSame('foo', Str::between('foofoobar', 'foo', 'bar'));
        static::assertSame('bar', Str::between('foobarbar', 'foo', 'bar'));
    }

    public function testStrAfter()
    {
        static::assertSame('nah', Str::after('hannah', 'han'));
        static::assertSame('nah', Str::after('hannah', 'n'));
        static::assertSame('nah', Str::after('ééé hannah', 'han'));
        static::assertSame('hannah', Str::after('hannah', 'xxxx'));
        static::assertSame('hannah', Str::after('hannah', ''));
        static::assertSame('nah', Str::after('han0nah', '0'));
    }

    public function testStrAfterLast()
    {
        static::assertSame('tte', Str::afterLast('yvette', 'yve'));
        static::assertSame('e', Str::afterLast('yvette', 't'));
        static::assertSame('e', Str::afterLast('ééé yvette', 't'));
        static::assertSame('', Str::afterLast('yvette', 'tte'));
        static::assertSame('yvette', Str::afterLast('yvette', 'xxxx'));
        static::assertSame('yvette', Str::afterLast('yvette', ''));
        static::assertSame('te', Str::afterLast('yv0et0te', '0'));
        static::assertSame('foo', Str::afterLast('----foo', '---'));
    }

    public function testStrContains()
    {
        static::assertTrue(Str::contains('taylor', 'ylo'));
        static::assertTrue(Str::contains('taylor', 'taylor'));
        static::assertTrue(Str::contains('taylor', ['ylo']));
        static::assertTrue(Str::contains('taylor', ['xxx', 'ylo']));
        static::assertFalse(Str::contains('taylor', 'xxx'));
        static::assertFalse(Str::contains('taylor', ['xxx']));
        static::assertFalse(Str::contains('taylor', ''));
        static::assertFalse(Str::contains('', ''));
        $mb_string = 'žščř';
        static::assertTrue(Str::contains($mb_string, 'š'));
    }

    public function testStrContainsAll()
    {
        static::assertTrue(Str::containsAll('taylor otwell', ['taylor', 'otwell']));
        static::assertTrue(Str::containsAll('taylor otwell', ['taylor']));
        static::assertFalse(Str::containsAll('taylor otwell', ['taylor', 'xxx']));
    }

    public function testSlug()
    {
        static::assertSame('hello-world', Str::slug('hello world'));
        static::assertSame('hello-world', Str::slug('hello-world'));
        static::assertSame('hello-world', Str::slug('hello_world'));
        static::assertSame('hello-world', Str::slug('-hello_world-'));
        static::assertSame('hello_world', Str::slug('hello_world', '_'));
        static::assertSame('hello_world', Str::slug('_hello_world_', '_'));
        static::assertSame('hello\'world', Str::slug('hello world', '\''));
        static::assertSame('hellö\'world', Str::slug('hellö world', '\''));
        static::assertSame('helloworld\'also', Str::slug('hello_world also', '\''));
        static::assertSame('user-at-host', Str::slug('user@host'));
        static::assertSame('sometext', Str::slug('some text', ''));
        static::assertSame('', Str::slug('', ''));
        static::assertSame('', Str::slug(''));
    }

    public function testStrStart()
    {
        static::assertSame('/test/string', Str::start('test/string', '/'));
        static::assertSame('/test/string', Str::start('/test/string', '/'));
        static::assertSame('/test/string', Str::start('//test/string', '/'));
    }

    public function testFinish()
    {
        static::assertSame('abbc', Str::finish('ab', 'bc'));
        static::assertSame('abbc', Str::finish('abbcbc', 'bc'));
        static::assertSame('abcbbc', Str::finish('abcbbcbc', 'bc'));
    }

    public function testIs()
    {
        static::assertTrue(Str::is('/', '/'));
        static::assertFalse(Str::is('/', ' /'));
        static::assertFalse(Str::is('/', '/a'));
        static::assertTrue(Str::is('foo/*', 'foo/bar/baz'));

        static::assertTrue(Str::is('*@*', 'App\Class@method'));
        static::assertTrue(Str::is('*@*', 'app\Class@'));
        static::assertTrue(Str::is('*@*', '@method'));

        // is case sensitive
        static::assertFalse(Str::is('*BAZ*', 'foo/bar/baz'));
        static::assertFalse(Str::is('*FOO*', 'foo/bar/baz'));
        static::assertFalse(Str::is('A', 'a'));

        static::assertTrue(Str::is('*/foo', 'blah/baz/foo'));
    }

    /**
     * @dataProvider validUuidList
     */
    public function testIsUuidWithValidUuid($uuid)
    {
        static::assertTrue(Str::isUuid($uuid));
    }

    /**
     * @dataProvider invalidUuidList
     */
    public function testIsUuidWithInvalidUuid($uuid)
    {
        static::assertFalse(Str::isUuid($uuid));
    }

    public function testKebab()
    {
        static::assertSame('laravel-php-framework', Str::kebab('LaravelPhpFramework'));
    }

    public function testLower()
    {
        static::assertSame('foo bar baz', Str::lower('FOO BAR BAZ'));
        static::assertSame('foo bar baz', Str::lower('fOo Bar bAz'));
    }

    public function testUpper()
    {
        static::assertSame('FOO BAR BAZ', Str::upper('foo bar baz'));
        static::assertSame('FOO BAR BAZ', Str::upper('foO bAr BaZ'));
    }

    public function testLimit()
    {
        static::assertSame(
            'Laravel is...',
            Str::limit('Laravel is a free, open source PHP web application framework.', 10)
        );
        static::assertSame('这是一...', Str::limit('这是一段中文', 6));

        $string = 'The PHP framework for web artisans.';
        static::assertSame('The PHP...', Str::limit($string, 7));
        static::assertSame('The PHP', Str::limit($string, 7, ''));
        static::assertSame('The PHP framework for web artisans.', Str::limit($string, 100));

        $nonAsciiString = '这是一段中文';
        static::assertSame('这是一...', Str::limit($nonAsciiString, 6));
        static::assertSame('这是一', Str::limit($nonAsciiString, 6, ''));

        // test implicit limit of 100
        $text = str_repeat("test ", 30);
        $expected = rtrim(substr($text, 0, 100)) . '...';
        static::assertSame($expected, Str::limit($text));
    }

    public function testLimitTrim()
    {
        $string = 'The PHP framework for web artisans.';
        static::assertSame('The PHP...', Str::limit($string, 8));
    }

    public function testLength()
    {
        static::assertEquals(11, Str::length('foo bar baz'));
        static::assertEquals(11, Str::length('foo bar baz', 'UTF-8'));

        $mb_string = 'žščř';
        static::assertEquals(4, Str::length($mb_string));
        static::assertEquals(4, Str::length($mb_string, 'UTF-8'));
    }

    public function testRandom()
    {
        static::assertEquals(16, strlen(Str::random()));
        $randomInteger = random_int(1, 1000);
        static::assertEquals($randomInteger, strlen(Str::random($randomInteger)));
        static::assertIsString(Str::random());
    }

    public function testReplaceArray()
    {
        static::assertSame('foo/bar/baz', Str::replaceArray('?', ['foo', 'bar', 'baz'], '?/?/?'));
        static::assertSame('foo/bar/baz/?', Str::replaceArray('?', ['foo', 'bar', 'baz'], '?/?/?/?'));
        static::assertSame('foo/bar', Str::replaceArray('?', ['foo', 'bar', 'baz'], '?/?'));
        static::assertSame('?/?/?', Str::replaceArray('x', ['foo', 'bar', 'baz'], '?/?/?'));
        // Ensure recursive replacements are avoided
        static::assertSame('foo?/bar/baz', Str::replaceArray('?', ['foo?', 'bar', 'baz'], '?/?/?'));
        // Test for associative array support
        static::assertSame('foo/bar', Str::replaceArray('?', [1 => 'foo', 2 => 'bar'], '?/?'));
        static::assertSame('foo/bar', Str::replaceArray('?', ['x' => 'foo', 'y' => 'bar'], '?/?'));
    }

    public function testReplaceFirst()
    {
        static::assertSame('fooqux foobar', Str::replaceFirst('bar', 'qux', 'foobar foobar'));
        static::assertSame('foo/qux? foo/bar?', Str::replaceFirst('bar?', 'qux?', 'foo/bar? foo/bar?'));
        static::assertSame('foo foobar', Str::replaceFirst('bar', '', 'foobar foobar'));
        static::assertSame('foobar foobar', Str::replaceFirst('xxx', 'yyy', 'foobar foobar'));
        static::assertSame('foobar foobar', Str::replaceFirst('', 'yyy', 'foobar foobar'));
        // Test for multibyte string support
        static::assertSame('Jxxxnköping Malmö', Str::replaceFirst('ö', 'xxx', 'Jönköping Malmö'));
        static::assertSame('Jönköping Malmö', Str::replaceFirst('', 'yyy', 'Jönköping Malmö'));
    }

    public function testReplaceLast()
    {
        static::assertSame('foobar fooqux', Str::replaceLast('bar', 'qux', 'foobar foobar'));
        static::assertSame('foo/bar? foo/qux?', Str::replaceLast('bar?', 'qux?', 'foo/bar? foo/bar?'));
        static::assertSame('foobar foo', Str::replaceLast('bar', '', 'foobar foobar'));
        static::assertSame('foobar foobar', Str::replaceLast('xxx', 'yyy', 'foobar foobar'));
        static::assertSame('foobar foobar', Str::replaceLast('', 'yyy', 'foobar foobar'));
        // Test for multibyte string support
        static::assertSame('Malmö Jönkxxxping', Str::replaceLast('ö', 'xxx', 'Malmö Jönköping'));
        static::assertSame('Malmö Jönköping', Str::replaceLast('', 'yyy', 'Malmö Jönköping'));
    }

    public function testSnake()
    {
        static::assertSame('laravel_p_h_p_framework', Str::snake('LaravelPHPFramework'));
        static::assertSame('laravel_php_framework', Str::snake('LaravelPhpFramework'));
        static::assertSame('laravel php framework', Str::snake('LaravelPhpFramework', ' '));
        static::assertSame('laravel_php_framework', Str::snake('Laravel Php Framework'));
        static::assertSame('laravel_php_framework', Str::snake('Laravel    Php      Framework   '));
        // ensure cache keys don't overlap
        static::assertSame('laravel__php__framework', Str::snake('LaravelPhpFramework', '__'));
        static::assertSame('laravel_php_framework_', Str::snake('LaravelPhpFramework_', '_'));
        static::assertSame('laravel_php_framework', Str::snake('laravel php Framework'));
        static::assertSame('laravel_php_frame_work', Str::snake('laravel php FrameWork'));
        // prevent breaking changes
        static::assertSame('foo-bar', Str::snake('foo-bar'));
        static::assertSame('foo-_bar', Str::snake('Foo-Bar'));
        static::assertSame('foo__bar', Str::snake('Foo_Bar'));
        static::assertSame('żółtałódka', Str::snake('ŻółtaŁódka'));
    }

    public function testStudly()
    {
        static::assertSame('LaravelPHPFramework', Str::studly('laravel_p_h_p_framework'));
        static::assertSame('LaravelPhpFramework', Str::studly('laravel_php_framework'));
        static::assertSame('LaravelPhPFramework', Str::studly('laravel-phP-framework'));
        static::assertSame('LaravelPhpFramework', Str::studly('laravel  -_-  php   -_-   framework   '));

        static::assertSame('FooBar', Str::studly('fooBar'));
        static::assertSame('FooBar', Str::studly('foo_bar'));
        static::assertSame('FooBar', Str::studly('foo_bar')); // test cache
        static::assertSame('FooBarBaz', Str::studly('foo-barBaz'));
        static::assertSame('FooBarBaz', Str::studly('foo-bar_baz'));
    }

    public function testCamel()
    {
        static::assertSame('laravelPHPFramework', Str::camel('Laravel_p_h_p_framework'));
        static::assertSame('laravelPhpFramework', Str::camel('Laravel_php_framework'));
        static::assertSame('laravelPhPFramework', Str::camel('Laravel-phP-framework'));
        static::assertSame('laravelPhpFramework', Str::camel('Laravel  -_-  php   -_-   framework   '));

        static::assertSame('fooBar', Str::camel('FooBar'));
        static::assertSame('fooBar', Str::camel('foo_bar'));
        static::assertSame('fooBar', Str::camel('foo_bar')); // test cache
        static::assertSame('fooBarBaz', Str::camel('Foo-barBaz'));
        static::assertSame('fooBarBaz', Str::camel('foo-bar_baz'));
    }

    public function testSubstr()
    {
        static::assertSame('Ё', Str::substr('БГДЖИЛЁ', -1));
        static::assertSame('ЛЁ', Str::substr('БГДЖИЛЁ', -2));
        static::assertSame('И', Str::substr('БГДЖИЛЁ', -3, 1));
        static::assertSame('ДЖИЛ', Str::substr('БГДЖИЛЁ', 2, -1));
        static::assertEmpty(Str::substr('БГДЖИЛЁ', 4, -4));
        static::assertSame('ИЛ', Str::substr('БГДЖИЛЁ', -3, -1));
        static::assertSame('ГДЖИЛЁ', Str::substr('БГДЖИЛЁ', 1));
        static::assertSame('ГДЖ', Str::substr('БГДЖИЛЁ', 1, 3));
        static::assertSame('БГДЖ', Str::substr('БГДЖИЛЁ', 0, 4));
        static::assertSame('Ё', Str::substr('БГДЖИЛЁ', -1, 1));
        static::assertEmpty(Str::substr('Б', 2));
    }

    public function testSubstrCount()
    {
        static::assertSame(3, Str::substrCount('laravelPHPFramework', 'a'));
        static::assertSame(0, Str::substrCount('laravelPHPFramework', 'z'));
        static::assertSame(2, Str::substrCount('laravelPHPFramework', 'l'));
        static::assertSame(1, Str::substrCount('laravelPHPFramework', 'l', 2));
        static::assertSame(0, Str::substrCount('laravelPHPFramework', 'z', 2));
        static::assertSame(1, Str::substrCount('laravelPHPFramework', 'k', -1));
        static::assertSame(1, Str::substrCount('laravelPHPFramework', 'k', -1));
        static::assertSame(1, Str::substrCount('laravelPHPFramework', 'a', 1, 2));
        static::assertSame(1, Str::substrCount('laravelPHPFramework', 'a', 1, 2));
        static::assertSame(3, Str::substrCount('laravelPHPFramework', 'a', 1, -2));
        static::assertSame(1, Str::substrCount('laravelPHPFramework', 'a', -10, -3));
    }

    public function testUcfirst()
    {
        static::assertSame('Laravel', Str::ucfirst('laravel'));
        static::assertSame('Laravel framework', Str::ucfirst('laravel framework'));
        static::assertSame('Мама', Str::ucfirst('мама'));
        static::assertSame('Мама мыла раму', Str::ucfirst('мама мыла раму'));
    }

    public function testPadBoth()
    {
        static::assertSame('__Alien___', Str::padBoth('Alien', 10, '_'));
        static::assertSame('  Alien   ', Str::padBoth('Alien', 10));
    }

    public function testPadLeft()
    {
        static::assertSame('-=-=-Alien', Str::padLeft('Alien', 10, '-='));
        static::assertSame('     Alien', Str::padLeft('Alien', 10));
    }

    public function testPadRight()
    {
        static::assertSame('Alien-----', Str::padRight('Alien', 10, '-'));
        static::assertSame('Alien     ', Str::padRight('Alien', 10));
    }

    public function testIsAbsolutePath()
    {
        static::assertTrue(Str::isAbsolutePath("C:\\Users\\ricardoboss\\test.txt"));
        static::assertTrue(Str::isAbsolutePath("/home/ricardoboss/test.txt"));
        static::assertFalse(Str::isAbsolutePath("../test.txt"));
        static::assertFalse(Str::isAbsolutePath(""));
        static::assertFalse(Str::isAbsolutePath("this is not a path"));
    }

    public function validUuidList()
    {
        return [
            ['a0a2a2d2-0b87-4a18-83f2-2529882be2de'],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1'],
            ['00000000-0000-0000-0000-000000000000'],
            ['e60d3f48-95d7-4d8d-aad0-856f29a27da2'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-21e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-31e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-41e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-51e1-9b21-0800200c9a66'],
            ['FF6F8CB0-C57D-11E1-9B21-0800200C9A66'],
        ];
    }

    public function invalidUuidList()
    {
        return [
            ['not a valid uuid so we can test this'],
            ['zf6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1' . PHP_EOL],
            ['145a1e72-d11d-11e8-a8d5-f2801f1b9fd1 '],
            [' 145a1e72-d11d-11e8-a8d5-f2801f1b9fd1'],
            ['145a1e72-d11d-11e8-a8d5-f2z01f1b9fd1'],
            ['3f6f8cb0-c57d-11e1-9b21-0800200c9a6'],
            ['af6f8cb-c57d-11e1-9b21-0800200c9a66'],
            ['af6f8cb0c57d11e19b210800200c9a66'],
            ['ff6f8cb0-c57da-51e1-9b21-0800200c9a66'],
        ];
    }
}
