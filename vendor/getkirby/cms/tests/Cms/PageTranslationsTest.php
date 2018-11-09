<?php

namespace Kirby\Cms;

use Exception;
use Kirby\Toolkit\F;
use PHPUnit\Framework\TestCase;

class PageTranslationsTest extends TestCase
{

    public function app($language = null)
    {
        $app = new App([
            'languages' => [
                [
                    'code'    => 'en',
                    'name'    => 'English',
                    'default' => true
                ],
                [
                    'code'    => 'de',
                    'name'    => 'Deutsch'
                ]
            ],
            'site' => [
                'children' => [
                    [
                        'children' => [
                            [
                                'children' => [
                                    [
                                        'slug' => 'child',
                                        'translations' => [
                                            [
                                                'code' => 'en',
                                                'content' => [
                                                    'title' => 'Child',
                                                ]
                                            ],
                                            [
                                                'code' => 'de',
                                                'slug' => 'kind',
                                                'content' => [
                                                    'title' => 'Kind',
                                                ]
                                            ],
                                        ]
                                    ]
                                ],
                                'slug' => 'mother',
                                'translations' => [
                                    [
                                        'code' => 'en',
                                        'content' => [
                                            'title' => 'Mother',
                                        ]
                                    ],
                                    [
                                        'code' => 'de',
                                        'slug' => 'mutter',
                                        'content' => [
                                            'title' => 'Mutter',
                                        ]
                                    ],
                                ],
                            ]
                        ],
                        'slug'  => 'grandma',
                        'translations' => [
                            [
                                'code' => 'en',
                                'content' => [
                                    'title' => 'Grandma',
                                    'untranslated' => 'Untranslated'
                                ]
                            ],
                            [
                                'code' => 'de',
                                'slug' => 'oma',
                                'content' => [
                                    'title' => 'Oma',
                                ]
                            ],
                        ],
                    ]
                ],
            ],
        ]);

        if ($language !== null) {
            $app->language = $app->languages()->find($language);
        }

        return $app;
    }

    public function testUrl()
    {
        $app = $this->app();

        $page = $app->page('grandma');
        $this->assertEquals('/en/grandma', $page->url());
        $this->assertEquals('/de/oma', $page->url('de'));

        $page = $app->page('grandma/mother');
        $this->assertEquals('/en/grandma/mother', $page->url());
        $this->assertEquals('/de/oma/mutter', $page->url('de'));

        $page = $app->page('grandma/mother/child');
        $this->assertEquals('/en/grandma/mother/child', $page->url());
        $this->assertEquals('/de/oma/mutter/kind', $page->url('de'));
    }

    public function testContentInEnglish()
    {
        $page = $this->app()->page('grandma');
        $this->assertEquals('Grandma', $page->title()->value());
        $this->assertEquals('Untranslated', $page->untranslated()->value());
    }

    public function testContentInDeutsch()
    {
        $page = $this->app('de')->page('grandma');
        $this->assertEquals('Oma', $page->title()->value());
        $this->assertEquals('Untranslated', $page->untranslated()->value());
    }

    public function testSlug()
    {
        $app = $this->app();

        $this->assertEquals('grandma', $app->page('grandma')->slug());
        $this->assertEquals('grandma', $app->page('grandma')->slug('en'));
        $this->assertEquals('oma', $app->page('grandma')->slug('de'));

        $this->assertEquals('mother', $app->page('grandma/mother')->slug());
        $this->assertEquals('mother', $app->page('grandma/mother')->slug('en'));
        $this->assertEquals('mutter', $app->page('grandma/mother')->slug('de'));

        $this->assertEquals('child', $app->page('grandma/mother/child')->slug());
        $this->assertEquals('child', $app->page('grandma/mother/child')->slug('en'));
        $this->assertEquals('kind', $app->page('grandma/mother/child')->slug('de'));
    }

    public function testFindInEnglish()
    {
        $app = $this->app();
        $this->assertEquals('grandma', $app->page('grandma')->id());
        $this->assertEquals('grandma/mother', $app->page('grandma/mother')->id());
        $this->assertEquals('grandma/mother/child', $app->page('grandma/mother/child')->id());
    }

    public function testFindInDeutsch()
    {
        $app = $this->app('de');
        $this->assertEquals('grandma', $app->page('oma')->id());
        $this->assertEquals('grandma/mother', $app->page('oma/mutter')->id());
        $this->assertEquals('grandma/mother/child', $app->page('oma/mutter/kind')->id());
    }

    public function testTranslations()
    {
        $page = $this->app()->page('grandma');
        $this->assertCount(2, $page->translations());
        $this->assertEquals(['en', 'de'], $page->translations()->keys());
    }

}
