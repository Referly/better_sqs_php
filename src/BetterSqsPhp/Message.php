<?php
namespace BetterSqsPhp;

class Message
{
	public function message_body()
	{

	}

	public function body()
	{
		return $this->message_body();
	}

	public function delete()
	{

	}
}