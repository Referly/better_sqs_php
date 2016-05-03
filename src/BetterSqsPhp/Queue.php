<?php
namespace BetterSqsPhp;

class Queue
{
	protected $queueName;
	protected $queueUrl;
	protected $sqsQueue;
	protected $client;

	public function __construct(Client $client, $queueName)
	{
		$this->client = $client;
	}
}