<?php
namespace BetterSqsPhp;

class Message
{
	protected $sqsMessage;
	protected $client;
	protected $queue;

	public function __construct($sqsMessage, $queue, $client)
	{
		$this->sqsMessage = $sqsMessage;
		$this->queue = $queue;
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

	public function delete()
	{

	}
}