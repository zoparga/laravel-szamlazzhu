<?php


namespace zoparga\SzamlazzHu\Tests;


use zoparga\SzamlazzHu\Providers\SzamlazzHuServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('szamlazz-hu.client', [
            'credentials' => [
                'username' => 'test',
                'password' => 'test'
            ],
        ]);

    }


    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SzamlazzHuServiceProvider::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'szamlazz-hu.client'
        ];
    }


}
