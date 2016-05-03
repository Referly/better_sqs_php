<?php

namespace Test\BetterSqsPhp;

use BetterSqsPhp\Client;
use BetterSqsPhp\Configuration;
use PHPUnit_Framework_TestCase;
use Aws\Sqs\SqsClient;
use Guzzle\Service\Resource\Model;

class ClientTest extends PHPUnit_Framework_TestCase
{
	protected $client;
	protected $configuration;
	protected $sqs;

	public function setUp()
	{
		$this->configuration = new Configuration;
	}

	public function tearDown()
	{

	}

	public function testConstructWhenSqsClientIsNullBuildsDefaultSqsClient()
	{
		$this->client = new Client($this->configuration, null);

		$expected = SqsClient::factory([
			'region'  => $this->configuration->getAwsRegion(),
		]);

		$this->assertEquals($expected, $this->client->getSqs());
	}

	public function testConstructWhenSqsClientIsProvidedItIsUsed()
	{
		$this->sqs = "a string that is pretendign to be an sqs client";

		$this->client = new Client($this->configuration, $this->sqs);

		$this->assertEquals($this->sqs, $this->client->getSqs());
	}

	public function testUrlForQueueCallsSqsCreateQueueWithQueueName()
	{
		$queueName = 'abracadabra';

		$this->sqs = $this->getMockBuilder('SqsClient')
			->disableOriginalConstructor()
			->setMethods([
				'createQueue',
			])
			->getMock();

		$createQueueResult = $this->getMockBuilder('Model')
			->disableOriginalConstructor()
			->setMethods([
				'get'
			])
			->getMock();

		$createQueueResult->expects($this->once())
			->method('get')
			->with('QueueUrl');

		$this->sqs->expects($this->once())
			->method('createQueue')
			->with(['QueueName' => $queueName])
			->willReturn($createQueueResult);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->client->urlForQueue($queueName);
	}

	public function testUrlForQueueReturnsQueueUrl()
	{

	}
}