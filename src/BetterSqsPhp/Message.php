<?php
namespace BetterSqsPhp;

class Message
{
	/**
	 * @var string
	 */
	protected $sqsMessage;

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var string the name of the queue where the message originated
	 */
	protected $queueName;

	public function __construct($sqsMessage, $queueName, Client $client)
	{
		$this->sqsMessage = $sqsMessage;
		$this->queueName = $queueName;
		$this->client = $client;
	}

	public function messageBody()
	{
		return $this->sqsMessage['Body'];
	}

	public function body()
	{
		return $this->messageBody();
	}

	public function receiptHandle()
	{
		return $this->sqsMessage['ReceiptHandle'];
	}

	public function queueName()
	{
		return $this->queueName;
	}

	public function delete()
	{
		$this->client->delete($this);
	}
}