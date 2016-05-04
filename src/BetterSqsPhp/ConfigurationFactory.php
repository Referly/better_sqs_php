<?php
namespace BetterSqsPhp;

class ConfigurationFactory
{
	public function createConfiguration(array $config = [])
	{
		$configuration = new Configuration;
		if(isset($config['region'])) $configuration->setAwsRegion($config['region']);
		return $configuration;
	}
}