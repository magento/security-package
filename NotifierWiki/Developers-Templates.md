# For Developers: Templates

## Programmatically sending template based messages

If you need to send a message from your custom code using a template, you can use `MSP\NotifierTemplateApi\Api\SendMessageInterface`.

This interface is an extended version of `MSP\NotifierApi\Api\SendMessageInterface` allowing you to specify a template and a set of parameters instead of a simple message.

Usage example:

```
...
public function __construct(MSP\NotifierTemplateApi\Api\SendMessageInterface $sendMessage)
{
    $this->sendMessage = $sendMessage;
}
...
public function execute()
{
    ... // Your code
    try {
        $this->sendMessage->execute('my_channel_code', 'my_template_id', ['param1' => 'Hello', 'param2' => 'World!']);
    } catch (\Exception $e) {
        // Do error management here...
    }
    ... // Your code
}
``` 

If you defined `my_template_id` as:

```
Here is my amazing message: {{ param1 }} {{ param2 }}

I sent it from {{ _store.getName() }}
```

You will get:

```
Here is my amazing message: Hello World!

I sent it from My Store
```

> Check [here](./Templates) to see how to define a template from backend

## Variables

As you can easily imagine, variables in the TWIG template are provided by the 3rd parameter in the `execute` prototype:

```
public function execute(string $channelCode, string $template, array $params = []): bool;
```

As you may have noticed in the previous example, the variable `_store` was not passed by `$params`.
This is because `MSP_NotifierTemplate` provides an injectable mechanism for global variables.

If you inspect `MSP\NotifierTemplate\Model\VariablesDecorator\CoreVariables` (click [here](//github.com/magespecialist/notifier-template/blob/master/Model/VariablesDecorator/CoreVariables.php) for source code),
you will see how we are injecting global variables.

### Adding a new variable decorator

If you need to add new global variables you must:

- Define a variable decorator class implementing `MSP\NotifierTemplate\Model\VariablesDecorator\VariablesDecoratorInterface`
- Inject variables from `execute` method
- Register your new decorator class in `MSP\NotifierTemplate\Model\VariablesDecorator\VariablesDecoratorChain` through `di.xml`

Example of DI registration:

```
<type name="MSP\NotifierTemplate\Model\VariablesDecorator\VariablesDecoratorChain">
    <arguments>
        <argument name="decorators" xsi:type="array">
            <item name="my_custom_decorator"
                  xsi:type="object">My\Module\Model\VariablesDecorator\MyDecorator</item>
        </argument>
    </arguments>
</type>
```

## Adding a new System Template

A `System Template` is a template included in your module. You may prefer this approach if
you want to provide a specific template bundled with your code.

`System Templates` can be defined and overridden through an XML file called `msp_notifier_template.xml` in your `etc` folder.

Definition example:

```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:MSP_NotifierTemplate:etc/msp_notifier_templates.xsd">

    <templates>
        <template id="my_template_id"
                  label="My awesome template"
                  file="My_Module::my_template.twig" />
    </templates>
</config>
```

As you may see, each template has **one identifier** (must be unique), **one descriptive name** and a TWIG file.

Your twig file must be placed under `msp_notifier` of your module's root directory.

### Event specific templates

If you are using `msp/module-notifier-event` and you are defining an event specific template, you should use a conventional
id in the form of: `event:event_name`.

Example:

**Template for event `backend_auth_user_login_failed`**:

```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:MSP_NotifierTemplate:etc/msp_notifier_templates.xsd">

    <templates>
        ...
        <template id="event:backend_auth_user_login_failed"
                  label="Admin login failed"
                  file="My_Module::backend_auth_user_login_failed.twig" />
        ...
    </templates>
</config>
```

> click [here](//github.com/magespecialist/notifier-event/blob/master/etc/msp_notifier_templates.xml) for a working example.
