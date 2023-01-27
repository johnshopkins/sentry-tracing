<?php

namespace SentryTracing;

class Transaction
{
  /**
   * @var \SentryTracing\Transaction
   */
  protected $transaction;

  /**
   * Create a Sentry transaction
   * @param string $name      Transaction name
   * @param string $operation Operation name (see: https://develop.sentry.dev/sdk/performance/span-operations/)
   */
  public function __construct(string $name, string $operation)
  {
    $transactionContext = new \Sentry\Tracing\TransactionContext();
    $transactionContext->setName( $name);
    $transactionContext->setOp($operation);

    $this->transaction = \Sentry\startTransaction($transactionContext);

    // set the current transaction as the current span so we can retrieve it later
    \Sentry\SentrySdk::getCurrentHub()->setSpan($this->transaction);
  }

  public function end(): void
  {
    $this->transaction->finish();
  }
}
