# Installation

Before trying to install this package, make sure you are running a **Magento version greater than 2.2.0**.
This module is known to be not compatible with 2.0 or 2.1.

## Installing the whole package:

Open your SSH console and install it via composer:

```
composer require msp/module-notifier-all
bin/magento setup:upgrade
```

This will install all the core modules provided by MageSpecialist:

- `msp/module-notifier`: The basic framework
- `msp/module-notifier-core-adapters`: A set of basic communication adapters
    - Telegram
    - Slack
    - Email
- `msp/module-notifier-admin-push-adapter`: A browser push notification adapter
- `msp/module-notifier-event`: An event handler to connect any Magento event to a channel
- `msp/module-notifier-template`: Twig template manager for messages
- `msp/module-notifier-queue`: An asynchronous message dispatcher based on Magento cron

## Installing single packages

If you want to install single packages you can require just the modules you need. Composer will handle all
the dependencies.

Example:

```
composer require msp/module-notifier msp/module-notifier-template
bin/magento setup:upgrade
```

> Of course you must be aware of each module responsibility, otherwise you will probably missing some needed feature.