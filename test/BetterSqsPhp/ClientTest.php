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
	protected $queueUrl;

	public function setUp()
	{
		$this->queueName = 'abracadabra';
		$this->queueUrl = 'sqs://someQueueUrl';
		$this->configuration = new Configuration;
	}

	public function tearDown()
	{

	}

	protected function getSqsClient()
	{
		$sqsClient = $this->getMockBuilder('SqsClient')
			->disableOriginalConstructor()
			->setMethods([
				'createQueue',
				'sendMessage',
				'receiveMessage',
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
			->willReturn($this->queueUrl);

		$sqsClient->expects($this->any())
			->method('createQueue')
			->willReturn($createQueueResult);

		return $sqsClient;
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
			->willReturn($this->queueUrl);

		$this->sqs->expects($this->any())
			->method('createQueue')
			->willReturn($createQueueResult);

		$this->sqs->expects($this->once())
			->method('sendMessage')
			->with([
				'QueueUrl' => $this->queueUrl,
				'MessageBody' => $messageBody,
			]);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->client->push($this->queueName, $messageBody);
	}

	public function testReserveAsksSqsForOneMessage()
	{
		$this->sqs = $this->getSqsClient();

		$this->sqs->expects($this->once())
			->method('receiveMessage')
			->with([
				'QueueUrl' => $this->queueUrl,
				'MaxNumberOfMessages' => 1,
			]);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->client->reserve($this->queueName);
	}

	public function testReserveIsNullIfSqsReturnsNonArray()
	{
		$this->sqs = $this->getSqsClient();

		$this->sqs->expects($this->any())
			->method('receiveMessage')
			->willReturn('notAnArray');

		$this->client = new Client($this->configuration, $this->sqs);

		$this->assertNull($this->client->reserve($this->queueName));
	}

	public function testReserveIsNullIfSqsReturnsArrayWithoutMessagesKey()
	{
		$this->sqs = $this->getSqsClient();

		$this->sqs->expects($this->any())
			->method('receiveMessage')
			->willReturn([]);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->assertNull($this->client->reserve($this->queueName));
	}

	public function testReserveIsNullIfSqsReturnsArrayWithMessagesKeyHavingNonArrayValue()
	{
		$this->sqs = $this->getSqsClient();

		$this->sqs->expects($this->any())
			->method('receiveMessage')
			->willReturn(['Messages' => 12345]);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->assertNull($this->client->reserve($this->queueName));
	}

	public function testReserveIsNullIfSqsReturnsZeroMessages()
	{
		$this->sqs = $this->getSqsClient();

		$this->sqs->expects($this->any())
			->method('receiveMessage')
			->willReturn(['Messages' => []]);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->assertNull($this->client->reserve($this->queueName));
	}

	public function testReserveIsAppropriateMessageInstanceIfSqsReturnsMessage()
	{
		$this->sqs = $this->getSqsClient();

		$this->sqs->expects($this->any())
			->method('receiveMessage')
			->willReturn(['Messages' => [
				[
					'Body' => 'READ THIS MESSAGE',
					'ReceiptHandle' => '1eee',
				]
			]]);

		$this->client = new Client($this->configuration, $this->sqs);

		$message = $this->client->reserve($this->queueName);

		$this->assertInstanceOf('BetterSqsPhp\Message', $message);
		$this->assertEquals('READ THIS MESSAGE', $message->body());
		$this->assertEquals('1eee', $message->receiptHandle());
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
		$this->queueUrl = 'sqs://someQueueUrl';

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
			->willReturn($this->queueUrl);

		$this->sqs->expects($this->any())
			->method('createQueue')
			->willReturn($createQueueResult);

		$this->client = new Client($this->configuration, $this->sqs);

		$this->assertEquals($this->queueUrl, $this->client->urlForQueue($this->queueName));
	}
}