# EasyVHost

Apache Virtual Hosts manager

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate --ansi
```

### Database

```php artisan migrate```

### Frontend

```bash
npm install && npm run dev
npm run dev #again
```

## Virtual Host Modifiers

Check [documentation](modifiers/README.md).