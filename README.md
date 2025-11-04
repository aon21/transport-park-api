# Transport Park Management API

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-7.3-black)](https://symfony.com/)
[![Tests](https://img.shields.io/badge/tests-77%20passed-brightgreen)](#test-coverage)
[![Coverage](https://img.shields.io/badge/coverage-95.95%25-brightgreen)](docs/coverage/index.html)

A comprehensive REST API for managing transport park operations including trucks, trailers, drivers, fleet sets, and service orders.

## 📋 Table of Contents

- [Features](#features)
- [Documentation](#documentation)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running Tests](#running-tests)
- [API Endpoints](#api-endpoints)
- [Architecture](#architecture)
- [Test Coverage](#test-coverage)

## ✨ Features

- **Complete CRUD Operations** for all entities
- **26 REST API Endpoints** with full validation
- **Complex Relationships** between Trucks, Trailers, Drivers, Fleet Sets, and Orders
- **Fleet Statistics** with database aggregation
- **Request/Response DTOs** for type-safe API contracts
- **Global Error Handling** with consistent JSON responses
- **Comprehensive Test Suite** (77 tests, 268 assertions)
- **OpenAPI/Swagger Documentation** for interactive API exploration

## 📚 Documentation

### API Documentation

- **[OpenAPI Specification (JSON)](docs/api/openapi.json)** - Machine-readable API specification
- **[OpenAPI Specification (YAML)](docs/api/openapi.yaml)** - Human-readable API specification
- **Interactive Swagger UI**: http://localhost:8000/api/doc (when server is running)

### Test Coverage

- **[HTML Coverage Report](docs/coverage/index.html)** - Detailed code coverage analysis
- **Overall Coverage**: 95.95% lines, 96.48% methods
- **Test Suite**: 77 tests with 268 assertions

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- SQLite3 (for development)

### Setup

```bash
# Clone the repository
git clone <repository-url>
cd transport-park-api

# Install dependencies
composer install

# Configure environment
cp .env .env.local
# Edit .env.local and set your APP_SECRET

# Create database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Load sample data (optional)
php bin/console doctrine:fixtures:load
```

## 🗄️ Database Setup

The project uses SQLite for development (easy setup, zero configuration).

```bash
# Run migrations
php bin/console doctrine:migrations:migrate

# Load fixtures (creates sample data)
php bin/console doctrine:fixtures:load
```

### Entities

- **Truck**: Registration number, brand, model, status
- **Trailer**: Registration number, type, capacity, status
- **Driver**: Name, license number, optional fleet assignment
- **FleetSet**: Truck + Trailer combination with status calculation
- **Order**: Service orders with truck/trailer/fleet relationships

## 🧪 Running Tests

```bash
# Run all tests
./bin/phpunit

# Run specific test suite
./bin/phpunit tests/Feature/
./bin/phpunit tests/Unit/

# Run with coverage
./bin/phpunit --coverage-html docs/coverage

# Run specific test
./bin/phpunit tests/Feature/TruckControllerTest.php
```

### Test Structure

- **Feature Tests** (50 tests): Integration tests for all controllers
- **Unit Tests** (27 tests): Isolated tests for services and listeners

## 🛣️ API Endpoints

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
- `GET /api/fleet-sets/statistics` - Get fleet statistics ⭐
- `POST /api/fleet-sets` - Create fleet set
- `PUT /api/fleet-sets/{id}` - Update fleet set
- `DELETE /api/fleet-sets/{id}` - Delete fleet set

### Orders
- `GET /api/orders` - List all orders
- `GET /api/orders/{id}` - Get order details
- `POST /api/orders` - Create order
- `PUT /api/orders/{id}` - Update order
- `DELETE /api/orders/{id}` - Delete order

## 🏗️ Architecture

### Design Patterns

- **Service Layer Pattern**: Business logic separated from controllers
- **Repository Pattern**: Data access abstraction
- **DTO Pattern**: Request/response transformation
- **Entity Auto-Resolution**: Automatic entity lookup in controllers
- **Global Exception Handling**: Consistent error responses

### Key Architectural Decisions

- **Entity Auto-Resolution**: Controllers use type-hinted entities for automatic 404 handling
- **#[MapRequestPayload]**: Automatic JSON to DTO deserialization
- **Database Aggregation**: Statistics calculated via DQL for performance
- **Strict SRP**: Services contain only business logic, no pass-through methods
- **findOrFail Pattern**: Consistent entity lookup with automatic exceptions

### Code Quality

- **SOLID Principles**: Fully compliant
- **Test Coverage**: 95.95% lines, 96.48% methods
- **Zero Technical Debt**: Clean, production-ready code
- **Comprehensive Tests**: 77 tests covering all critical paths

## 📊 Test Coverage

Current test coverage (updated: 2025-11-04):

| Component | Methods | Lines | Status |
|-----------|---------|-------|--------|
| **Overall** | **96.48%** (192/199) | **95.95%** (521/543) | ✅ Excellent |
| Controllers | 100% | 100% | ✅ Perfect |
| OrderService | 100% | 100% | ✅ Perfect |
| TruckService | 100% | 100% | ✅ Perfect |
| TrailerService | 100% | 100% | ✅ Perfect |
| ExceptionListener | 100% | 100% | ✅ Perfect |
| Entities | 95-100% | 95-100% | ✅ Excellent |
| Repositories | 83-100% | 97-100% | ✅ Excellent |

View detailed coverage: **[HTML Report](docs/coverage/index.html)**

## 🛠️ Development

### Start Development Server

```bash
# Option 1: Symfony CLI (recommended)
symfony server:start

# Option 2: PHP Built-in Server
php -S localhost:8000 -t public
```

### Access Documentation

- API Documentation: http://localhost:8000/api/doc
- OpenAPI JSON: http://localhost:8000/api/doc.json

### Generate Documentation

```bash
# Update OpenAPI documentation
php bin/console nelmio:apidoc:dump --format=json > docs/api/openapi.json
php bin/console nelmio:apidoc:dump --format=yaml > docs/api/openapi.yaml

# Generate coverage report
./bin/phpunit --coverage-html docs/coverage
```

## 📦 Tech Stack

- **Framework**: Symfony 7.3
- **Language**: PHP 8.2+
- **Database**: SQLite (dev), Doctrine ORM
- **Testing**: PHPUnit 11.5
- **API Docs**: Nelmio API Doc Bundle (OpenAPI 3.0)
- **Fixtures**: Doctrine Fixtures Bundle

## 🔐 Error Handling

All API errors return consistent JSON format:

```json
{
  "error": "Error message here"
}
```

**HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `204` - No Content (delete)
- `400` - Bad Request
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## 🤝 Contributing

When adding new features:

1. Write tests first (TDD approach)
2. Ensure 95%+ code coverage
3. Update OpenAPI documentation
4. Follow SOLID principles
5. Run full test suite before committing

## 📝 License

[Your License Here]

## 👥 Authors

[Your Name/Team]

---

**Project Status**: ✅ Production Ready

For detailed API usage, see the **[OpenAPI Specification](docs/api/openapi.yaml)** or **[Interactive Swagger UI](http://localhost:8000/api/doc)**.

