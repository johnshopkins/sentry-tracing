# Sentry Transactions

A wrapper library for implementing [custom instrumentation](https://docs.sentry.io/platforms/php/performance/instrumentation/custom-instrumentation/) of [Sentry performance monitoring](https://docs.sentry.io/product/performance/).

## Installation

```bash
composer require johnshopkins/sentry-tracing
```

## Basic usage

```php

use SentryTracing\Span;
use SentryTracing\Transaction;

require __DIR__ . '/vendor/autoload.php';

// creates and starts a transaction
$transaction = new Transaction('transaction name', 'http.server');

// record a span
$span = new Span('db.sql.execute');

// run a database query

// end and record the span
$span->end();

// record another span
$span = new Span('template.render');

// render the template

$span->end();


// end the transaction and send to sentry
$transaction->end();
```

## Connecting services

For traces that begin in the backend, connect the front end `pageload` transaction using `meta` tags.

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
