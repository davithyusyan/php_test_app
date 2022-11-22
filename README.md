## Установка

```shell
# Копируем файл конфигурации
$ cp .env.example .env

# Открываем в редакторе
$ vim .env

# Изменяем следующие строки для настройки базы данных
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_app
DB_USERNAME=root
DB_PASSWORD=

# Установка пакетов
$ composer install

# Генерация ключа приложения
$ php artisan key:generate

# Запускаем миграцию базы данных + настройка пакета для авторизации
$ php artisan migrate
$ php artisan passport:install --uuids
$ php artisan db:seed

# Запускаем сервер
$ php artisan serve
```
### Тут список всех API запросов для тестирования.

Перед тестированием отправьте запрос на «Auth - Login», чтобы получить токены access/refresh и установить все переменные в глобальной среде почтальона.

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/2803724-1aaee9f6-a599-4cf5-b457-927255abd32a?action=collection%2Ffork&collection-url=entityId%3D2803724-1aaee9f6-a599-4cf5-b457-927255abd32a%26entityType%3Dcollection%26workspaceId%3Dbe781ae3-7faa-471d-ab5f-62edac5e442b)
