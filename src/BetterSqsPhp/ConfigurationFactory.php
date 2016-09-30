<?php
namespace BetterSqsPhp;

class ConfigurationFactory
{
	public function createConfiguration(array $config = [])
	{
		$configuration = new Configuration;
		if(isset($config['region'])) $configuration->setAwsRegion($config['region']);
		if(isset($config['credentials'])) $configuration->setAwsCredentials($config['credentials']);
		return $configuration;
	}
}