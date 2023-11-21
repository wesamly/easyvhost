# EasyVHost

Apache Virtual Hosts manager with the following features:

- Manage hosts using a web browser.
- Save hosts to database.
- Predefined directives.
- Use tags for hosts.
- Easily search hosts.
- Setting for virtual host file path with support to define a file per tag.

**Technology**

Laravel PHP Framework, with Vuejs for frontend, and MySQL Database.

**Status**

Beta

> **Warning**
> Work in progress, always backup your virtual host config files.

## Dependencies

- PHP >= 8.1 (For detailed extensions check [Laravel 10 Deployment](https://laravel.com/docs/10.x/deployment#server-requirements))
- Apache Web Server (for local development)
- MySQL 5.7+, MariaDB 10.10+, or SQLite 3.8.8+

## Installation

Use `git clone` or download files, then run the following commands:

```bash
composer install
cp .env.example .env
php artisan key:generate --ansi
```

### Database

#### MySQL

Create a database using your database manager, e.g. phpMyAdmin.

In `.env` file update database variables:

```.env
DB_DATABASE=evhost_db
DB_USERNAME=root
DB_PASSWORD=
```
#### SQLite

Create file `database.sqlite` under */database*

In `.env` file update database variables:

```.env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/easyvhost/database/database.sqlite
```

Run the following command to create database tables:

```php artisan migrate```

### Frontend

Run the following commands:

```bash
npm install
npm run dev
```

## Virtual Host Modifiers

Check [documentation](modifiers/README.md).

## Getting help

If you have questions, concerns, bug reports, etc, please file an issue in this repository's Issue Tracker.

## License

EasyVHost is released under the [MIT License](https://opensource.org/licenses/MIT).