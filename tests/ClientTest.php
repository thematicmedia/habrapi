<?php

namespace Habrahabr\Tests\Api;

use Habrahabr\Api\Client;
use Habrahabr\Tests\Api\HttpAdapter\MockAdapter;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var Client */
    protected $client;

    protected function setUp()
    {
        $adapter = new MockAdapter();
        $this->client = new Client($adapter);
    }

    /**
     * @dataProvider resourcesProvider
     */
    public function testGetResource($name, $expected)
    {
        $resource = call_user_func([$this->client, 'get' . $name . 'Resource']);
        $this->assertInstanceOf($expected, $resource);
    }

    /**
     * @expectedException \Habrahabr\Api\Exception\ResourceNotExistsException
     */
    public function testGetResourceFail()
    {
        $this->client->getFoobarResource();
    }

    /**
     * @expectedException \Habrahabr\Api\Exception\ResourceNotExistsException
     */
    public function testMethodFail()
    {
        $this->client->getFoobar();
    }

    public function resourcesProvider()
    {
        return [
            ['User', 'Habrahabr\Api\Resources\UserResource'],
            ['Search', 'Habrahabr\Api\Resources\SearchResource'],
            ['Post', 'Habrahabr\Api\Resources\PostResource'],
            ['Hub', 'Habrahabr\Api\Resources\HubResource'],
            ['Feed', 'Habrahabr\Api\Resources\FeedResource'],
            ['Company', 'Habrahabr\Api\Resources\CompanyResource'],
            ['Comments', 'Habrahabr\Api\Resources\CommentsResource'],
            ['Tracker', 'Habrahabr\Api\Resources\TrackerResource'],
        ];
    }
}