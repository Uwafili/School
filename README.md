# School
time

## Order workflow queue

Store rejections and customer cancellations enqueue Paystack refunds. The database queue is the default, so keep a worker running in deployments:

```sh
php artisan queue:work
```

The local `composer dev` script starts a queue listener alongside the web server. If Paystack accepts a refund but its final status is still pending, verify it in the Paystack dashboard before attempting any retry.
