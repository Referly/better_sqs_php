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
	protected $queueName;

	public function setUp()
	{
		$this->queueName = 'abracadabra';
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

	public function testPushSendsMessageToSqs()
	{
		$queueUrl = 'sqs://someQueueUrl';
		$messageBody = 'please read this important message.';

		$this->sqs = $this->getMockBuilder('SqsClient')
			->disableOriginalConstructor()
			->setMethods([
				'createQueue',
				'sendMessage'
			])
			->getMock();

		$createQueueResult = $this->getMockBuilder('Model')
			->disableOriginalConstructor()
			->setMethods([
				'get'
			])
			->getMock();

		$createQueueResult->expects($this->any())
			->method('get')
			->willReturn($queueUrl);

		$this->sqs->expects($this->any())
			->method('createQueue')
			->willReturn($createQueueResult);

		$this->sqs->expects($this->once())
			->method('sendMessage')
			->with([
				'QueueUrl' => $queueUrl,
				'MessageBody' => $messageBody,
			]);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->client->push($this->queueName, $messageBody);
	}

	public function testUrlForQueueCallsSqsCreateQueueWithQueueName()
	{

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
			->with(['QueueName' => $this->queueName])
			->willReturn($createQueueResult);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->client->urlForQueue($this->queueName);
	}

	public function testUrlForQueueReturnsQueueUrl()
	{
		$queueUrl = 'sqs://someQueueUrl';

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

		$createQueueResult->expects($this->any())
			->method('get')
			->willReturn($queueUrl);

		$this->sqs->expects($this->any())
			->method('createQueue')
			->willReturn($createQueueResult);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->assertEquals($queueUrl, $this->client->urlForQueue($this->queueName));
	}
}