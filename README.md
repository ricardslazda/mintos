### Prerequisites

- Docker >= 20.10.x

### Setup

- Spin up the Docker containers.
```sh
./vendor/bin/sail up
```

- Do a fresh database install.
```sh
./vendor/bin/sail artisan migrate:fresh --seed
```

- Create .env file.
```sh
cp .env.example .env
```

- Fill the required .env parameters.
```sh
FREE_CURRENCY_API_KEY={API_KEY}
```


### Tests
- To start the tests, use the following command:

```sh
./vendor/bin/sail artisan test
```

### Accessing the API

Email: **mintos@mintos.com**

Client Key: **secret**

1. API client has been created automatically by the seeder. For each request client key and email headers must be added:

Headers:
 ```sh
-H 'Accept: application/json' \
-H 'Content-Type: application/x-www-form-urlencoded' \
-H 'x-client-key: {KEY}' \
-H 'x-client-email: {EMAIL}'
```

2. To access accounts:

Request:
```sh
curl -X GET 'http://localhost/api/accounts' \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/x-www-form-urlencoded' \
     -H 'x-client-key: {KEY}' \
     -H 'x-client-email: {EMAIL}'
```
Response:
```sh
[{"id":1,"client_id":1,"balance":"3720.21","currency":"EUR","created_at":"2024-04-01T17:36:11.000000Z","updated_at":"2024-04-01T17:36:11.000000Z","deleted_at":null},{"id":2,"client_id":1,"balance":"4453.67","currency":"SEK","created_at":"2024-04-01T17:36:11.000000Z","updated_at":"2024-04-01T17:36:11.000000Z","deleted_at":null},{"id":3,"client_id":1,"balance":"5779.60","currency":"EUR","created_at":"2024-04-01T17:36:11.000000Z","updated_at":"2024-04-01T17:36:11.000000Z","deleted_at":null},{"id":4,"client_id":1,"balance":"9892.94","currency":"EUR","created_at":"2024-04-01T17:36:11.000000Z","updated_at":"2024-04-01T17:36:11.000000Z","deleted_at":null},{"id":5,"client_id":1,"balance":"165.32","currency":"GBP","created_at":"2024-04-01T17:36:11.000000Z","updated_at":"2024-04-01T17:36:11.000000Z","deleted_at":null}]
```

3. To transfer funds:

Request:
```sh
curl -X POST 'http://localhost/api/transactions' \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/x-www-form-urlencoded' \
     -H 'x-client-key: {KEY}' \
     -H 'x-client-email: {EMAIL}' \
     -d 'sender_account_id={SENDER_ACCOUNT_ID}' \
     -d 'recipient_account_id={RECIPIENT_ACCOUNT_ID}' \
     -d 'amount={AMOUNT}'
```

Response:
```sh
{"message":"Transfer successful."}
```
