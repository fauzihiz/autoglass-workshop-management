# Workshop Management System

> A web-based management system for automotive glass workshops to manage customers, vehicles, glass inventory, rack locations, transactions, technicians, payments, and business insights in one place.

**Status:** 🚧 In Development
**Version:** 0.5.0
**Deployment Target:** VPS + Supabase PostgreSQL

---

## 📸 Visual Preview

> Screenshots and demo will be added as the application reaches a stable UI stage.

<!-- Add screenshots or demo GIF here -->

---

## 📋 Table of Contents

* [Core Features](#-core-features)
* [System Concepts](#-system-concepts)
* [Transaction Types](#-transaction-types)
* [Inventory Management](#-inventory-management)
* [Profit Calculation](#-profit-calculation)
* [Tech Stack](#-tech-stack)
* [Prerequisites](#-prerequisites)
* [Getting Started](#-getting-started)
* [Environment Variables](#-environment-variables)
* [Usage Guide](#-usage-guide)
* [Database Overview](#-database-overview)
* [Demo & Production Strategy](#-demo--production-strategy)
* [Roadmap](#-roadmap)
* [Progress Tracker](#-progress-tracker)
* [Testing](#-testing)
* [Deployment](#-deployment)
* [Contributing](#-contributing)
* [License](#-license)

---

## ✨ Core Features

### Customer Management

* Create and manage customer records
* Store customer contact information
* Manage multiple vehicles belonging to one customer
* Track customer transaction history
* Search customers by name or contact number

### Vehicle Management

* Store vehicle brand and model
* Store vehicle year when applicable
* Store license plate number
* Associate vehicles with customers
* Use vehicle information for transaction and complaint traceability

### Automotive Glass Catalog

* Manage glass products
* Define glass positions such as `LFW`, `FDR`, `FDL`, `RDR`, `RDL`, `RW`, etc.
* Define compatible vehicle models
* Support products compatible with multiple vehicle models
* Support optional vehicle year ranges
* Store accessories such as sensors, shadebands, mouldings, antennas, and other attached components
* Set minimum stock thresholds

### Inventory Management

* Track glass stock by product
* Track stock by supplier lot
* Track stock across multiple racks
* Transfer stock between racks
* Record stock movements
* Perform stock opname
* Track actual purchase cost
* Identify low-stock and out-of-stock products
* View rack-level stock availability

### Supplier Management

* Manage suppliers
* Store supplier pricelist
* Store supplier discount information when applicable
* Store actual purchase price
* Support different pricing schemes between suppliers
* Maintain historical purchase costs through stock lots

### Transaction Management

Support three main transaction categories:

* **Glass Sale**
* **Glass Installation**
* **Service Only**

Transactions can contain:

* Glass products
* Services
* Packages
* Other charges when required

A glass installation can be recorded as a single customer-facing price, for example:

```text
Front windshield installation - Avanza
Rp950,000
```

The customer does not need to see separate glass and installation prices.

Internally, however, the system still tracks the actual glass product and stock cost used for inventory and profit calculations.

### Payment Management

* Record full payments
* Record partial payments
* Record unpaid transactions
* Support multiple payments for one transaction
* Track payment status
* Support cash, transfer, QRIS, and other payment methods

### Technician Tracking

* Manage technician records
* Assign technicians to service work
* Track which technician performed a service
* Trace technician information from historical transactions

### Complaint Traceability

Transaction history can be traced using:

* Customer
* Vehicle
* Vehicle brand
* Vehicle model
* License plate
* Transaction date
* Glass product
* Technician
* Invoice number

This provides a reliable service history and helps prevent unsupported customer claims.

### Price Calculator

The inventory interface provides a pricing calculator for negotiation.

The calculator can use:

* Supplier pricelist
* Supplier discount
* Actual purchase cost
* Desired customer discount
* Desired selling price
* Estimated profit

The calculator is a **UI utility only**.

It does not modify inventory prices or create a database record.

The final negotiated selling price is stored only when the transaction is confirmed.

### Dashboard & Analytics

The system provides insights into:

* Total revenue
* Glass-only sales
* Glass installation revenue
* Service-only revenue
* Glass cost
* Profit
* Transaction volume
* Best-selling glass products
* Glass movement
* Stock status
* Low-stock products
* Customer purchasing frequency
* Top customers
* Sales trends
* Stock movement trends

---

## 🧠 System Concepts

The system separates several concepts that are often incorrectly combined in a basic workshop inventory system.

### Product vs. Stock

A **glass product** describes what the item is.

A **stock lot** describes a particular batch of that product purchased from a supplier.

A **stock balance** describes where that stock is currently located.

Example:

```text
Glass Product
└── Avanza LFW

    Stock Lot #001
    ├── Supplier A
    ├── Purchase Cost: Rp550,000
    ├── Rack A1: 3 pcs
    └── Rack A2: 10 pcs

    Stock Lot #002
    ├── Supplier B
    ├── Purchase Cost: Rp650,000
    └── Rack A3: 5 pcs
```

This structure allows the system to accurately track stock and historical cost even when the same product comes from different suppliers.

---

## 🚗 Product Compatibility

Compatibility is managed through relationships between glass products and vehicle models.

A single glass product may be compatible with multiple vehicles.

Example:

```text
Glass Product:
LFW - Product A

Compatible Vehicles:
- Honda Mobilio
- Honda Brio
```

Another product may be:

```text
Glass Product:
LFW - Product B

Compatible Vehicles:
- Toyota Avanza
- Daihatsu Xenia
```

Year ranges are optional because suppliers may provide compatibility information without specific year ranges.

Example:

```text
Avanza / Xenia
```

can exist without a year range.

When a supplier specifies a year range:

```text
Avanza
2015 - 2020
```

the system can store it.

---

## 📦 Inventory Management

### Rack-Based Stock

A product can exist in multiple racks.

Example:

```text
Avanza LFW

Rack A1 → 3 pcs
Rack A2 → 10 pcs
Rack A3 → 1 pcs

Total → 14 pcs
```

The rack is treated as a **location**, not as part of the glass product itself.

### Stock Movement

Every stock change must create a stock movement.

Supported movement types:

```text
IN
OUT
TRANSFER
ADJUSTMENT
```

Examples:

```text
Stock In:
Supplier A → Rack A1 → +10

Transfer:
Rack A1 → Rack A2 → 3

Stock Out:
Rack A2 → Customer Transaction → -1

Adjustment:
Stock Opname → +1 / -1
```

This creates an audit trail for inventory changes.

---

## ⚠️ Stock Threshold

Each glass product can have a minimum stock level.

Example:

```text
Product:
Avanza LFW

Current Stock:
2

Minimum Stock:
5
```

The dashboard displays a warning because the current stock is below the configured threshold.

Possible statuses:

```text
NORMAL
LOW STOCK
OUT OF STOCK
```

The stock dashboard provides a dedicated warning list so staff can quickly identify products that may need to be reordered.

---

## 💰 Transaction Types

### 1. Glass Sale

Customer purchases the glass product without installation.

Example:

```text
Avanza LFW
Rp800,000
```

Transaction category:

```text
GLASS_SALE
```

---

### 2. Glass Installation

Customer purchases a glass installation package or combined glass + installation service.

Example:

```text
Front windshield installation - Avanza
Rp950,000
```

The customer does not need to see:

```text
Glass         Rp800,000
Installation Rp150,000
```

The system can record the customer-facing transaction as one price while internally tracking the glass product and its stock cost.

Transaction category:

```text
GLASS_INSTALLATION
```

---

### 3. Service Only

Customer requests a service without purchasing a glass product.

Example:

```text
Glass repair
Rp200,000
```

Transaction category:

```text
SERVICE_ONLY
```

No glass stock is deducted.

---

## 📊 Profit Calculation

The business uses a simple profit formula:

```text
Profit = Selling Price - Glass Cost
```

### Glass Sale

```text
Selling Price = Rp800,000
Glass Cost    = Rp550,000

Profit        = Rp250,000
```

### Glass Installation

```text
Selling Price = Rp950,000
Glass Cost    = Rp550,000

Profit        = Rp400,000
```

### Service Only

```text
Selling Price = Rp200,000
Glass Cost    = Rp0

Profit        = Rp200,000
```

### Business Rule

The system currently does **not** deduct technician labor, installation cost, or other service-related costs from profit.

Therefore:

```text
Total Profit = Total Revenue - Total Glass Cost
```

Historical glass cost is determined from the actual stock lot allocated to the transaction.

---

## 🧮 Price Calculator

The price calculator is a negotiation tool used when a customer asks for a price.

Example:

```text
Supplier Pricelist
Rp1,000,000

Supplier Discount
45%

Purchase Cost
Rp550,000

Customer Discount
30%

Suggested Selling Price
Rp700,000

Estimated Profit
Rp150,000
```

The admin can then negotiate and enter the final price.

For example:

```text
Final Selling Price
Rp750,000
```

Only the final agreed price is saved when the transaction is confirmed.

The calculator itself does not modify the product price, inventory, supplier data, or stock cost.

---

# 🛠️ Tech Stack

### Backend

* PHP
* Laravel
* Laravel Eloquent ORM

### Frontend

* Blade
* Livewire
* Alpine.js
* Tailwind CSS

### Database

**Development / Demo**

* SQLite

**Production / Portfolio Demo**

* Supabase PostgreSQL (Session mode pooler)
* Fully PostgreSQL compatible — all migrations use standard Laravel Schema

### Development Tools

* Git
* GitHub
* Composer
* npm
* Vite

### Deployment

* VPS (Tencent Cloud / Ubuntu 24.04)
* Supabase PostgreSQL (Session mode pooler)
* PHP 8.3 CLI

The application is intentionally designed to avoid infrastructure requirements such as Redis, dedicated queue workers, or VPS-only services for the initial version.

---

## 📋 Prerequisites

Before running the project locally, make sure you have:

* PHP with the version required by the selected Laravel version
* Composer
* Node.js and npm
* Git

SQLite is used for the default development/demo environment, so a separate database server is not required for local development.

Verify your environment:

```bash
php -v
composer -V
node -v
npm -v
git --version
```

### Dual Database Setup

This project supports **two database backends** for different environments:

| Environment | Database | Driver | Notes |
|-------------|----------|--------|-------|
| **Local Development** | SQLite | `sqlite` | Zero-config, file-based. Default in `.env.example` |
| **Production (VPS)** | Supabase PostgreSQL | `pgsql` | Use `.env.production.example` as template |

All 25 migrations are written in database-agnostic SQL — no PostgreSQL-specific syntax is used. Both SQLite and PostgreSQL are fully supported without migration changes.

**Key points:**

* `SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`, and `CACHE_STORE=database` all work with SQLite locally — no changes needed when switching environments.
* The `.env.example` file defaults to SQLite. The `.env.production.example` file contains the Supabase PostgreSQL template.
* To switch between environments, simply adjust `DB_CONNECTION` and the corresponding `DB_*` variables in your `.env`.

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone <repository-url>
cd <project-directory>
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Frontend Dependencies

```bash
npm install
```

### 4. Create Environment File

```bash
cp .env.example .env
```

On Windows:

```powershell
copy .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Prepare the Demo Database

Create the SQLite database:

```bash
touch database/database.sqlite
```

On Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

### 7. Run Migrations and Seed Demo Data

```bash
php artisan migrate:fresh --seed
```

This creates the database structure and populates the application with realistic demo data.

### 8. Build Frontend Assets

```bash
npm run build
```

### 9. Start the Development Server

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

---

## 🔐 Environment Variables

The application uses Laravel's environment configuration to switch between development/demo and production databases.

### Demo / Development

The default configuration uses SQLite:

```env
APP_NAME="Workshop Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
```

The database file is:

```text
database/database.sqlite
```

### Production

For the portfolio demo deployment, configure Supabase PostgreSQL:

```env
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.<your-project-ref>
DB_PASSWORD=your_supabase_password
```

> **Note:** Supabase provides two connection modes. Use **Session mode** (port `6543`) for web requests where connections are short-lived. Use **Transaction mode** (port `5432`) for long-running processes.

The application code does not need to be rewritten when switching between SQLite and PostgreSQL.

Never commit `.env` or real credentials to the repository.

---

# 🎭 Demo & Production Strategy

This project is designed to serve two purposes:

1. **Learning and portfolio project**
2. **Potential foundation for a real client project**

The application therefore uses the same Laravel application architecture in both environments.

### Local Development

```text
Laravel
   ↓
Eloquent
   ↓
SQLite
   ↓
Seeded Demo Data
   ↓
Development Server
```

### Portfolio Demo (Deployed)

```text
Git Push
   ↓
VPS Deployment
   ↓
Laravel + PHP 8.3 CLI
   ↓
Supabase PostgreSQL
   ↓
Seeded Demo Data
   ↓
Live Demo Application
```

The goal is to avoid building a separate "fake demo application".

The demo uses the same business logic, database structure, models, relationships, inventory workflows, transaction workflows, and analytics that the production application will use.

Only the **database connection and data source** change.

---

## 🌱 Demo Data

The development database should contain realistic seeded data so the application is immediately usable as a portfolio demo.

Seed data should include examples of:

* Customers
* Multiple vehicles per customer
* Vehicle brands and models
* Technicians
* Suppliers
* Glass products
* Product compatibility
* Accessories
* Services
* Multiple racks
* Stock lots from different suppliers
* Stock distributed across multiple racks
* Low-stock products
* Out-of-stock products
* Glass sales
* Glass installation transactions
* Service-only transactions
* Full payments
* Partial payments
* Historical transactions

The seed data should intentionally create meaningful dashboard results rather than purely random records.

For example:

```text
Avanza LFW
Stock: 2
Minimum: 5
Status: LOW STOCK
```

and:

```text
Top Customer
PT ABC

Transactions: 15
```

This allows the dashboard, inventory warnings, analytics, and transaction history to be demonstrated immediately.

---

## ⚠️ Database Environment Rules

### Development / Demo

It is safe to reset the database:

```bash
php artisan migrate:fresh --seed
```

This removes existing local/demo data and recreates the database from migrations and seeders.

### Production

**Never use `migrate:fresh --seed` in production.**

Use:

```bash
php artisan migrate --force
```

Production data must be preserved.

Seeders are primarily intended for development and demonstration.

---

## 🖥️ Usage Guide

### Check Glass Availability

1. Open the inventory dashboard.
2. Search for the vehicle or glass product.
3. Select the required glass position.
4. Review compatible vehicles.
5. Check current stock.
6. Review rack locations.
7. Review supplier and purchase cost information.
8. Use the price calculator when negotiating with the customer.

Example:

```text
Vehicle:
Toyota Avanza

Position:
LFW

Stock:
14 pcs

Locations:
A1 → 3
A2 → 10
A3 → 1
```

---

### Create a Glass Sale

```text
Customer
    ↓
Select Glass
    ↓
Check Stock
    ↓
Select Stock Allocation
    ↓
Enter Final Selling Price
    ↓
Payment
    ↓
Confirm Transaction
    ↓
Stock OUT
    ↓
Invoice
```

---

### Create a Glass Installation

```text
Customer
    ↓
Select Vehicle
    ↓
Select Glass
    ↓
Select Stock
    ↓
Assign Technician
    ↓
Enter Final Package Price
    ↓
Payment
    ↓
Confirm Transaction
    ↓
Stock OUT
    ↓
Save Service Record
    ↓
Invoice
```

The customer may see only one combined price.

Example:

```text
Front windshield installation - Avanza
Rp950,000
```

Internally, the system still records which glass stock was used.

---

### Create a Service-Only Transaction

```text
Customer
    ↓
Select Vehicle
    ↓
Select Service
    ↓
Assign Technician
    ↓
Enter Price
    ↓
Payment
    ↓
Confirm Transaction
    ↓
Invoice
```

No glass stock is deducted.

---

## 🗄️ Database Overview

Main entities:

```text
customers
    └── vehicles

car_brands
    └── car_models

glass_positions

glass_products
    ├── product_compatibilities
    ├── product_accessories
    └── stock_lots
            ├── stock_balances
            │       └── racks
            └── stock_movements

suppliers

accessories

services

technicians

transactions
    ├── transaction_items
    │       ├── stock_allocations
    │       └── service_assignments
    │
    └── payments

stock_opnames
    └── stock_opname_items
```

### Core Relationship

```text
Customer
   │
   └── Vehicles
          │
          └── Transactions
                 │
                 ├── Transaction Items
                 │       ├── Glass
                 │       ├── Service
                 │       └── Package
                 │
                 ├── Payments
                 │
                 └── Service Assignments
                         │
                         └── Technician
```

---

# 🗺️ Roadmap

## Phase 1 — Project Foundation

**Goal:** Establish the Laravel application and database foundation.

* [x] Initialize Laravel project
* [x] Configure SQLite development database
* [x] Configure PostgreSQL production deployment (Render + Supabase)
* [x] Configure environment
* [x] Set up Git repository
* [x] Set up base layout
* [x] Install and configure Livewire
* [x] Install and configure Tailwind CSS
* [x] Establish application conventions
* [x] Create initial database migrations (26 migrations)
* [x] Create base seeders (13 seeders with Indonesian workshop data)
* [x] Configure VPS deployment with Supabase

---

## Phase 2 — Master Data

**Goal:** Build all foundational data management.

* [x] Customer CRUD
* [x] Vehicle CRUD
* [x] Technician CRUD
* [x] Supplier CRUD
* [x] Car brand CRUD
* [x] Car model CRUD
* [x] Glass position CRUD
* [x] Glass product CRUD
* [x] Product compatibility management
* [x] Accessory CRUD
* [x] Product-accessory management
* [x] Service CRUD
* [x] Rack CRUD

---

## Phase 3 — Inventory Management

**Goal:** Build reliable multi-rack and supplier-lot inventory.

* [x] Stock lot management
* [x] Supplier purchase information
* [x] Actual purchase cost tracking
* [x] Stock balance management
* [x] Multi-rack stock support
* [x] Stock-in workflow
* [x] Stock transfer workflow
* [ ] Stock-out workflow
* [x] Stock movement history
* [x] Minimum stock configuration
* [x] Low-stock warnings
* [x] Out-of-stock warnings
* [x] Stock opname
* [x] Stock adjustment
* [x] Inventory search and filtering

---

## Phase 4 — Transaction Management ✅

**Goal:** Build the complete sales and service workflow.

* [x] Customer selection (with inline creation)
* [x] Vehicle selection (with inline creation)
* [x] Glass availability lookup
* [x] Rack availability display
* [x] Price calculator
* [x] Glass sale transaction
* [x] Glass installation transaction
* [x] Service-only transaction
* [x] Flexible package pricing
* [x] Stock allocation (FIFO on confirmation)
* [x] Technician assignment
* [x] Payment recording
* [x] Partial payment support
* [x] Payment status
* [x] Transaction confirmation (atomic stock deduction)
* [x] Invoice generation (INV-YYYY-NNNN format)
* [x] Invoice printing
* [x] Profit calculation

---

## Phase 5 — Transaction & Complaint History

**Goal:** Make every completed job traceable.

* [ ] Transaction history
* [ ] Transaction detail
* [ ] Customer history
* [ ] Vehicle history
* [ ] License plate lookup
* [ ] Technician service history
* [ ] Glass product history
* [ ] Stock lot traceability
* [ ] Invoice history
* [ ] Payment history
* [ ] Complaint lookup workflow

---

## Phase 6 — Dashboard & Analytics

**Goal:** Turn operational data into useful business insights.

* [ ] Overview dashboard
* [ ] Revenue summary
* [ ] Glass sales summary
* [ ] Glass installation summary
* [ ] Service-only summary
* [ ] Glass cost summary
* [ ] Profit summary
* [ ] Sales trend chart
* [ ] Best-selling glass
* [ ] Glass movement analysis
* [ ] Customer ranking
* [ ] Purchase frequency analysis
* [ ] Stock alert dashboard
* [ ] Fast-moving products
* [ ] Slow-moving products
* [ ] Stock value overview

---

## Phase 7 — Production Hardening

**Goal:** Prepare the application for real workshop usage.

* [ ] Form validation review
* [ ] Authorization architecture preparation
* [ ] Database indexing
* [ ] Error handling
* [ ] Transaction rollback testing
* [ ] Stock consistency testing
* [ ] Invoice testing
* [ ] Backup strategy
* [ ] Production environment configuration (Render + Supabase)
* [ ] Render deployment verification
* [ ] Production smoke testing

---

## 🔐 Phase 8 — Authentication & Roles

> Planned after the core workflow is stable.

Potential roles:

```text
Admin
Manager
Inventory Staff
Cashier
Technician
```

Potential permissions:

```text
Inventory Management
Transaction Management
Customer Management
Technician Management
Reports
Analytics
Settings
```

Authentication is intentionally excluded from the initial development scope so the core business workflow can be completed first.

---

# 📈 Progress Tracker

### Overall Progress

```text
Foundation          ██████████ 100%
Master Data         ██████████ 100%
Inventory           ██████████ 100%
Transactions        ░░░░░░░░░░   0%
History & Complaint ░░░░░░░░░░   0%
Analytics           ░░░░░░░░░░   0%
Production          ░░░░░░░░░░   0%
Authentication      ░░░░░░░░░░   0%
```

### Detailed Tracker

| Phase          | Feature                        | Status |
| -------------- | ------------------------------ | ------ |
| Foundation     | Laravel setup                  | 🟢      |
| Foundation     | SQLite demo database           | 🟢      |
| Foundation     | PostgreSQL production deployment | 🟢    |
| Foundation     | Environment configuration      | 🟢      |
| Foundation     | Base UI layout                 | 🟢      |
| Foundation     | Livewire setup                 | 🟢      |
| Foundation     | Tailwind setup                 | 🟢      |
| Foundation     | Application conventions        | 🟢      |
| Foundation     | Database migrations            | 🟢      |
| Foundation     | Demo seeders                   | 🟢      |
| Foundation     | VPS deployment              | 🟢      |
| Master Data    | Customer management            | 🟢      |
| Master Data    | Vehicle management             | 🟢      |
| Master Data    | Technician management          | 🟢      |
| Master Data    | Supplier management            | 🟢      |
| Master Data    | Car brand management           | 🟢      |
| Master Data    | Car model management           | 🟢      |
| Master Data    | Glass position management      | 🟢      |
| Master Data    | Glass product management       | 🟢      |
| Master Data    | Compatibility management       | 🟢      |
| Master Data    | Accessory management           | 🟢      |
| Master Data    | Product-accessory management   | 🟢      |
| Master Data    | Service management             | 🟢      |
| Master Data    | Rack management                | 🟢      |
| Inventory      | Stock lot management           | 🟢      |
| Inventory      | Stock-in                       | 🟢      |
| Inventory      | Multi-rack stock               | 🟢      |
| Inventory      | Stock transfer                 | 🟢      |
| Inventory      | Stock movement                 | 🟢      |
| Inventory      | Stock opname                   | 🟢      |
| Inventory      | Minimum stock                  | 🟢      |
| Inventory      | Low-stock warning              | 🟢      |
| Transactions   | Glass sale                     | ⬜      |
| Transactions   | Glass installation             | ⬜      |
| Transactions   | Service only                   | ⬜      |
| Transactions   | Package pricing                | ⬜      |
| Transactions   | Price calculator               | ⬜      |
| Transactions   | Stock allocation               | ⬜      |
| Transactions   | Technician assignment          | ⬜      |
| Transactions   | Payment management             | ⬜      |
| Transactions   | Invoice                        | ⬜      |
| Transactions   | Profit calculation             | ⬜      |
| History        | Transaction history            | ⬜      |
| History        | Vehicle history                | ⬜      |
| History        | Complaint traceability         | ⬜      |
| Analytics      | Revenue dashboard              | ⬜      |
| Analytics      | Profit dashboard               | ⬜      |
| Analytics      | Glass sales analytics          | ⬜      |
| Analytics      | Installation analytics         | ⬜      |
| Analytics      | Customer analytics             | ⬜      |
| Analytics      | Stock analytics                | ⬜      |
| Production     | Validation review              | ⬜      |
| Production     | Error handling                 | ⬜      |
| Production     | Testing                        | ⬜      |
| Production     | Deployment                     | ⬜      |
| Authentication | Login                          | ⬜      |
| Authentication | Roles & permissions            | ⬜      |

### Status Legend

```text
⬜ Not Started
🟡 In Progress
🟢 Completed
🔴 Blocked
```

---

## 🧪 Running Tests

Run the Laravel test suite with:

```bash
php artisan test
```

For a specific test:

```bash
php artisan test --filter=TestName
```

Important test areas include:

* Stock calculation
* Stock transfer
* Stock opname
* Stock allocation
* Transaction creation
* Transaction rollback
* Payment calculation
* Profit calculation
* Minimum stock warnings
* Product compatibility
* Complaint traceability
* SQLite development behavior
* PostgreSQL production compatibility

---

## 🚢 Deployment

The application is deployed on a **VPS** (Tencent Cloud, Ubuntu 24.04) with **Supabase PostgreSQL** as the production database.

### Architecture

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

### Production Database

Supabase PostgreSQL (Session mode pooler):

```text
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.<your-project-ref>
DB_PASSWORD=<your-password>
DB_SSLMODE=require
```

### Deployment Steps

1. **Create a Supabase project** at [supabase.com](https://supabase.com) and note the pooler connection details.

2. **Set up the VPS:**
   * Install PHP 8.3 with required extensions (pdo_pgsql, pgsql, mbstring, bcmath, gd, xml, opcache)
   * Install Composer
   * Install Node.js 22 and npm
   * Install and configure Nginx as reverse proxy

3. **Clone the repository** and configure environment:

   ```bash
   git clone https://github.com/fauzihiz/autoglass-workshop-management.git
   cd autoglass-workshop-management/awm
   cp .env.production.example .env
   php artisan key:generate
   # Edit .env with your Supabase credentials
   ```

4. **Install dependencies and build:**

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```

5. **Run migrations and seed:**

   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Run production smoke tests:**
   * Verify the application loads at the VPS URL.
   * Check that demo data is visible on the dashboard.
   * Verify inventory, transactions, and analytics pages work.

Production should **never expose `.env` or application source files directly through the public web root**.

---

## 🤝 Contributing

This project is currently developed as a client-specific application and portfolio project.

For development:

1. Create a feature branch.

```bash
git checkout -b feature/your-feature
```

2. Implement the feature.
3. Run tests.

```bash
php artisan test
```

4. Review migrations and database changes.
5. Commit the changes.

```bash
git add .
git commit -m "feat: add your feature"
```

6. Push the branch.

```bash
git push origin feature/your-feature
```

Keep commits focused and avoid mixing unrelated changes.

---

## 📄 License

This project is proprietary software developed as a portfolio/demo project and potential foundation for a client-specific automotive glass workshop system.

The final license and distribution terms will be defined according to the client agreement if the project is commercialized.

---

## 📌 Project Principles

The following principles guide the implementation:

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
