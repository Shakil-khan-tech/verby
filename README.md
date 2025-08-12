# Metronic 7 + Laravel 8
v1

### Introduction

...

### Installation

Laravel has a set of requirements in order to ron smoothly in specific environment. Please see [requirements](https://laravel.com/docs/7.x#server-requirements) section in Laravel documentation.

Trace similarly uses additional plugins and frameworks, so ensure You have [Composer](https://getcomposer.org/) and [Node](https://nodejs.org/) installed on Your machine.

Assuming your machine meets all requirements - let's process to installation of Trace.

(tested with node v14.8.0)

1. Open in cmd or terminal app and navigate to this folder
2. Run following commands

```bash
composer install
```

```bash
cp .env.example .env
```

```bash
php artisan migrate:refresh --seed
```

```bash
php artisan passport:client --personal
```

```bash
php artisan passport:keys
```

The following is required on local development
```bash
php artisan key:generate
```

```bash
npm install
```

```bash
npm run dev
```

```bash
php artisan serve
```

```bash
local: sudo service apache2 restart
when changing max_input_vars in public/.user.ini
```

```bash
to fix form:
pdftk arbeitslosenversicherung.pdf output fixed.pdf
```

```bash
to make public lang script files:
php artisan lang:js
```

And navigate to generated server link (http://127.0.0.1:8000)

### Installation
Modify bootstrap.css & bootstrap.min.css for background colors in print, remove line ~ 10278:
```bash
./node_modules/bootstrap/dist/css/bootstrap.css
```
```bash
.table td,
.table th {
  background-color: #fff !important;
}
```

### Copyright

...
