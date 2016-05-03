<?php
namespace BetterSqsPhp;

use Aws\Sqs\SqsClient;

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

	public function reserve($queueName, $maxNumberOfMessages = 1)
	{

	}

	public function delete(Message $message)
	{

	}

	public function sqs()
	{
		return $this->sqs;
	}

	public function getSqs()
	{
		return $this->sqs();
	}

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
		return SqsClient::factory([
			'region'  => $this->configuration->getAwsRegion(),
		]);
	}
}