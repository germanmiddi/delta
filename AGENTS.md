# AGENTS.md

This file contains guidelines and commands for agentic coding agents working in this Laravel + Vue.js application.

## Project Overview

This is a Laravel 9 application with Vue 3 + Inertia.js frontend for a delivery/order management system. The application manages clients, orders, drivers, and companies with real-time dashboard features.

## Build Commands

### Frontend (Laravel Mix)
```bash
# Development build
npm run dev
# or
npm run development

# Watch for changes (development)
npm run watch

# Watch with polling (for Docker environments)
npm run watch-poll

# Hot module replacement
npm run hot

# Production build
npm run prod
# or
npm run production
```

### Backend (Laravel)
```bash
# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start development server
php artisan serve

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Testing Commands

### PHPUnit
```bash
# Run all tests
php artisan test
# or
./vendor/bin/phpunit

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run single test file
php artisan test tests/Feature/AuthenticationTest.php

# Run specific test method
php artisan test --filter test_login_screen_can_be_rendered

# Run tests with coverage
php artisan test --coverage

# Run tests in verbose mode
php artisan test --verbose
```

## Code Style Guidelines

### PHP (Backend)

#### Imports & Namespaces
- Use PSR-4 autoloading
- Alphabetical import ordering
- One `use` statement per line
- Group imports: external libraries first, then internal

```php
<?php

namespace App\Http\Controllers\Manager\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;
use App\Models\Order;
use Carbon\Carbon;
```

#### Class Structure
1. Namespace declaration
2. Import statements
3. Class declaration
4. Properties (public, protected, private)
5. Constructor
6. Methods (public, protected, private)

#### Method Naming
- Use camelCase for method names
- Use descriptive names: `getClients()` not `getCl()`
- Resource methods follow REST conventions: `index()`, `show()`, `store()`, `update()`, `destroy()`

#### Documentation
- Use PHPDoc blocks for all methods
- Include `@param`, `@return`, and `@throws` where applicable
- Use Laravel's conventional docblocks for resource methods

```php
/**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
public function index()
{
    return Inertia::render('Manager/Dashboard/Index');
}
```

#### Error Handling
- Use Laravel's built-in validation
- Return proper HTTP status codes
- Use try-catch blocks for external API calls
- Log errors using `Log::error()`

### JavaScript/Vue (Frontend)

#### Component Structure
1. `<template>` section
2. `<script setup>` or `<script>` section
3. `<style scoped>` section

#### Vue 3 Composition API
- Prefer `<script setup>` syntax
- Use `ref()` for primitive values
- Use `reactive()` for objects
- Use `computed()` for derived values

```javascript
<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'

const showingNavigationDropdown = ref(false)
const title = computed(() => 'Dashboard')
</script>
```

#### Options API (when used)
- Use `data()` for component state
- Use `methods` for component methods
- Use `computed` for computed properties
- Use `mounted()` and `created()` lifecycle hooks

#### Event Handling
- Use kebab-case for event names: `@update-order`
- Emit events with descriptive names: `$emit('order-updated', order)`
- Use `.prevent` and `.stop` modifiers when needed

#### Styling
- Use Tailwind CSS classes
- Prefer utility classes over custom CSS
- Use `scoped` styles for component-specific CSS
- Follow mobile-first responsive design

## File Organization

### Backend Structure
```
src/app/
├── Http/Controllers/
│   └── Manager/          # Manager-specific controllers
│       ├── Dashboard/
│       ├── Clients/
│       ├── Orders/
│       └── Drivers/
├── Models/               # Eloquent models
├── Providers/            # Service providers
└── Actions/              # Jetstream actions
```

### Frontend Structure
```
src/resources/js/
├── Pages/
│   └── Manager/          # Manager-specific pages
├── Layouts/              # Layout components
├── Components/           # Reusable components
└── Jetstream/            # Jetstream components
```

## Database Conventions

### Migrations
- Use descriptive migration names
- Include foreign key constraints
- Use proper column types and lengths
- Add indexes for frequently queried columns

### Models
- Use fillable arrays for mass assignment
- Use hidden arrays for sensitive data
- Define relationships using Eloquent methods
- Use casts for data type conversion

## API Conventions

### Routes
- Use resourceful routes where possible
- Group routes with middleware
- Use descriptive route names
- Follow REST conventions

### Controllers
- Use dependency injection
- Return Inertia responses for web routes
- Use JSON responses for API routes
- Validate input using Form Requests

## Security Guidelines

### Authentication
- Use Laravel's built-in authentication
- Implement proper authorization checks
- Use Sanctum for API authentication
- Enable CSRF protection

### Data Validation
- Validate all user input
- Use Laravel's validation rules
- Sanitize data before database operations
- Implement rate limiting for API endpoints

## Performance Guidelines

### Database
- Use eager loading to prevent N+1 queries
- Implement proper database indexing
- Use query caching for expensive operations
- Paginate large result sets

### Frontend
- Lazy load components when possible
- Use Vue's built-in optimizations
- Minimize bundle size with code splitting
- Implement proper caching strategies

## Testing Guidelines

### Unit Tests
- Test individual methods in isolation
- Use factories for test data
- Mock external dependencies
- Test edge cases and error conditions

### Feature Tests
- Test complete user workflows
- Use browser testing when needed
- Test authentication and authorization
- Verify API responses

## Development Workflow

1. Create feature branch from main
2. Run tests before starting work
3. Make small, focused commits
4. Run tests after changes
5. Update documentation as needed
6. Create pull request for review

## Common Issues & Solutions

### Laravel Mix Issues
- Clear node_modules and reinstall: `rm -rf node_modules && npm install`
- Clear Mix cache: `npm run clean`

### Laravel Issues
- Clear config cache: `php artisan config:clear`
- Check .env file for proper configuration
- Ensure proper file permissions

### Vue Issues
- Check for missing imports in components
- Verify proper event emission syntax
- Use Vue DevTools for debugging

## Environment Configuration

### Required Environment Variables
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Development Setup
1. Clone repository
2. Install PHP dependencies: `composer install`
3. Install Node dependencies: `npm install`
4. Copy environment file: `cp .env.example .env`
5. Generate application key: `php artisan key:generate`
6. Configure database in .env
7. Run migrations: `php artisan migrate`
8. Start development servers: `php artisan serve` and `npm run watch`