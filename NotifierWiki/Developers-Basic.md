# For Developers: Basic

`msp/module-notifier` comes with a wide set of SPI and API for integration in 3rd party components or features extensions.

## Programmatically sending messages

It may happens you need a direct interface to the lowest level mechanism of **MSP Notifier to directly send a message
for your custom code**.
In this case you may use `MSP\NotifierApi\Api\SendMessageInterface` interface by calling its method `execute`.
 
```
...
public function __construct(MSP\NotifierApi\Api\SendMessageInterface $sendMessage)
{
    $this->sendMessage = $sendMessage;
}
...
public function execute()
{
    ... // Your code
    try {
        $this->sendMessage->execute('my_channel_code', 'Hello world!');
    } catch (\Exception $e) {
        // Do error management here... maybe your channel does not exist?
    }
    ... // Your code
}
``` 

> In the previous example we assume the channel `my_channel_code` exists. If you do not know how to configure a channel,
please refer to [Channels section](./Fundamentals#channels).

## Sending a message from console

A command line interface is provided to easily integrate this framework from the sysadmin point of view:

`bin/magento msp:notifier:send my_channel_code "Hello world!"`
