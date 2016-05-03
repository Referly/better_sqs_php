<?php

namespace BetterSqsPhp;

class Configuration
{
	protected $awsRegion;

	public function __construct()
	{
		$this->awsRegion = 'us-east-1';
	}

	public function getAwsRegion()
	{
		return $this->awsRegion;
	}

	public function setAwsRegion($region)
	{
		$this->awsRegion = $region;
	}
}