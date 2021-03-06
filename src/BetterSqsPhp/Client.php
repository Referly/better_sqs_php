<?php
namespace BetterSqsPhp;

use Aws\Sqs\SqsClient;
use BetterAwsPhpCore\Configuration;
class Client
{
	/**
	 * @var SqsClient
	 */
	protected $sqs;

	/**
	 * @var Configuration the configuration object
	 */
	protected $configuration;

	public function __construct(Configuration $configuration, $sqsClient = null)
	{
		$this->configuration = $configuration;
		// If no SQS Client is provided, build a new one.
		if(is_null($sqsClient)) {
			$this->sqs = $this->build_default_sqs_client();
		} else {
			$this->sqs = $sqsClient;
		}
	}

	/**
	 * @param $queueName
	 * @param $messageAsStr
	 * @return \Guzzle\Service\Resource\Model
	 */
	public function push($queueName, $messageAsStr)
	{
		return $this->sqs->sendMessage([
			'QueueUrl' => $this->urlForQueue($queueName),
			'MessageBody' => $messageAsStr,
		]);
	}

	/**
	 * @param string $queueName
	 * @return mixed|null
	 */
	public function reserve($queueName)
	{
		$resp = $this->sqs->receiveMessage([
			'QueueUrl' => $this->urlForQueue($queueName),
			'MaxNumberOfMessages' => 1,
		]);
		if($this->canReadMessage($resp)) {
			return new Message($sqsMessage = $resp['Messages'][0], $queue = $queueName, $queueClient = $this);
		} else {
			return null;
		}
	}

	/**
	 * @param Message $message
	 * @return \Guzzle\Service\Resource\Model
	 */
	public function delete(Message $message)
	{
		return $this->sqs->deleteMessage([
			'QueueUrl' => $this->urlForQueue($message->queueName()),
			'ReceiptHandle' => $message->receiptHandle(),
		]);
	}

	public function sqs()
	{
		return $this->sqs;
	}

	public function getSqs()
	{
		return $this->sqs();
	}

	/**
	 * @param string $queueName
	 * @return string
	 */
	public function urlForQueue($queueName)
	{
		$result = $this->sqs->createQueue([
			'QueueName' => $queueName,
		]);
		return $result->get('QueueUrl');
	}
	/**
	 * @return SqsClient
	 *
	 * Creates a sensible default AWS\Sqs\SqsClient instance
	 */
	protected function build_default_sqs_client()
	{
		$config = [
			'region'  => $this->configuration->getAwsRegion()
		];
		if($this->configuration->hasAwsCredentials()) {
			$config['credentials'] = $this->configuration->getAwsCredentials();
		};
		return SqsClient::factory($config);
	}

	protected function canReadMessage($sqsResponse)
	{
		return isset($sqsResponse['Messages']) && is_array($sqsResponse['Messages']) && count($sqsResponse['Messages']) > 0;
	}
}