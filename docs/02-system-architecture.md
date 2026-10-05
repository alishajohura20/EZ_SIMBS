# EZ_SIMBS - System Architecture

## Architecture Pattern

EZ_SIMBS follows a **simple MVC-like architecture** without a framework:

```
Browser Request
    │
    ▼
┌─────────────┐    ┌──────────────┐    ┌──────────────┐
│  Page        │───▶│  Class       │───▶│  Database    │
│  Controller  │    │  (Model)     │    │  (MySQL)     │
│  (PHP)       │◀───│              │◀───│              │
└─────────────┘    └──────────────┘    └──────────────┘
       │
       ▼
┌─────────────┐
│  API         │    Returns JSON via
│  Endpoint    │───▶ jsonSuccess() / jsonError()
└─────────────┘
```

## Layer Descriptions

### Page Controllers (`pages/`)
- PHP files that handle HTTP requests for specific pages
- Include layout partials (header, sidebar, navbar, footer)
- Make AJAX calls to API endpoints via `apiRequest()` JS helper
- Render HTML with Bootstrap 5 components

### API Endpoints (`api/`)
- Return JSON responses via `jsonSuccess()` / `jsonError()` helpers
- Route by HTTP method: GET (read), POST (create/action), PUT (update), DELETE (remove)
- All endpoints call `requireLogin()` for authentication
- Admin/manager endpoints call `requireRole()` for authorization

### Model Classes (`classes/`)
- Each class maps to a database table
- Singleton Database pattern via `Database::getInstance()`
- Methods: `getAll()`, `getById()`, `create()`, `update()`, `delete()`
- Domain-specific business logic (e.g., stock deduction, invoice generation)
- Activity logging via `logActivity()` helper

### Database Layer (`includes/db.php`)
- PDO-based singleton with prepared statements
- Methods: `fetch()`, `fetchAll()`, `insert()`, `update()`, `delete()`, `count()`
- Error mode: `ERRMODE_EXCEPTION`
- Emulated prepares disabled for security

## Request Flow

```
1. Browser hits index.php → redirect to login or dashboard
2. Login → session created with user_id, user_role
3. Dashboard page loads → makes AJAX to api/dashboard/stats.php + charts.php
4. API checks session → queries DB → returns JSON
5. JS renders data into DOM via innerHTML
```

## Authentication Flow

```
1. User submits credentials to api/auth/login.php
2. PHP verifies bcrypt hash against DB
3. On success: session vars set (user_id, user_role, user_name)
4. requireLogin() checks session on every protected page/API
5. requireRole() checks role against allowed roles array
```

## File Conventions

| Convention | Example |
|-----------|---------|
| Class file | `classes/Product.php` |
| API endpoint | `api/products/index.php` |
| Page controller | `pages/products/index.php` |
| Route params | `?id=N` query parameter |
| DB table | Plural snake_case (e.g., `sale_items`) |
| JS helper | `apiRequest(url, method, body)` |
