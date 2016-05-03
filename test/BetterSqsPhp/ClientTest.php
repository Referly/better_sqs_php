<?php

namespace Test\BetterSqsPhp;

use BetterSqsPhp\Client;
use BetterSqsPhp\Configuration;
use PHPUnit_Framework_TestCase;
use Aws\Sqs\SqsClient;

class ClientTest extends PHPUnit_Framework_TestCase
{
	protected $client;
	protected $configuration;

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
		$sqs_client = "a string that is pretendign to be an sqs client";

		$this->client = new Client($this->configuration, $sqs_client);

		$this->assertEquals($sqs_client, $this->client->getSqs());
	}
}