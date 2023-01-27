<?php

namespace SentryTracing;

class Span
{
  /**
   * @var \Sentry\Tracing\Span|null
   */
  protected $parent;

  /**
   * @var \Sentry\Tracing\SpanContext
   */
  protected $context;

  /**
   * @var \Sentry\Tracing\Span
   */
  protected \Sentry\Tracing\Span $span;

  /**
   * Create and start a Sentry span
   * @param string $operation   Operation name (see: https://develop.sentry.dev/sdk/performance/span-operations/)
   * @param string $description Description
   */
  public function __construct(string $operation, string|null $description = null)
  {
    $this->parent = \Sentry\SentrySdk::getCurrentHub()->getSpan();

    if ($this->parent) {
      $this->context = new \Sentry\Tracing\SpanContext();
      $this->context->setOp($operation);

      if ($description) {
        $this->context->setDescription($description);
      }

      // start the span
      $this->span = $this->parent->startChild($this->context);

      // set the current span to the span we just started
      \Sentry\SentrySdk::getCurrentHub()->setSpan($this->span);
    }
  }

  /**
   * Returns a string that can be used for the `sentry-trace` header and meta tag.
   * @return string
   */
  public function getTraceId(): string
  {
    return (string) $this->span->toTraceparent();
  }

  /**
   * Returns a string that can be used for the `baggage` header and meta tag.
   * @return string
   */
  public function getBaggage(): string
  {
    return $this->span->toBaggage();
  }

  public function end(): void
  {
    if (!$this->parent) {
      return;
    }

    $this->span->finish();

    // Restore the current span back to the parent span
    \Sentry\SentrySdk::getCurrentHub()->setSpan($this->parent);
  }
}
