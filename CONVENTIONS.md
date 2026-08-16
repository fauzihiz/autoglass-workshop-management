# Application Conventions

> **Purpose:** This document defines the finalized architectural decisions, coding conventions, and project standards for the Autoglass Workshop Management System. All developers and AI agents working on this codebase must follow these conventions.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Architecture](#2-architecture)
3. [Database Strategy](#3-database-strategy)
4. [Deployment](#4-deployment)
5. [Model Conventions](#5-model-conventions)
6. [Enum Conventions](#6-enum-conventions)
7. [Migration Conventions](#7-migration-conventions)
8. [Seeder Conventions](#8-seeder-conventions)
9. [Naming Conventions](#9-naming-conventions)
10. [Livewire Conventions](#10-livewire-conventions)
11. [Blade Conventions](#11-blade-conventions)
12. [Testing Conventions](#12-testing-conventions)
13. [Code Style](#13-code-style)
14. [Project Principles](#14-project-principles)

---

## 1. Project Overview

A web-based management system for automotive glass workshops to manage customers, vehicles, glass inventory, rack locations, transactions, technicians, payments, and business insights.

### Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.3, Laravel 13 |
| ORM | Eloquent |
| Frontend | Blade, Livewire 4, Alpine.js, Tailwind CSS |
| Build Tool | Vite |
| Testing | Pest |
| Local Database | SQLite |
| Production Database | Supabase PostgreSQL |
| Deployment | Render (Docker) |
| Version Control | Git + GitHub |

### Key Packages

| Package | Version | Purpose |
|---------|---------|---------|
| `laravel/framework` | ^13.17 | Core framework |
| `livewire/livewire` | ^4.1 | Server-driven UI |
| `livewire/blaze` | ^1.0 | Livewire starter kit |
| `pestphp/pest` | ^5.0 | Testing framework |
| `laravel/pint` | ^1.27 | Code formatting |

> Always confirm package versions with `composer show --direct` or `package.json` before using a package's API.

---

## 2. Architecture

### Application Structure

```text
awm/
├── app/
│   ├── Enums/              # Backed string enums (TransactionType, PaymentMethod, etc.)
│   ├── Livewire/           # Livewire full-page components
│   ├── Models/             # Eloquent models with SoftDeletes
│   └── Providers/          # Service providers
├── database/
│   ├── migrations/         # Timestamped migrations (anonymous classes)
│   └── seeders/            # Indonesian workshop demo data
├── resources/
│   └── views/
│       ├── components/     # Anonymous Blade components
│       ├── layouts/        # app.blade.php, sidebar.blade.php
│       └── livewire/       # Livewire component views
├── routes/
│   └── web.php             # Web routes (named routes)
├── tests/                  # Pest feature and unit tests
├── public/                 # Public assets (Vite build output)
├── storage/                # Logs, cache, sessions
└── bootstrap/              # Application bootstrap
```

### Architectural Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Admin Panel | Custom Blade + Livewire (no Filament) | Full control over UI, matches workshop workflows |
| CRUD Approach | Livewire full-page components | Consistent with existing dashboard pattern |
| Business Logic | Service classes grouped by domain | Testable, organized, without over-engineering |
| Authentication | Deferred to Phase 8 | Core business workflow takes priority |
| Roles & Permissions | Deferred to Phase 8 | Will use Spatie Laravel Permission when needed |
| Frontend Framework | Alpine.js for client-side only | Lightweight, sufficient for modal/dropdown/tabs |

### Service Layer (Planned)

When Phase 2+ adds business logic, use service classes:

```text
app/Services/
├── TransactionService.php     # Transaction creation, confirmation, rollback
├── InventoryService.php       # Stock-in, stock-out, transfer, opname
├── CustomerService.php        # Customer management logic
└── PaymentService.php         # Payment recording, partial payments
```

Rules:
- One service per domain
- Services injected via constructor (Laravel container)
- Keep methods focused — split into Action classes if a method exceeds ~30 lines

---

## 3. Database Strategy

### Environment Mapping

| Environment | Database | Connection String | Purpose |
|-------------|----------|-------------------|---------|
| Development / Demo | SQLite | `database/database.sqlite` | Local dev, `migrate:fresh --seed` safe |
| Production (Portfolio Demo) | Supabase PostgreSQL | `pgsql://...pooler.supabase.com:6543/postgres` | Live demo, data preserved across deploys |

### Supabase Connection Details

```text
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543              # Session mode (web requests)
DB_DATABASE=postgres
DB_USERNAME=postgres.<project-ref>
DB_PASSWORD=<password>
```

> **Session mode (port 6543)** for web requests. **Transaction mode (port 5432)** for long-running processes.

### Migration Rules

1. **Use standard Laravel Schema builder only** — no raw SQL, no database-specific functions
2. **All migrations must be PostgreSQL compatible** — verified against Supabase
3. **No SQLite-specific syntax** in migrations (e.g., no `DB::statement()` with SQLite pragmas)
4. **Use `foreignId()->constrained()->cascadeOnDelete()`** for foreign keys
5. **Use `timestamps()` and `softDeletes()`** on all core business tables
6. **Anonymous class pattern** for all new migrations:

```php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_name', function (Blueprint $table) {
            $table->id();
            // columns...
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
};
```

---

## 4. Deployment

### VPS + Supabase Architecture

```text
VPS (Ubuntu 24.04)
   ├── PHP 8.3 + Composer
   ├── Node.js 22 + npm
   └── Laravel artisan serve / Nginx
          ↓
Supabase PostgreSQL (Session mode pooler)
          ↓
Live Application
```

### Production Environment Variables

Copy `.env.production.example` to `.env` on the VPS and fill in your credentials:

```text
APP_NAME="Workshop Management System"
APP_ENV=production
APP_DEBUG=false
APP_KEY=<run: php artisan key:generate --show>
APP_URL=http://<your-vps-ip>

DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.<project-ref>
DB_PASSWORD=<password>
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Deployment Commands

```bash
# On the VPS
cd autoglass-workshop-management/awm
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- `migrate --force` runs pending migrations without confirmation
- `db:seed --force` re-seeds demo data (seeders handle duplicates gracefully)
- Seed data persists in Supabase across redeploys because migrations don't drop tables

### VPS Notes

- Seed data persists because `migrate --force` does not drop existing tables
- Use Nginx as reverse proxy for production (SSL, static assets, performance)

---

## 5. Model Conventions

### Standard Model Pattern

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'email', 'address', 'notes'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
```

### Rules

1. **Always use `SoftDeletes`** on core business models (Customer, Transaction, GlassProduct, StockLot, etc.)
2. **Explicit `$fillable` arrays** — never use `$guarded = []`
3. **Relationship methods have no return type declarations** — matching current codebase style
4. **Use `hasMany()` / `belongsTo()`** — standard Eloquent relationship methods only
5. **One model per file** in `app/Models/`
6. **Table names are plural snake_case** — `customers`, `glass_products`, `stock_balances`
7. **Foreign keys follow `{model}_id` convention** — `customer_id`, `vehicle_id`, `glass_product_id`

### Model Inventory (24 models)

```text
Core Business:
├── Customer, Vehicle
├── CarBrand, CarModel
├── GlassPosition, GlassProduct
├── Accessory, Service, Technician, Supplier, Rack

Inventory:
├── StockLot, StockBalance, StockMovement
├── StockOpname, StockOpnameItem, StockAllocation

Transactions:
├── Transaction, TransactionItem, Payment, ServiceAssignment

System:
└── User
```

---

## 6. Enum Conventions

### Standard Enum Pattern

```php
<?php

namespace App\Enums;

enum TransactionType: string
{
    case GlassSale = 'glass_sale';
    case GlassInstallation = 'glass_installation';
    case ServiceOnly = 'service_only';

    public function label(): string
    {
        return match ($this) {
            self::GlassSale => 'Glass Sale',
            self::GlassInstallation => 'Glass Installation',
            self::ServiceOnly => 'Service Only',
        };
    }
}
```

### Rules

1. **Backed string enums** — `enum Name: string`
2. **TitleCase case names** — `GlassSale`, `ServiceOnly`, `Cash`, `QRIS`
3. **snake_case database values** — `'glass_sale'`, `'service_only'`, `'cash'`
4. **`label(): string` method** on every enum — returns human-readable display text
5. **One enum per file** in `app/Enums/`

### Existing Enums

| Enum | Cases | Database Column |
|------|-------|-----------------|
| `TransactionType` | `GlassSale`, `GlassInstallation`, `ServiceOnly` | `transactions.type` |
| `TransactionStatus` | `Pending`, `Confirmed`, `Cancelled` | `transactions.status` |
| `PaymentMethod` | `Cash`, `Transfer`, `QRIS`, `Other` | `payments.method` |
| `StockMovementType` | `In`, `Out`, `Transfer`, `Adjustment` | `stock_movements.type` |

---

## 7. Migration Conventions

### File Naming

```text
YYYY_MM_DD_HHMMSS_create_{table_name}_table.php
```

- Timestamps use **seconds precision** (standard Laravel format)
- Related tables may share the same timestamp prefix
- Suffix is always `_create_{table}_table`

### Column Conventions

| Pattern | Usage |
|---------|-------|
| `$table->id()` | Primary key (bigIncrements) |
| `$table->foreignId('model_id')->constrained()->cascadeOnDelete()` | Foreign keys |
| `$table->string('type')` | Enum columns stored as strings |
| `$table->string('status')->default('pending')` | Status with default |
| `$table->text('notes')->nullable()` | Optional text fields |
| `$table->timestamps()` | `created_at` / `updated_at` |
| `$table->softDeletes()` | Soft delete timestamp |

### Index Conventions

- Foreign keys with `->constrained()` get automatic indexes
- Add explicit indexes for frequently queried columns: `$table->index(['customer_id', 'created_at'])`
- Unique constraints create automatic indexes — don't duplicate with `->index()`

### Existing Migrations (26)

```text
System:      users, cache, jobs
Core:        customers, vehicles, car_brands, car_models, glass_positions,
             glass_products, services, accessories, suppliers, technicians, racks
Inventory:   stock_balances, stock_lots, stock_movements, stock_opnames,
             stock_opname_items, stock_allocations, product_accessories,
             product_compatibilities
Transactions: transactions, transaction_items, payments, service_assignments
```

---

## 8. Seeder Conventions

### Standard Seeder Pattern

```php
<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Budi Hartono', 'phone' => '0812-1111-2222', ...],
            ['name' => 'Siti Rahayu', 'phone' => '0856-3333-4444', ...],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
```

### Rules

1. **Array-based seed data** — each record is an associative array
2. **Indonesian workshop data** — names, addresses, phone numbers from Surabaya
3. **`foreach` + `Model::create()`** pattern — simple and readable
4. **One seeder per entity type** — `CustomerSeeder`, `GlassProductSeeder`, etc.
5. **`DatabaseSeeder` orchestrates** — calls individual seeders in dependency order
6. **Seed data designed for meaningful dashboard results** — not random data

### Seed Data Coverage (13 seeders)

```text
Master Data:
├── CustomerSeeder (5 customers)
├── VehicleSeeder (multiple vehicles per customer)
├── CarBrandSeeder / CarModelSeeder
├── GlassPositionSeeder
├── TechnicianSeeder
├── SupplierSeeder
├── RackSeeder

Product & Inventory:
├── GlassProductSeeder
├── AccessorySeeder
├── ServiceSeeder
├── StockLotSeeder + StockBalanceSeeder

Transactions:
└── TransactionSeeder (glass sales, installations, service-only)
```

---

## 9. Naming Conventions

| Entity | Convention | Example |
|--------|-----------|---------|
| Model class | Singular PascalCase | `Customer`, `GlassProduct` |
| Model table | Plural snake_case | `customers`, `glass_products` |
| Enum class | Singular PascalCase | `TransactionType`, `PaymentMethod` |
| Enum case | TitleCase | `GlassSale`, `Cash` |
| Enum DB value | snake_case string | `'glass_sale'`, `'cash'` |
| Migration file | `create_{table}_table` | `create_customers_table.php` |
| Seeder class | `{Entity}Seeder` | `CustomerSeeder` |
| Livewire component | PascalCase | `TransactionIndex`, `StockOpnameForm` |
| Blade component | kebab-case | `<x-status-badge>`, `<x-card>` |
| Route name | dot notation | `dashboard`, `transactions.index` |
| Foreign key | `{model}_id` | `customer_id`, `vehicle_id` |

---

## 10. Livewire Conventions

### Component Structure

```text
app/Livewire/
├── Dashboard.php              → resources/views/livewire/dashboard.blade.php
├── TransactionIndex.php       → resources/views/livewire/transaction-index.blade.php
└── TransactionCreate.php      → resources/views/livewire/transaction-create.blade.php
```

### Rules

1. **Full-page components** for most pages — each major page is one Livewire component
2. **Nested components** only for complex forms (transaction workflow)
3. **State lives server-side** — UI always reflects server state
4. **Validate and authorize in actions** — same as HTTP request validation
5. **Use `mount()` for initialization** — set up component state on load
6. **Use `#[Route()]` attribute** for routing (Laravel 11+ style):

```php
#[Route('/transactions', name: 'transactions.index')]
class TransactionIndex extends Component
{
    public function render()
    {
        return view('livewire.transaction-index');
    }
}
```

### View Files

- Livewire views go in `resources/views/livewire/`
- View file name matches component name in kebab-case
- Use `wire:model` for form bindings, `wire:click` for actions
- Use Alpine.js for client-side only interactions (modals, dropdowns)

---

## 11. Blade Conventions

### Component Types

| Type | Location | Use Case |
|------|----------|----------|
| Anonymous components | `resources/views/components/` | Presentational elements |
| Class-based components | `app/View/Components/` | Components needing PHP logic (rare) |
| Livewire views | `resources/views/livewire/` | Dynamic, interactive pages |
| Layouts | `resources/views/layouts/` | Page wrappers |

### Rules

1. **Anonymous components for all presentational elements** — `<x-card>`, `<x-status-badge>`
2. **Use `{{ $slot }}`** for component content injection
3. **Use `$attributes->merge()`** for attribute forwarding
4. **Tailwind utility classes** directly in templates — no separate CSS files
5. **Alpine.js for client-side interactions** — `x-data`, `x-show`, `x-on:click`
6. **`wire:` directives for server interactions** — `wire:model`, `wire:click`
7. **Named routes with `route()`** helper

---

## 12. Testing Conventions

### Framework

**Pest** — configured in `composer.json`, run with `php artisan test`.

### Test Creation

```bash
# Feature test (preferred)
php artisan make:test --pest CustomerManagementTest

# Unit test
php artisan make:test --pest --unit StockCalculationTest
```

### Test Rules

1. **Feature tests preferred over unit tests** — test the full request/response cycle
2. **Use factories** for creating test models
3. **Use `php artisan test --compact`** to run tests
4. **Use `php artisan test --compact --filter=testName`** to filter tests
5. **Do NOT delete tests without approval**
6. **Code formatting before commit:** `vendor/bin/pint --dirty --format agent`

### Critical Test Areas

```text
Stock Calculation          — Stock-in, stock-out, transfer, balance
Stock Allocation           — Which lot gets deducted first
Transaction Creation       — Glass sale, installation, service-only
Transaction Rollback       — Stock restoration on cancellation
Payment Calculation        — Full payment, partial payment
Profit Calculation         — Revenue minus glass cost
Minimum Stock Warnings     — LOW STOCK, OUT OF STOCK detection
Product Compatibility      — Vehicle-glass relationships
Complaint Traceability     — License plate → transaction → stock lot
```

---

## 13. Code Style

### Formatter

**Laravel Pint** — PSR-12 base with Laravel preset.

### Rules

1. **Run Pint before committing:** `vendor/bin/pint --dirty --format agent`
2. **Don't run `pint --test`** — just run `pint` to fix
3. **PSR-12 compliant** — spaces, brackets, line lengths
4. **Use descriptive variable/method names** — `isRegisteredForDiscounts` not `discount()`
5. **Check sibling files** for conventions before creating new files
6. **One class per file** — PSR-4 autoloading

### PHP Conventions

```php
// Explicit return types and parameter type hints
public function calculateProfit(Transaction $transaction): float
{
    // ...
}

// Enums: TitleCase cases, snake_case values
enum PaymentMethod: string
{
    case Cash = 'cash';
}

// No empty __construct() unless private
// Prefer PHPDoc over inline comments
// Use array shape type definitions in PHPDoc
```

---

## 14. Project Principles

These principles guide every implementation decision:

1. **Inventory accuracy comes first.**
2. **Every stock change must be traceable.**
3. **Historical transaction data must not depend on current product prices.**
4. **Actual purchase cost comes from the stock lot used.**
5. **Customer-facing pricing must remain flexible.**
6. **The price calculator is a negotiation tool, not an inventory record.**
7. **A package transaction may have one customer-facing price while still tracking the glass used internally.**
8. **Vehicle and license plate information are essential for complaint traceability.**
9. **Profit is calculated as revenue minus glass cost.**
10. **The demo and production application use the same application architecture.**
11. **SQLite is used for development/demo, while PostgreSQL (Supabase) is used for production.**
12. **Demo data is generated through seeders rather than hardcoded application state.**
13. **The application should remain practical and deployable on modern cloud platforms.**
14. **Complex infrastructure should not be introduced unless the business actually requires it.**
15. **Core workshop workflows take priority over secondary features.**

---

*Last updated: 2026-08-12 | Phase 1 Complete — Auto Glass Workshop Management System*
