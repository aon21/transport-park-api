# Transport Park Management API

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-7.3-black)](https://symfony.com/)
[![Tests](https://img.shields.io/badge/tests-91%20passed-brightgreen)](#test-coverage)
[![Coverage](https://img.shields.io/badge/coverage-99.82%25-brightgreen)](coverage/index.html)

REST API for managing transport park operations including trucks, trailers, drivers, fleet sets, and service orders.

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- SQLite3 (for development)

### Setup

```bash
# Clone the repository
git clone git@github.com:aon21/transport-park-api.git
cd transport-park-api

# Install dependencies
composer install

# Configure environment
cp .env .env.local
# Edit .env.local and set your APP_SECRET

# Create database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Load sample data
php bin/console doctrine:fixtures:load

# Start the server
symfony serve
```
### Entities

- **Truck**: Registration number, brand, model, status
- **Trailer**: Registration number, type, capacity, status
- **Driver**: Name, license number, optional fleet assignment
- **FleetSet**: Truck + Trailer combination with status calculation
- **Order**: Service orders with truck/trailer/fleet relationships

## Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage
```
### Test Coverage

- **Overall Coverage**: 99.82% lines, 99.46% methods
- **Test Suite**: 91 tests with 260 assertions

## API Endpoints

### Trucks
- `GET /api/trucks` - List all trucks
- `GET /api/trucks/{id}` - Get truck details
- `POST /api/trucks` - Create truck
- `PUT /api/trucks/{id}` - Update truck
- `DELETE /api/trucks/{id}` - Delete truck

### Trailers
- `GET /api/trailers` - List all trailers
- `GET /api/trailers/{id}` - Get trailer details
- `POST /api/trailers` - Create trailer
- `PUT /api/trailers/{id}` - Update trailer
- `DELETE /api/trailers/{id}` - Delete trailer

### Drivers
- `GET /api/drivers` - List all drivers
- `GET /api/drivers/{id}` - Get driver details
- `POST /api/drivers` - Create driver
- `PUT /api/drivers/{id}` - Update driver
- `DELETE /api/drivers/{id}` - Delete driver

### Fleet Sets
- `GET /api/fleet-sets` - List all fleet sets
- `GET /api/fleet-sets/{id}` - Get fleet set details
- `GET /api/fleet-sets/statistics` - Get fleet statistics
- `POST /api/fleet-sets` - Create fleet set
- `PUT /api/fleet-sets/{id}` - Update fleet set
- `DELETE /api/fleet-sets/{id}` - Delete fleet set

### Orders
- `GET /api/orders` - List all orders
- `GET /api/orders/{id}` - Get order details
- `POST /api/orders` - Create order
- `PUT /api/orders/{id}` - Update order
- `DELETE /api/orders/{id}` - Delete order

## Tech Stack

- **Framework**: Symfony 7.3
- **Language**: PHP 8.2+
- **Database**: SQLite (dev), Doctrine ORM
- **Testing**: PHPUnit 11.5
- **Fixtures**: Doctrine Fixtures Bundle
