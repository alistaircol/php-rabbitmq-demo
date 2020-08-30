Brief interactive demo with RabbitMQ and PHP publisher/producer and subscriber/consumer.

For more background: https://ac93.uk/articles/php-message-pubsub-with-rabbitmq/

Go to http://localhost:15672/ for the admin panel and login with `guest:guest`.

Fire up stack:

```bash
docker-compose up -d
```

Install dependencies:

```bash
docker exec -i -u $(id -u) -w /var/www/html/app ac_crm composer install
```

Publish a message:

```bash
docker exec -it -u $(id -u) -w /var/www/html/app ac_crm ./producer
```

Consume a message:

```bash
docker exec -it -u $(id -u) -w /var/www/html/app ac_crm ./consumer
```

For non-interactive, `git checkout non-interactive`.
