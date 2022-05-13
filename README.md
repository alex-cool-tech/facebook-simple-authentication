## Facebook simple authentication

This is a Facebook authentication test app

## Application deployment steps

- generate SSL keys with `mkcert` for the normal operation of the application and the Facebook API (installation guide https://github.com/FiloSottile/mkcert)
  ```
  cd docker/nginx/certs
  mkcert -key-file ssl.key -cert-file ssl.crt localhost
  ```

- copy Docker local file (`docker/docker-compose/local/docker-compose.yml.sample`) and set up `OWNER_USER`, `OWNER_USER_ID` (run `id` in console to find out your user ID) variables

- run Docker
  ```
  docker-compose -p facebook-simple-authentication -f docker/docker-compose.yml -f docker/docker-compose/local/docker-compose.yml up -d --build
  ```

- attach to Docker CLI image
  ```
  docker-compose -p facebook-simple-authentication -f docker/docker-compose.yml -f docker/docker-compose/local/docker-compose.yml exec php-cli /bin/bash
  ```

- in Docker CLI image run
  ```
  composer i
  ```

- copy `.env` from `.env.example`, set up `FACEBOOK_API_CLIENT_ID`, `FACEBOOK_API_CLIENT_SECRET` variables (you must take this data in the developer's application)
  - Warning! Your application must be configured to allow login and run in developer mode
  - For more information visit https://developers.facebook.com/docs/facebook-login/web

- in Docker CLI image run
  ```
  php artisan key:generate
  ```

- in some cases you need to set permissions on directories, in Docker CLI image run
  ```
  chmod -R 777 storage/logs storage/framework
  ```

- now you can open https://localhost:8443/ and use app

- to stop the application run
  ```
  docker-compose -p facebook-simple-authentication -f docker/docker-compose.yml -f docker/docker-compose/local/docker-compose.yml down -v
  ```

## Tests

Attach Docker CLI and run
```
vendor/bin/phpunit
```
