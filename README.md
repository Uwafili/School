# School
time

## Order workflow queue

Store rejections and customer cancellations enqueue Paystack refunds. The database queue is the default, so keep a worker running in deployments:

```sh
php artisan queue:work
```

The local `composer dev` script starts a queue listener alongside the web server. If Paystack accepts a refund but its final status is still pending, verify it in the Paystack dashboard before attempting any retry.

## Order and delivery setup

Apply the order-history, rider-assignment, and Laravel notification schema updates with:

```sh
php artisan migrate
```

Online checkout and refunds require `PAYSTACK_SECRET_KEY`. Database notifications are enabled by default. To enable real-time broadcasts, configure `BROADCAST_CONNECTION=pusher` and set `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, and `PUSHER_APP_CLUSTER`; `PUSHER_HOST`, `PUSHER_PORT`, and `PUSHER_SCHEME` are optional overrides.

Run the end-to-end ordering, messaging, delivery, authorization, cancellation, and failure workflow tests with:

```sh
php artisan test
```
