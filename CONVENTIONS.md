# Application Conventions — Learning Guide

> **Purpose:** This document explains the architectural decisions you need to make before Phase 2 begins. Each section describes what it is, the available options with pros/cons, and links to official documentation. Read through each section, learn the trade-offs, and then we'll finalize the decisions together.

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [CRUD Approach](#2-crud-approach)
3. [Roles & Permission Management](#3-roles--permission-management)
4. [Service / Action / DTO Layer](#4-service--action--dto-layer)
5. [Livewire Component Structure](#5-livewire-component-structure)
6. [Blade Component Patterns](#6-blade-component-patterns)
7. [Frontend Stack](#7-frontend-stack)
8. [CSS / UI Framework](#8-css--ui-framework)
9. [Naming Conventions](#9-naming-conventions)
10. [Database Conventions](#10-database-conventions)
11. [Testing Strategy](#11-testing-strategy)
12. [File Organization](#12-file-organization)
13. [Filament Panel Configuration](#13-filament-panel-configuration)

---

## 1. Authentication

### What is it?
Authentication handles **who can log in** to the system. It manages login forms, session handling, password resets, and "remember me" functionality.

### Options

#### A. Filament Default Auth (Recommended for Filament projects)
Filament ships its own authentication system. When you create a Filament panel, it automatically includes login, password reset, and email verification pages.

- **Pros:** Zero setup, integrated with Filament panel, includes login/password-reset/email-verification
- **Cons:** Only covers Filament panel pages — doesn't protect your custom Blade routes (e.g., the current dashboard)
- **Docs:** https://filamentphp.com/docs/3.x authentication

#### B. Laravel Breeze
A minimal authentication scaffolding. Provides login, register, password reset, email verification, and a basic dashboard.

- **Pros:** Full auth system for all routes, simple and lightweight, first-party Laravel package
- **Cons:** Includes registration by default (you may not want public registration), adds extra views/routes
- **Docs:** https://laravel.com/docs/12.x/starter-kits#laravel-breeze

#### C. Laravel Fortify
A headless authentication backend (no views). Handles login, registration, password reset via API/actions. You build your own UI.

- **Pros:** Flexible, no opinionated UI, full control
- **Cons:** More work to set up, you must build all the views yourself
- **Docs:** https://laravel.com/docs/12.x/fortify

#### D. Custom Authentication
Build your own using Laravel's Auth facade, middleware, and guards.

- **Pros:** Complete control
- **Cons:** Most work, easy to make security mistakes, reinventing the wheel
- **Docs:** https://laravel.com/docs/12.x/authentication

### Recommendation
Since we're using **Filament for the admin panel**, Filament's built-in auth handles admin login. For the custom dashboard (non-Filament Blade pages), we'll likely need **Laravel Breeze** or simple auth middleware.

---

## 2. CRUD Approach

### What is it?
CRUD (Create, Read, Update, Delete) is how you build the data management screens — forms for creating/editing records, tables for listing them, modals for viewing details.

### Options

#### A. Filament PHP (Recommended)
A full admin panel builder for Laravel. You define "Resource" classes that describe your models, and Filament generates the entire UI.

```php
// Example: Filament Resource
class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('phone'),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('phone'),
        ]);
    }
}
```

- **Pros:** Extremely fast to build, production-ready UI, built-in search/sort/filter, responsive, consistent design
- **Cons:** UI is opinionated (Filament's style), learning curve for advanced features
- **Docs:** https://filamentphp.com/docs/3.x

#### B. Custom Livewire Components
Build each CRUD screen as a Livewire component with Blade views.

- **Pros:** Full control over UI/UX, no framework opinions, lighter weight
- **Cons:** Much more code to write, you must build table sorting/filtering/pagination yourself
- **Docs:** https://livewire.laravel.com/docs

#### C. Hybrid (Filament + Custom Livewire)
Use Filament for admin/management panels and custom Livewire for customer-facing or technician-facing pages.

- **Pros:** Best of both worlds — fast admin CRUD + custom UX where needed
- **Cons:** Two patterns to maintain, potential style inconsistency

---

## 3. Roles & Permission Management

### What is it?
Roles & permissions control **what each user can do** after they log in. For example: an Admin can manage all data, a Cashier can only create transactions, a Technician can only view assigned jobs.

### Options

#### A. Spatie Permission + Filament Shield (Recommended)
Spatie Laravel Permission is the most popular permission package. Filament Shield auto-generates permissions for every Filament resource/page/widget.

```php
// Example: Using permissions
Role::create(['name' => 'admin']);
Role::create(['name' => 'cashier']);
Permission::create(['name' => 'manage customers']);
$user->assignRole('cashier');
$user->givePermissionTo('manage customers');

// In Filament Resource:
class CustomerResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('manage customers');
    }
}
```

- **Pros:** Battle-tested, auto-generates permissions for Filament resources, GUI for managing roles
- **Cons:** Adds a package dependency, permission matrix can grow large
- **Docs:** https://spatie.be/docs/laravel-permission/ and https://github.com/bezhansaleh/filament-shield

#### B. Spatie Permission (Without Shield)
Use Spatie Permission alone, manually defining each permission.

- **Pros:** Full control over permission names
- **Cons:** More manual work to keep permissions in sync with new resources

#### C. Laravel Policies Only
Use Laravel's built-in policy system without any package.

- **Pros:** No extra package, built into Laravel, simple for small apps
- **Cons:** No database-backed roles, harder to manage at scale, no GUI for role management
- **Docs:** https://laravel.com/docs/12.x/authorization#defining-policies

#### D. Bouncer
An alternative to Spatie with a simpler API and "abilities" concept.

- **Pros:** Simpler API than Spatie, supports both roles and abilities
- **Cons:** Less popular, fewer Filament integrations

### Recommendation
**Spatie Permission + Filament Shield** is the standard for Filament projects. It auto-generates permissions for every Filament resource you create.

---

## 4. Service / Action / DTO Layer

### What is it?
This is about **where business logic lives**. When a customer buys a glass product, stock needs to be decremented, a transaction created, a payment recorded, and a stock movement logged. Where does that code go?

### Options

#### A. Keep It Simple (No Service Layer)
Put business logic directly in Filament Resources, Eloquent models, or controllers.

- **Pros:** Fast to implement, everything in one place
- **Cons:** Resources become huge, hard to test business logic in isolation, logic gets duplicated
- **Best for:** Small apps, rapid prototyping, MVPs

#### B. Service Classes (Recommended)
Extract complex business operations into dedicated service classes.

```php
class TransactionService
{
    public function createSale(Customer $customer, array $items, float $totalPaid): Transaction
    {
        $transaction = Transaction::create([...]);
        foreach ($items as $item) {
            TransactionItem::create([...]);
            StockBalance::where('glass_product_id', $item['product_id'])->decrement('quantity', $item['qty']);
            StockMovement::create([...]);
        }
        return $transaction;
    }
}
```

- **Pros:** Reusable, testable, keeps models/resources clean, clear single responsibility
- **Cons:** Extra directory/files, can become "junk drawers" if not well-organized
- **Best for:** Medium to large apps with complex business rules (like this one)

#### C. Action Classes
Each action is a single-purpose class (one class = one operation).

- **Pros:** Very focused, easy to test, clear intent from class name
- **Cons:** Many small files, overkill for simple operations
- **Best for:** Large apps with complex domain logic

#### D. DTOs (Data Transfer Objects) + Actions
DTOs are simple data objects that carry data between layers without business logic.

```php
readonly class SaleData
{
    public function __construct(
        public int $customerId,
        public array $items,
        public float $totalPaid,
        public ?string $notes = null,
    ) {}
}
```

- **Pros:** Type-safe data passing, self-documenting, IDE autocomplete
- **Cons:** More files and boilerplate, steeper learning curve
- **Best for:** Large apps with many data flows, teams

### Recommendation
Given the complexity of stock management (lot-based tracking, stock movements, allocations, opnames), a **Service Layer** is recommended. Start with services and migrate to Actions+DTOs if complexity grows.

---

## 5. Livewire Component Structure

### What is it?
If we use Livewire (either standalone or within Filament), we need to decide where Livewire components live, how they're named, and how they're organized.

### Options

#### A. `app/Livewire/` (Default since Livewire v3)
Livewire v3 convention: components live in `app/Livewire/` and views in `resources/views/livewire/`.

```
app/Livewire/CustomerList.php
resources/views/livewire/customer-list.blade.php
```

- **Pros:** Livewire v3 default, simple, widely documented
- **Cons:** Can get cluttered with many components
- **Docs:** https://livewire.laravel.com/docs/directory-structure

#### B. Domain-Organized
Group by business domain.

```
app/Livewire/Customers/CustomerList.php
app/Livewire/Transactions/TransactionList.php
app/Livewire/Inventory/StockBalanceList.php
```

- **Pros:** Scalable, easy to find related components
- **Cons:** More directory nesting, overkill for small apps

### Note for Filament
If we use Filament exclusively for CRUD, we may not need custom Livewire components at all — Filament Resources handle everything. Custom Livewire would only be needed for non-Filament pages (like the dashboard).

---

## 6. Blade Component Patterns

### What is it?
Blade components are reusable UI pieces (buttons, cards, modals, form fields) used in Blade views.

### Options

#### A. Anonymous Components (Recommended)
Simple `.blade.php` files in `resources/views/components/`. No PHP class needed.

```blade
{{-- resources/views/components/card.blade.php --}}
<div {{ $attributes->merge(['class' => 'rounded-xl border bg-white p-6 shadow-sm']) }}>
    {{ $slot }}
</div>

{{-- Usage --}}
<x-card class="mb-4">
    <h2>Title</h2>
</x-card>
```

- **Pros:** Simple, no boilerplate, fast to create, recommended by Laravel
- **Docs:** https://laravel.com/docs/12.x/blade#anonymous-components

#### B. Class-Based Components
PHP classes + `.blade.php` files in `app/View/Components/`.

- **Pros:** Complex logic, validation, computed properties
- **Cons:** More boilerplate, overkill for most UI components
- **Docs:** https://laravel.com/docs/12.x/blade#class-based-components

#### C. WireUI / Flux / Other Libraries
Pre-built component libraries that provide polished UI components.

- **Pros:** Professional-looking UI out of the box
- **Cons:** Additional dependency, may conflict with Filament's UI
- **Examples:** https://wireui.com/ and https://fluxui.com/

### Note for Filament
Filament has its own component system (Form Components, Table Columns, Infolists). Custom Blade components would mainly be used in non-Filament pages.

---

## 7. Frontend Stack

### What is it?
The JavaScript and CSS tools that run in the browser to make the UI interactive.

### Current State
- **Alpine.js** — loaded via CDN (interactive UI behaviors)
- **Tailwind CSS** — configured via `@tailwindcss/vite` plugin
- **Vite** — build tool (already configured)
- **Livewire** — installed (via composer)

### Options

#### A. Alpine.js + Tailwind + Blade (Current)
Keep the current lightweight stack.

- **Pros:** Lightweight, fast page loads, simple mental model
- **Cons:** Limited reactivity (full page refreshes for server-side changes)
- **Best for:** Content-heavy apps, simple dashboards

#### B. Livewire + Alpine.js + Tailwind (Recommended)
Add Livewire for reactive components that update without full page refreshes.

- **Pros:** Server-side reactivity, still simple, works great with Blade
- **Cons:** More complex than pure Alpine, requires understanding Livewire lifecycle
- **Best for:** Interactive dashboards, real-time features, form-heavy apps
- **Docs:** https://livewire.laravel.com/docs

#### C. Inertia.js + Vue/React + Tailwind
Use Inertia.js to connect Laravel backend to a Vue.js or React frontend.

- **Pros:** Full SPA experience, rich client-side interactivity
- **Cons:** Requires JavaScript framework knowledge, heavier, different mental model
- **Docs:** https://inertiajs.com/

### Recommendation
**Livewire + Alpine.js + Tailwind** is the natural choice since Livewire is already installed and Filament is built on Livewire.

---

## 8. CSS / UI Framework

### What is it?
The styling system that controls how the application looks.

### Current State
- **Tailwind CSS** — utility-first CSS framework, already configured
- **Instrument Sans** — font, already loaded via Vite fonts plugin

### Options

#### A. Tailwind CSS Only (Current)
Use Tailwind's utility classes directly in Blade templates.

- **Pros:** No extra dependencies, full control, widely known, Filament uses Tailwind internally
- **Cons:** Verbose HTML, can be hard to maintain in large templates
- **Docs:** https://tailwindcss.com/

#### B. Tailwind + Flowbite
Flowbite is a component library built on Tailwind with pre-built components (dropdowns, modals, navbars).

- **Pros:** Pre-built components, consistent design, works with Alpine.js
- **Cons:** Extra dependency, may conflict with Filament's components
- **Docs:** https://flowbite.com/

#### C. Tailwind + DaisyUI
DaisyUI adds component class names to Tailwind (e.g., `btn btn-primary` instead of long utility strings).

- **Pros:** Shorter class names, consistent theming
- **Cons:** Different class naming, may conflict with Filament's Tailwind setup
- **Docs:** https://daisyui.com/

### Recommendation
**Tailwind CSS only** — Filament already uses Tailwind, so no conflict. Keep it simple.

---

## 9. Naming Conventions

### What is it?
Standardized names for routes, views, models, migrations, and methods so the codebase is predictable.

### Recommended Conventions

| Type | Convention | Example |
|------|-----------|---------|
| Models | Singular, PascalCase | `Customer`, `GlassProduct`, `StockLot` |
| Tables | Plural, snake_case | `customers`, `glass_products`, `stock_lots` |
| Columns | snake_case | `first_name`, `created_at`, `glass_product_id` |
| Foreign keys | `{model}_id` | `customer_id`, `glass_product_id` |
| Route names | Dot notation, noun-first | `customers.index`, `customers.store` |
| View paths | Plural, dot notation | `customers/index.blade.php` |
| Filament Resources | Plural model + Resource | `CustomerResource`, `GlassProductResource` |
| Controllers | RESTful methods | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` |
| Services | `{Domain}Service` | `TransactionService`, `StockService` |
| Actions | `{Verb}{Noun}` | `CreateSaleTransaction`, `UpdateStockBalance` |
| DTOs | `{Noun}Data` | `SaleData`, `StockAdjustmentData` |

---

## 10. Database Conventions

### What is it?
Standards for database design: timestamps, soft deletes, ID types, and relationship patterns.

### Primary Key Type

| Option | Description | Recommendation |
|--------|-------------|----------------|
| Auto-incrementing `id` (BIGINT) | Default Laravel | ✅ **Use this** — simple, fast, sufficient for a workshop app |
| UUID | Universally unique, no sequential leaking | Use if you need distributed IDs or public-facing URLs |
| ULID | Like UUID but sortable by time | Use for high-throughput event systems |

### Soft Deletes
Soft deletes add a `deleted_at` column instead of actually deleting rows.

- **Use soft deletes for:** Customers, Transactions, Glass Products (things you might want to audit/recover)
- **Don't use soft deletes for:** Stock Movements, Payments (immutable records), pivot tables
- **Docs:** https://laravel.com/docs/12.x/eloquent#soft-deleting

### Timestamps
- Always use `created_at` and `updated_at` (default Laravel behavior)
- Add `deleted_at` only where soft deletes are enabled
- Use `$timestamps = false` only for pivot/junction tables

### Morph Map
Consider defining a morph map for cleaner database values:

```php
// AppServiceProvider.php
Relation::enforceMorphMap([
    'glass_product' => GlassProduct::class,
    'service' => Service::class,
    'accessory' => Accessory::class,
]);
```

- **Pros:** Cleaner DB values (string keys instead of full class names), easier refactoring
- **Docs:** https://laravel.com/docs/12.x/eloquent-relationships#polymorphic-relationships

---

## 11. Testing Strategy

### What is it?
What kind of tests to write, what framework to use, and how thorough to be.

### Options

#### A. Pest PHP (Recommended)
A testing framework built on top of PHPUnit with expressive syntax.

```php
it('can calculate stock balance', function () {
    $stock = StockBalance::factory()->create(['quantity' => 10]);
    expect($stock->quantity)->toBe(10);
});

it('can create a transaction', function () {
    $response = $this->post(route('transactions.store'), [
        'customer_id' => Customer::factory()->create()->id,
        'items' => [...],
    ]);
    $response->assertRedirect();
    $this->assertDatabaseCount('transactions', 1);
});
```

- **Pros:** Expressive syntax, concise, growing Laravel community standard, built on PHPUnit
- **Docs:** https://pestphp.com/docs

#### B. PHPUnit
Laravel's default testing framework.

- **Pros:** Built-in, no extra dependency, extensive documentation
- **Cons:** More verbose, class-based syntax
- **Docs:** https://laravel.com/docs/12.x/testing

### What to Test?

| Test Type | Priority | What It Covers |
|-----------|----------|----------------|
| Feature Tests | **High** | HTTP endpoints, form submissions, auth, CRUD operations |
| Unit Tests | **Medium** | Service classes, business logic, helper functions |
| Livewire Tests | **High** | Livewire component interactions (if using custom Livewire) |
| Filament Tests | **Medium** | Filament resource actions, pages |
| Browser Tests (Dusk) | **Low** | Full browser automation, complex UI flows |

### Recommendation
**Pest PHP** for cleaner syntax. Focus on **Feature tests** for CRUD and **Unit tests** for business logic.

---

## 12. File Organization

### Where to put different types of files so the project stays organized.

```
awm/
├── app/
│   ├── Enums/                          # PHP enums (exists ✅)
│   ├── Models/                         # Eloquent models (exists ✅)
│   ├── Services/                       # Business logic service classes
│   │   ├── TransactionService.php
│   │   └── StockService.php
│   ├── Actions/                        # (Optional) Single-purpose action classes
│   │   └── Transactions/
│   │       └── CreateSaleTransaction.php
│   ├── DTOs/                           # (Optional) Data transfer objects
│   │   └── SaleData.php
│   ├── Filament/                       # Filament resources, pages, widgets
│   │   └── Resources/
│   │       ├── CustomerResource.php
│   │       └── GlassProductResource.php
│   ├── Http/Controllers/               # Traditional controllers (if needed)
│   ├── Livewire/                       # Custom Livewire components (if any)
│   └── Providers/
├── database/
│   ├── migrations/                     # (exists ✅)
│   ├── seeders/                        # (exists ✅)
│   └── factories/                      # Model factories for testing
├── resources/views/
│   ├── components/                     # Anonymous Blade components
│   ├── layouts/                        # (exists ✅)
│   ├── livewire/                       # Livewire component views
│   └── filament/                       # Filament view overrides
├── routes/
└── tests/
    ├── Unit/
    └── Feature/
```

### Key Rules
1. **Group by type first, then by domain** — `Services/TransactionService.php`
2. **One class per file** — following PSR-4 autoloading
3. **Filament files stay in `app/Filament/`** — don't mix with other app code
4. **Keep views organized by concern** — `layouts/`, `components/`, `livewire/`, `filament/`

---

## 13. Filament Panel Configuration

### What is it?
Filament organizes its admin area into "panels." You can have one panel (single admin) or multiple panels (e.g., admin + staff + customer portal).

### Options

#### A. Single Panel (Recommended)
One Filament panel for all admin/management tasks.

```php
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors(['primary' => Color::Blue])
            ->navigationGroups([
                'Master Data',
                'Inventory',
                'Transactions',
                'Analytics',
                'Settings',
            ]);
    }
}
```

- **Pros:** Simple, one login, one place for all management
- **Cons:** All roles share the same panel (permissions control access, not separate logins)

#### B. Multi-Panel
Multiple panels with separate logins and UIs.

- **Pros:** Different login experiences for different roles
- **Cons:** More setup, duplicate configuration
- **Best for:** Multi-tenant SaaS, platforms with distinct user types

### Key Features to Configure

| Feature | Description | Recommendation |
|---------|-------------|----------------|
| `->login()` | Enables login page | ✅ Use |
| `->registration()` | Enables public registration | ❌ Skip (admin-only) |
| `->passwordReset()` | Enables password reset | ✅ Use |
| `->emailVerification()` | Requires email verification | ❌ Skip for now |
| `->sidebarCollapsibleOnDesktop()` | Collapses sidebar | ✅ Use |
| `->navigationGroups()` | Groups sidebar items | ✅ Use |
| `->widgets()` | Dashboard widgets | ✅ Use |
| `->colors()` | Brand colors | ✅ Use |
| `->favicon()` | Panel favicon | ✅ Use |

### Filament Resources to Create (Phase 2 Preview)

**Master Data:** CustomerResource, VehicleResource, CarBrandResource, CarModelResource, GlassPositionResource, TechnicianResource, SupplierResource

**Product & Inventory:** GlassProductResource, AccessoryResource, RackResource, StockBalanceResource, StockLotResource, StockMovementResource, StockOpnameResource, StockAllocationResource

**Transactions:** TransactionResource, TransactionItemResource, PaymentResource, ServiceAssignmentResource

**Settings:** Filament's built-in User resource

---

## Summary: Decision Checklist

Once you've reviewed all sections, we'll make final decisions on:

- [ ] **Auth:** Filament default + Breeze for dashboard? Or just Filament?
- [ ] **CRUD:** Filament Resources (100%) or Hybrid with custom Livewire?
- [ ] **Roles:** Spatie Permission + Shield? Or simpler approach?
- [ ] **Business Logic:** Service layer? Actions? Keep it simple?
- [ ] **Testing:** Pest or PHPUnit? How thorough?
- [ ] **Filament:** Single panel or multi-panel?

> **Next Step:** Read through this document, research the links, and tell me which options you'd like to go with. I'll then finalize the conventions and update `AGENTS.md` to match.

---

*Last updated: 2026-08-10 | Phase 1 — Auto Glass Workshop Management System*
