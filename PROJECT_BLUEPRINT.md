# JALAK FASHION Admin Panel Blueprint

## 🏗️ Core Architecture
The application has been transformed from a basic CRUD into a multi-phase logistics and financial management system.

### 1. Dynamic Invoice Engine (`invoice.blade.php`)
- **Phases**: Supports `Booking`, `Logistics/Delivery`, and `Return Settlement`.
- **Theming**: Automatically switches colors and labels based on the `type` parameter or booking `status`.
- **Exporting**: Supports triple-version PDF generation for every single transaction.

### 2. Master Dashboard (`index.blade.php`)
- **Real-time Logistics**: Tracks Today/Tomorrow deliveries and returns.
- **Micro-Metrics**: Shows packed/unpacked counts for tomorrow's dispatch prep.
- **Inventory Overview**: Aggregates total unique designs and total physical units across all stocks.
- **Dynamic Feed**: Displays the latest 6 transactions with status badges.

### 3. Centralized Invoice Center (`invoices.blade.php`)
- **Universal Search**: Search by Invoice, Name, or Mobile across the entire database.
- **Phase Filtering**: Fast-toggle between Booking, Delivery, and Return categories.
- **Contextual Management**: Action buttons change dynamically (Update Booking -> Manage Delivery -> View Return) based on the transaction's current stage.

### 🔐 Security & Access
- **Auth Guard**: All admin routes are protected via the `auth` middleware.
- **Login Credentials**: 
  - **User**: `kishan7112@gmail.com`
  - **Pass**: `123`
- **Session**: Secure logout and session re-generation implemented in `LoginController.php`.

## 🚀 Production Handover Checklist
1. **Remove .gemini/brain**: Only keep this if you want the AI history to follow the server.
2. **Environment**: Set `APP_DEBUG=false` and `APP_ENV=production`.
3. **Build**: Run `npm run build` on the server.
4. **Optimization**: Run `php artisan optimize` to cache routes and config.
