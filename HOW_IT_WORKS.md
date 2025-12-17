# SmartQueue - Hospital Queue Management System
## Complete System Analysis: How It Works

**Project:** Laravel Multi-Tenant SaaS Hospital Queue Management System  
**Date:** December 17, 2025  
**Version:** 1.0  

---

## 📋 TABLE OF CONTENTS

1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Core Workflows](#core-workflows)
4. [User Flows](#user-flows)
5. [Data Flow](#data-flow)
6. [Key Features](#key-features)
7. [Technical Stack](#technical-stack)

---

## 1. SYSTEM OVERVIEW

### What Is SmartQueue?

SmartQueue is a **multi-tenant SaaS (Software as a Service)** system designed for hospitals and clinics to manage patient queues efficiently. Multiple hospitals can use the same system with complete data isolation.

### Core Purpose

- **Queue Management**: Manage patient queues for different clinics/departments
- **Multi-Tenant**: Each hospital organization operates independently
- **Real-Time Updates**: Live queue status updates every 3 seconds
- **Display Screens**: Second screen/TV displays for patients
- **Laboratory Management**: Special OPD Lab queue system with multi-language support
- **Subscription-Based**: Plan-based access with clinic/user/screen limits

### Key Entities

- **Tenant**: Hospital/Organization (e.g., "City Hospital")
- **User**: Staff member (doctor, receptionist, lab technician, etc.)
- **Clinic**: Department within hospital (e.g., "Cardiology", "OPD Lab")
- **Queue**: Queue configuration for a clinic
- **SubQueue**: Individual queue line within a clinic (e.g., "Queue #1", "Queue #2")
- **Subscription**: Plan assignment for a tenant

---

## 2. ARCHITECTURE

### Multi-Tenancy Model

**Pattern:** Shared Database, Shared Schema with Row-Level Isolation

- All tenants share the same database
- Each table has `tenant_id` column
- Global scopes automatically filter by tenant
- Foreign keys ensure data integrity
- Middleware enforces tenant boundaries

### Tenant Identification Methods

The system supports 4 ways to identify a tenant:

1. **Subdomain** (e.g., `hospital1.smartqueue.com`)
2. **Custom Domain** (e.g., `hospital.com`)
3. **Route Parameter** (e.g., `/hospital1/dashboard`)
4. **User Session** (authenticated user's `current_tenant_id`)

### Data Isolation Layers

1. **Database Level**: `tenant_id` foreign key on all tenant-scoped tables
2. **Model Level**: Global `TenantScope` automatically filters queries
3. **Middleware Level**: `IdentifyTenant`, `EnsureTenantAccess`, `EnsureUserBelongsToTenant`
4. **Route Level**: Route model binding scopes by tenant automatically

### Security Layers

```
Request → IdentifyTenant → Auth → Tenant Access → Subscription → Role Check → Controller
```

**Middleware Chain:**
- `IdentifyTenant`: Determines which tenant from subdomain/domain/session
- `auth`: Ensures user is logged in
- `tenant.access`: Verifies user belongs to tenant
- `subscription`: Checks active subscription or trial
- `role:admin,reception,doctor`: Role-based access control
- `queue.auth`: Queue-specific access (password or role)
- `opd.verify`: OPD Lab password verification

---

## 3. CORE WORKFLOWS

### 3.1 Tenant Registration & Setup

```
1. User visits /register
   ↓
2. Fill registration form (name, email, password, organization name)
   ↓
3. TenantService::createTenant()
   - Generate unique slug from organization name
   - Create Tenant record
   - Create 14-day trial subscription
   - Attach user as 'owner' role
   - Set as user's current_tenant_id
   ↓
4. User redirected to dashboard
```

**Files:**
- `TenantController::register()`
- `TenantService::createTenant()`
- `Subscription::create()` with trial plan

### 3.2 Queue Management Workflow

#### A. Accessing Queue Display

```
1. User clicks clinic from dashboard
   ↓
2. System checks:
   - User authenticated? ✓
   - User belongs to tenant? ✓
   - Clinic belongs to tenant? ✓
   - User has role (admin/reception/doctor)? 
     → YES: Direct access
     → NO: Check password
   ↓
3. If password required:
   - Show password modal
   - Verify clinic/queue password (or default "1234")
   - Store clinic_id in session['allowed_clinics']
   ↓
4. Load queue display (index.blade.php)
   - Get Queue for clinic (or create default)
   - Get all SubQueues for clinic
   - Render queue cards with current/next numbers
```

#### B. Moving Queue Forward (Next Button)

```
1. User clicks "Next" button on Queue #1
   ↓
2. JavaScript: submitQueueAction('next', queueNumber, event)
   - Creates FormData with CSRF token
   - POST to /queues/{clinic}/next/{queueNumber}
   ↓
3. QueueController::next()
   - Validate request
   - Find or create SubQueue for clinic + queue_number
   - Set tenant_id if missing
   - Update: current_number = next_number, next_number += 1
   - Save to database
   ↓
4. Return JSON {success: true}
   ↓
5. Frontend: fetchQueueLive() polls every 3 seconds
   - GET /api/queue/{clinic}
   - Updates DOM with new numbers
   - Triggers text-to-speech announcement (Tamil)
   - Updates second screen if open
```

#### C. Live Updates (Polling)

```
Every 3 seconds:
1. fetchQueueLive() → GET /api/queue/{clinic}
   ↓
2. QueueController::getLiveQueue()
   - Get all SubQueues for clinic (automatically scoped by tenant)
   - Return JSON with current_number and next_number for each
   ↓
3. Frontend JavaScript:
   - Compare with previous numbers
   - If changed: Add animation, trigger speech
   - Update DOM elements
   - Update second screen window
```

### 3.3 OPD Lab Workflow

```
1. User clicks "OPD Lab" from dashboard
   ↓
2. Password modal appears
   - User enters OPD Lab password
   - POST /opd-lab/verify
   - OPDLabController::verifyPassword()
     * Check against config('opd.password')
     * Set session['opd_lab_verified'] = true
   ↓
3. Redirect to /opdLab
   - VerifyOPDLabAccess middleware checks session
   - Show OPD Lab interface
   ↓
4. User selects test type (Urine Test, FBC, ESR)
   - Enters start and end numbers
   - Clicks "Call" button
   ↓
5. JavaScript: displayTokens(start, end, labelInfo)
   - Generate token cards (numbers from start to end)
   - Update main display
   - Update second screen if open (via window.open)
   - Trigger Tamil text-to-speech announcement
   ↓
6. Second Screen:
   - Opens in new window/TV display
   - Auto-updates when main screen changes
   - Shows large token numbers for patient viewing
```

### 3.4 Subscription Management

```
1. Admin accesses "Billing & Subscription"
   ↓
2. SubscriptionController::index()
   - Get current subscription
   - Get available plans
   - Show current plan details, expiry, limits
   ↓
3. Admin clicks "Upgrade" or "Activate Plan"
   ↓
4. PlanController::activate()
   - Cancel existing active subscriptions
   - Calculate end date (monthly/yearly)
   - Create new Subscription record
   - Copy limits from Plan (max_clinics, max_users, max_screens)
   ↓
5. Middleware enforces limits:
   - EnforceClinicLimit: Blocks clinic creation beyond limit
   - EnforceScreenLimit: Blocks second screen beyond limit
   - Staff creation: Checked via SubscriptionHelper::canAddUser()
```

### 3.5 Staff Management

```
1. Admin clicks "Staff Management"
   ↓
2. StaffController::index()
   - Get all users for current tenant (via tenant->users())
   ↓
3. Admin clicks "Add Staff"
   ↓
4. StaffController::store()
   - Check if email already exists
   - If new: Create User
   - If exists: Link existing user to tenant
   - Attach to tenant_users pivot with role
   - Check subscription limit (max_users)
   ↓
5. Roles assigned:
   - admin: Full access
   - reception: Queue management
   - doctor: Queue + OPD Lab access
   - lab: OPD Lab only
   - viewer: Read-only queue viewing
```

---

## 4. USER FLOWS

### Flow 1: New Hospital Registration

```
[Public Landing Page]
    ↓
[Click "Register"]
    ↓
[Fill Registration Form]
  - Organization Name
  - Email
  - Password
    ↓
[Submit]
    ↓
[System Creates:]
  - Tenant record
  - User account (owner role)
  - 14-day trial subscription
    ↓
[Auto-login → Dashboard]
    ↓
[Welcome - No clinics yet]
```

### Flow 2: Queue Management (Receptionist)

```
[Login]
    ↓
[Dashboard - Select Clinic]
    ↓
[Enter Clinic Password]
  (or auto-access if admin/reception/doctor role)
    ↓
[Queue Display Page]
  Shows all queue lines for clinic
    ↓
[Patient arrives]
    ↓
[Click "Next" on Queue #1]
    ↓
[System Updates:]
  - Current number increments
  - Next number increments
  - Database saved
    ↓
[Live Update (3 sec):]
  - Frontend polls API
  - Number appears on main screen
  - Number appears on TV/second screen
  - Tamil announcement: "வரிசை எண் X உள்ளே வரவும்"
```

### Flow 3: OPD Lab Technician

```
[Login]
    ↓
[Dashboard - Click "OPD Lab"]
    ↓
[Enter OPD Lab Password]
    ↓
[OPD Lab Interface]
    ↓
[Select Test: "Urine Test"]
[Enter Range: 1-10]
[Click "Call"]
    ↓
[System Displays:]
  - Token numbers 1-10 on main screen
  - Token numbers 1-10 on second screen (TV)
  - Tamil announcement: "சிறுநீர் பரிசோதனை எண் 1 முதல் 10 வரை வரவும்"
    ↓
[Patients see their numbers on TV display]
```

### Flow 4: Administrator

```
[Login]
    ↓
[Dashboard]
    ↓
[Multiple Actions Available:]
  
  a) Staff Management:
     - Add/Edit/Delete staff
     - Assign roles
     - Reset passwords
  
  b) Billing & Subscription:
     - View current plan
     - Upgrade/downgrade plan
     - See limits (clinics, users, screens)
     - Check expiry date
  
  c) Queue Management:
     - Access all clinics
     - Manage queues
     - No password required (admin role bypass)
```

---

## 5. DATA FLOW

### 5.1 Database Structure

```
tenants
  ├── id
  ├── name, slug, domain
  ├── email, phone, address
  └── trial_ends_at

subscriptions
  ├── id
  ├── tenant_id (FK)
  ├── plan_id (FK)
  ├── status (active/trial/cancelled/expired)
  ├── max_clinics, max_users, max_screens
  ├── starts_at, ends_at
  └── features (JSON)

tenant_users (pivot)
  ├── tenant_id (FK)
  ├── user_id (FK)
  ├── role (owner/admin/reception/doctor/lab/viewer)
  └── is_active

users
  ├── id
  ├── name, email, password (hashed)
  └── current_tenant_id (FK)

clinics
  ├── id
  ├── tenant_id (FK)
  ├── name
  └── password (hashed)

queues
  ├── id
  ├── tenant_id (FK)
  ├── clinic_id (FK)
  ├── display
  └── password (hashed)

sub_queues
  ├── id
  ├── tenant_id (FK)
  ├── clinic_id (FK)
  ├── queue_number (1, 2, 3, ...)
  ├── current_number
  └── next_number
```

### 5.2 Query Flow (Example: Get Queue)

```
1. User requests /queues/5 (clinic ID 5)
   ↓
2. IdentifyTenant middleware:
   - Checks subdomain → finds Tenant
   - Sets app('tenant') = Tenant
   - Sets app('tenant_id') = tenant.id
   ↓
3. Route model binding:
   - AppServiceProvider binds 'clinic'
   - Query: Clinic::where('id', 5)->where('tenant_id', tenant.id)->first()
   ↓
4. QueueController::index(Clinic $clinic)
   - Clinic already scoped by tenant (from binding)
   - Queue::where('clinic_id', clinic.id)
     * TenantScope automatically adds: ->where('tenant_id', tenant.id)
   - SubQueue::where('clinic_id', clinic.id)
     * TenantScope automatically adds: ->where('tenant_id', tenant.id)
   ↓
5. Return view with data
```

### 5.3 Real-Time Update Flow

```
[Browser Tab 1: Main Queue Display]
  ↓
[Every 3 seconds:]
  JavaScript: fetchQueueLive()
    ↓
  GET /api/queue/{clinic}
    ↓
  QueueController::getLiveQueue()
    ↓
  Returns JSON: {subQueues: [{queue_number, current_number, next_number}]}
    ↓
  JavaScript updates DOM:
    - Compare numbers with previous state
    - If changed: Add animation class, trigger speech
    - Update <span id="current-number-1">, etc.
    ↓
  If second screen open:
    - Access secondScreen.document
    - Update second screen DOM
    - Reload second screen HTML

[Browser Tab 2: Second Screen/TV]
  - Separate window opened via window.open()
  - Updates via DOM manipulation from main window
  - OR auto-reloads every 3 seconds (depending on implementation)
```

---

## 6. KEY FEATURES

### 6.1 Multi-Tenancy

- **Complete Data Isolation**: Each hospital's data is completely separated
- **Automatic Scoping**: All queries automatically filtered by tenant
- **Multiple Identification Methods**: Subdomain, domain, route, or session
- **Tenant Switching**: Users can belong to multiple tenants and switch between them

### 6.2 Queue Management

- **Multiple Queue Lines**: Each clinic can have multiple queues (Queue #1, #2, #3...)
- **Next/Previous/Reset**: Full queue control
- **Real-Time Updates**: 3-second polling for live status
- **Password Protection**: Clinic-level passwords (with role bypass)
- **Auto-Create**: Queues and sub-queues created automatically when needed

### 6.3 OPD Lab System

- **Test Types**: Urine Test, Full Blood Count (FBC), ESR
- **Token Range**: Call patients by number range (e.g., 1-10)
- **Multi-Language**: English, Tamil, Sinhala labels
- **Text-to-Speech**: Tamil announcements via Web Speech API
- **Second Screen**: Large display for patient viewing
- **Password Protected**: Separate OPD Lab password

### 6.4 Subscription System

- **Plan-Based Limits**: 
  - Max clinics per tenant
  - Max users per tenant
  - Max second screens per tenant
- **Trial Period**: 14-day trial for new tenants
- **Manual Activation**: No payment gateway (admin-managed)
- **Feature Gating**: JSON-based feature flags per plan
- **Status Tracking**: Active, trial, cancelled, expired

### 6.5 Role-Based Access Control (RBAC)

- **5 Roles**:
  - `owner`: Full access, tenant management
  - `admin`: Full feature access
  - `reception`: Queue management
  - `doctor`: Queue + OPD Lab
  - `lab`: OPD Lab only
  - `viewer`: Read-only queue viewing

- **Middleware Protection**: Routes protected by role requirements
- **Permission Methods**: `canManageQueues()`, `canAccessLab()`, `isAdmin()`

### 6.6 Second Screen Support

- **TV/Monitor Display**: Opens in new window, fullscreen
- **Auto-Updates**: Synchronized with main screen
- **Cross-Window Communication**: JavaScript DOM manipulation
- **Screen Limits**: Enforced by subscription plan

---

## 7. TECHNICAL STACK

### Backend

- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL/MariaDB
- **ORM**: Eloquent with Global Scopes
- **Authentication**: Laravel Sanctum (session-based)
- **Validation**: Laravel Form Requests
- **Middleware**: Custom tenant/subscription/role middleware

### Frontend

- **Templating**: Blade (Laravel)
- **CSS Framework**: Tailwind CSS 4.0
- **JavaScript**: Vanilla JS (no frameworks)
- **Real-Time**: AJAX polling (3-second intervals)
- **Text-to-Speech**: Web Speech API (Tamil)
- **Animations**: CSS animations + Tailwind utilities

### Key Libraries/APIs

- **Tailwind CSS**: Utility-first CSS framework
- **Web Speech API**: Browser text-to-speech
- **Font Awesome**: Icons (where used)
- **Inter Font**: Typography

### Architecture Patterns

- **Multi-Tenancy**: Shared Database, Row-Level Isolation
- **Global Scopes**: Automatic tenant filtering
- **Route Model Binding**: Automatic tenant scoping
- **Service Layer**: TenantService for complex operations
- **Middleware Pipeline**: Layered security checks
- **Pivot Tables**: Many-to-many relationships (tenant_users)

---

## 8. SECURITY FEATURES

### Authentication & Authorization

- ✅ Password hashing (bcrypt)
- ✅ CSRF protection on all forms
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ Tenant isolation enforcement

### Data Protection

- ✅ Global scopes prevent cross-tenant access
- ✅ Foreign key constraints
- ✅ Route model binding with tenant scoping
- ✅ Input validation on all endpoints
- ✅ Rate limiting (5 attempts/minute on passwords)

### Access Control

- ✅ Password-protected clinics
- ✅ Password-protected OPD Lab
- ✅ Role-based route protection
- ✅ Subscription-based feature gating
- ✅ Plan-based resource limits

---

## 9. SYSTEM LIMITATIONS & CONSIDERATIONS

### Current Limitations

1. **Session-Based Screen Limits**: Can be bypassed by clearing session
2. **Polling-Based Updates**: 3-second delay (not true real-time)
3. **Default Password Fallback**: "1234" fallback for backward compatibility
4. **No Payment Gateway**: Manual subscription activation
5. **Single Database**: All tenants share same database (scales to ~1000 tenants)

### Scalability Notes

- **Current Capacity**: ~1000 tenants, ~10,000 users
- **Database**: Single MySQL instance (can add read replicas)
- **Queue Updates**: Polling works but WebSockets would be better for 100+ concurrent users
- **Second Screen**: DOM manipulation works but could use BroadcastChannel API for better sync

---

## 10. DEPLOYMENT & CONFIGURATION

### Environment Variables

```env
APP_NAME=SmartQueue
APP_ENV=production
APP_DEBUG=false
APP_URL=https://smartqueue.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=smartqueue
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=database
SESSION_LIFETIME=120

OPD_LAB_PASSWORD=your_secure_password_here
```

### Required Setup Steps

1. Run migrations: `php artisan migrate`
2. Seed plans: `php artisan db:seed --class=PlanSeeder`
3. Set OPD_LAB_PASSWORD in .env
4. Configure session driver (database recommended)
5. Set up subdomain routing (if using subdomain method)

---

## SUMMARY

SmartQueue is a **production-ready multi-tenant SaaS** system for hospital queue management. It provides:

✅ Complete tenant isolation  
✅ Real-time queue updates  
✅ Role-based access control  
✅ Subscription management  
✅ Second screen/TV display support  
✅ Multi-language support (Tamil, Sinhala, English)  
✅ OPD Lab management  
✅ Staff management  

The system is designed for **multiple hospitals** to use independently with **complete data separation**, making it suitable for a SaaS business model serving healthcare facilities.

---

**Document Version:** 1.0  
**Last Updated:** December 17, 2025  
**Status:** ✅ Production Ready
