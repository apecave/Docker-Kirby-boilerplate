<?php

namespace Kirby\Cms;

class RouterTest extends TestCase
{

    protected $app;

    public function setUp()
    {
        $this->app = new App([
            'roots' => [
                'index' => '/dev/null'
            ],
            'site' => [
                'children' => [
                    [
                        'slug' => 'home',
                    ],
                    [
                        'slug' => 'projects',
                        'children' => [
                            [
                                'slug'  => 'project-a',
                                'files' => [
                                    [
                                        'filename' => 'cover.jpg'
                                    ]
                                ]
                            ],
                        ],
                    ]
                ],
                'files' => [
                    [
                        'filename' => 'background.jpg'
                    ]
                ]
            ],
        ]);
    }

    public function testHomeRoute()
    {
        $page = $this->app->call('');
        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('home', $page->id());
    }

    public function testPageRoute()
    {
        $page = $this->app->call('projects');
        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('projects', $page->id());
    }

    public function testPageFileRoute()
    {
        $file = $this->app->call('projects/project-a/cover.jpg');
        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('projects/project-a/cover.jpg', $file->id());
    }

    public function testSiteFileRoute()
    {
        $file = $this->app->call('background.jpg');
        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('background.jpg', $file->id());
    }

    public function testNestedPageRoute()
    {
        $page = $this->app->call('projects/project-a');
        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('projects/project-a', $page->id());
    }

    public function testNotFoundRoute()
    {
        $page = $this->app->call('not-found');
        $this->assertNull($page);
    }

    public function testPageMediaRoute()
    {
        $response = $this->app->call('media/pages/projects/project-a/cover.jpg');
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSiteMediaRoute()
    {
        $response = $this->app->call('media/site/background.jpg');
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testUserMediaRoute()
    {
        $this->markTestIncomplete();
    }

    public function testDisabledApi()
    {
        $app = $this->app->clone([
            'options' => [
                'api' => false
            ]
        ]);

        $this->assertNull($app->call('api'));
        $this->assertNull($app->call('api/something'));
    }

    public function testDisabledPanel()
    {
        $app = $this->app->clone([
            'options' => [
                'panel' => false
            ]
        ]);

        $this->assertNull($app->call('panel'));
        $this->assertNull($app->call('panel/something'));
    }

    public function testPluginAssets()
    {
        $this->markTestIncomplete();
    }

    public function testPluginIndexJsAndCss()
    {
        $this->markTestIncomplete();
    }

}
