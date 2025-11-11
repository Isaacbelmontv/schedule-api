# Schedule API

A RESTful API for managing schedules

## Features

- Create, read, update, and delete schedules

## Requirements

- PHP 8.2 or higher
- Composer
- Symfony
- Docker

## Installation

Install dependencies:

```bash
composer install
```

Configure your environment:

- Copy `.env.example` to `.env`
- Update database configuration in `.env`

## Run with Docker

Start the API and PostgreSQL using Docker Compose:

```bash
docker compose up -d --build
```

Run database migrations inside the API container:

```bash
docker compose exec api php bin/console doctrine:migrations:migrate --no-interaction
```

- API: [http://localhost:8000](http://localhost:8000)

## API Endpoints

Postman Collection:
[URL](https://full-stack-team-8726.postman.co/workspace/My-Workspace~011ab1c4-c804-43e8-8bee-0d99d813b9ea/collection/3352689-846233c2-feec-4bad-b904-67b58fb0d924?action=share&creator=3352689)

## Development

### Running the development server

```bash
symfony server:start
```

The API is available at `http://localhost:8000`
