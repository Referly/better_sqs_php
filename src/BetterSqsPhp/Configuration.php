<?php

namespace BetterSqsPhp;

class Configuration
{
	protected $awsRegion;

	public function getAwsRegion()
	{
		return $this->awsRegion;
	}

	public function setAwsRegion($region)
	{
		$this->awsRegion = $region;
	}
}