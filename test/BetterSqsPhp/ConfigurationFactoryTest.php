<?php

namespace Test\BetterSqsPhp;

use BetterSqsPhp\ConfigurationFactory;
use PHPUnit_Framework_TestCase;

class ConfigurationFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $factory;

	public function setUp()
	{
		$this->factory = new ConfigurationFactory;
	}

	public function tearDown()
	{

	}

	public function testCreateConfigurationWithSpecifiedRegionUsesSpecifiedRegion()
	{
		$configuration = $this->factory->createConfiguration(['region' => 'us-west-2']);
		$this->assertEquals('us-west-2', $configuration->getAwsRegion());
	}

	public function testCreateConfigurationWithoutSpecifiedRegionUsesDefaultRegion()
	{
		$configuration = $this->factory->createConfiguration([]);
		$this->assertEquals('us-east-1', $configuration->getAwsRegion());
	}

}