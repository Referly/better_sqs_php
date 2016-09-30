<?php

namespace BetterSqsPhp;

class Configuration
{
	protected $awsRegion;
	protected $awsCredentials;

	public function __construct()
	{
		$this->awsRegion = 'us-east-1';
		$this->awsCredentials = [];
	}

	public function getAwsRegion()
	{
		return $this->awsRegion;
	}

	public function setAwsRegion($region)
	{
		$this->awsRegion = $region;
	}

	public function hasAwsCredentials()
	{
		return count($this->awsCredentials) > 0;
	}

	public function setAwsCredentials(array $credentials = [])
	{
		$this->awsCredentials = $credentials;
	}

	public function getAwsCredentials()
	{
		return $this->awsCredentials;
	}
}