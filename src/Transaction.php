<?php

namespace SentryTracing;

class Transaction
{
  /**
   * @var bool
   */
  protected $tracingEnabled;

  /**
   * @var \SentryTracing\Transaction
   */
  protected $transaction;

  protected $canceled = false;

  /**
   * Create a Sentry transaction
   * @param string $name      Transaction name
   * @param string $operation Operation name (see: https://develop.sentry.dev/sdk/performance/span-operations/)
   */
  public function __construct(string $name, string $operation)
  {
    $this->tracingEnabled = defined('SENTRY_TRACE') ? SENTRY_TRACE : true;

    if (!$this->tracingEnabled) {
      return;
    }

    $transactionContext = new \Sentry\Tracing\TransactionContext();
    $transactionContext->setName( $name);
    $transactionContext->setOp($operation);

    $this->transaction = \Sentry\startTransaction($transactionContext);

    // set the current transaction as the current span so we can retrieve it later
    \Sentry\SentrySdk::getCurrentHub()->setSpan($this->transaction);
  }

  public function cancel(): void
  {
    $this->canceled = true;
  }

  /**
   * Send the transaction to Sentry
   * @return \Sentry\EventId|null
   */
  public function end(): \Sentry\EventId|null
  {
    if ($this->canceled) {
      // dont' send this transaction to sentry
      return null;
    }

    return $this->transaction->finish();
  }

  /**
   * Returns a string that can be used for the `sentry-trace` header and meta tag.
   * @return string
   */
  public function getTraceId(): string
  {
    if (!$this->tracingEnabled) {
      return '';
    }

    return (string) $this->transaction->toTraceparent();
  }

  /**
   * Returns a string that can be used for the `baggage` header and meta tag.
   * @return string
   */
  public function getBaggage(): string
  {
    if (!$this->tracingEnabled) {
      return '';
    }

    return $this->transaction->toBaggage();
  }

  /**
   * Gets the URI of the current request
   * @param $trimTrailingSlash
   * @return string
   */
  public static function getUri($trimTrailingSlash = false): string
  {
    $uriFragments = explode('?', $_SERVER['REQUEST_URI']);
    return $trimTrailingSlash ?
      rtrim($uriFragments[0], '/') :
      $uriFragments[0];
  }
}
