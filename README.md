# better_sqs_php
Better SQS API for PHP

# Usage

## Get a client

```php
$configuration = new Configuration;
$client = new Client($configuration);
```

## Enqueue a message

```php
$queueName = 'someSqsQueueName';
$client->push($queueName, 'can you see this amazing message?');
```

## Reserve a message

```php
$queueName = 'someSqsQueueName';
$message = $client->reserve($queueName);
echo "The message is {$message->body()} with receipt {$message->receiptHandle()}";
```

## Delete a reserved message

```php
$message->delete();
```

Note that reserved messages will regain visibility after a certain amount of time (see visibility timeout settings for
SQS). Thus explicitly returning an unprocessed message to the queue is not necessary.