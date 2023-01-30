# Sentry Transactions

A wrapper library for implementing [custom instrumentation](https://docs.sentry.io/platforms/php/performance/instrumentation/custom-instrumentation/) of [Sentry performance monitoring](https://docs.sentry.io/product/performance/).

## Installation

```bash
composer require johnshopkins/sentry-tracing
```

## Basic usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

// create and start a transaction
$transaction = new SentryTracing\Transaction('transaction name', 'operation.name');

// record a span
$span = new SentryTracing\Span('operation.name');
// do some stuff...
$span->end();

// record another span
$span = new SentryTracing\Span('operation.name');
// do some more stuff...
$span->end();


// end the transaction and send to sentry
$transaction->end();
```
### Using the `SENTRY_TRACE` constant
To easily enable or disable tracing, define the `SENTRY_TRACE` before creating any transactions or spans. If the constant is not defined, it will default to `true`. The advantage of using this constant is that if at any point, you wish to turn off tracing, you can toggle the constant instead of removing all references to transactions and spans within your code base.
```php
// enable tracing
const SENTRY_TRACE = true;
```

## Connecting services

For traces that begin in the backend, connect the front end `pageload` transaction using [meta tags](https://docs.sentry.io/platforms/javascript/performance/connect-services/#pageload).

```html
<html>
  <head>
    <meta name="sentry-trace" content="<?= $span->getTraceId() ?>" />
    <meta name="baggage" content="<?= $span->getBaggage() ?>" />
    <!-- ... -->
  </head>
</html>
```

The span referenced should be the one that generates the HTML.
