# Enterprise Metrics Dashboard Documentation

## Overview

The Enterprise Metrics Dashboard provides system-wide analytics and business intelligence for investors and stakeholders. This dashboard is **read-only** and accessible only to admin users. It aggregates data across all tenants while maintaining tenant isolation at the data level.

## Access Control

- **Route**: `/metrics`
- **Authentication**: Required (must be logged in)
- **Authorization**: Admin role only
- **Tenant Scope**: System-wide (bypasses tenant scoping for metrics)
- **Data Exposure**: No personal or patient data is exposed

## Metrics Overview

### 1. Total Tenants

**Definition**: Total number of registered tenant organizations in the system.

**Calculation**: Count of all records in the `tenants` table (including inactive and soft-deleted).

**Purpose**: Provides baseline count of all registered organizations.

**Limitations**: 
- Includes inactive tenants
- Includes soft-deleted tenants
- Does not indicate current active usage

---

### 2. Active Tenants (Last 30 Days)

**Definition**: Count of tenants that have shown activity in the last 30 days.

**Calculation**: 
- Tenants with `is_active = true`
- AND have activity in the last 30 days (either:
  - Screen usage logs with `started_at >= 30 days ago`
  - Subscription updates with `updated_at >= 30 days ago`)

**Purpose**: Indicates current engagement and usage of the platform.

**Activity Indicators**:
- Screen registrations/usage
- Subscription changes
- User activity (indirectly via subscriptions)

**Limitations**:
- Does not capture all types of activity (e.g., queue updates, OPD Lab token displays)
- Based on data availability in logs
- 30-day window may not reflect longer-term engagement patterns

---

### 3. Active Paid Subscriptions

**Definition**: Count of subscriptions that are currently active and paid.

**Calculation**:
- `status = 'active'`
- AND (`ends_at` is null OR `ends_at > now()`)
- AND associated plan has `price > 0`

**Purpose**: Indicates revenue-generating subscriptions.

**Limitations**:
- Does not distinguish between different billing cycles
- Free plans are excluded
- Does not account for manually adjusted pricing or discounts

---

### 4. Monthly Recurring Revenue (MRR)

**Definition**: Total monthly revenue from all active paid subscriptions.

**Calculation**:
- Sum of all active subscription plan prices
- Annual plans are converted to monthly equivalent: `price / 12`
- Monthly plans use price as-is

**Formula**:
```
MRR = Σ(plan_price / billing_period_months)
```

**Example**:
- 5 subscriptions @ $100/month = $500
- 2 subscriptions @ $1000/year = $166.67/month
- Total MRR = $666.67

**Purpose**: Key SaaS metric for investors and business planning.

**Important Limitations**:
- ⚠️ **Assumes subscription-based billing**: If billing is manual or invoice-based, MRR may not reflect actual revenue
- ⚠️ **Based on plan prices only**: Does not account for:
  - Discounts or promotional pricing
  - Custom pricing arrangements
  - Overdue or unpaid subscriptions
  - Trial periods
  - One-time fees or setup charges
- ⚠️ **Does not reflect actual payments**: This is a projection based on subscription status, not actual cash received

**Recommendation**: Verify actual revenue against payment records for accurate financial reporting.

---

### 5. Active Screens Today

**Definition**: Count of screens currently displaying (active within last 30 seconds).

**Calculation**: 
- Count of `active_screens` where `last_heartbeat_at >= now() - 30 seconds`

**Purpose**: Real-time indicator of current platform usage.

**Limitations**:
- Reflects screens with active WebSocket/heartbeat connection
- May not capture all screens (e.g., offline screens, screens with connection issues)
- 30-second window may miss briefly disconnected screens

---

### 6. Total Screen Hours

**Definition**: Cumulative screen usage hours across all tenants (all-time).

**Calculation**:
- Sum of `duration_seconds` from `screen_usage_logs`
- Only includes completed sessions (`ended_at IS NOT NULL`)
- Converted to hours: `total_seconds / 3600`

**Purpose**: Indicates overall platform usage and engagement.

**Limitations**:
- Only includes completed sessions (active sessions not yet ended are excluded)
- Based on heartbeat tracking (sessions are closed when heartbeat expires)
- Does not account for screens that were never properly closed

**Usage Patterns**:
- Higher hours indicate active usage
- Useful for capacity planning
- Can identify usage trends over time

---

### 7. Usage by Screen Type

**Definition**: Breakdown of screen usage hours by screen type (Queue vs OPD Lab).

**Calculation**:
- Same as Total Screen Hours, but grouped by `screen_type`
- Returns: `['queue' => hours, 'opd_lab' => hours]`

**Purpose**: 
- Identify which features are most used
- Product development insights
- Resource allocation decisions

**Screen Types**:
- **Queue**: Queue management screens showing current/next numbers
- **OPD Lab**: OPD Lab token display screens

**Limitations**: Same as Total Screen Hours

---

### 8. Subscription Breakdown by Plan

**Definition**: Count of active subscriptions grouped by plan name.

**Calculation**:
- Active subscriptions grouped by associated plan name
- Shows distribution of subscriptions across different plan tiers

**Purpose**:
- Understand subscription distribution
- Identify popular plans
- Pricing strategy insights

**Limitations**:
- Only shows active subscriptions
- Plan names may change over time (historical data may show outdated names)

---

### 9. Tenants by Status

**Definition**: Count of tenants grouped by active/inactive status.

**Calculation**:
- Active: `is_active = true`
- Inactive: `is_active = false`

**Purpose**: Understand tenant account status distribution.

**Limitations**:
- `is_active` is a manual flag, not automatically updated
- Does not reflect actual usage patterns

---

## Technical Implementation

### Data Access Pattern

The MetricsController bypasses tenant scoping to aggregate system-wide data:

```php
Tenant::withoutGlobalScope(TenantScope::class)->count()
```

This ensures:
- System-wide metrics are accessible
- Tenant isolation is preserved for regular operations
- Admin users can view aggregate metrics

### Performance Considerations

- Queries use database indexes where available
- Aggregations are performed at the database level
- No data is cached (real-time metrics)
- For large datasets, consider adding caching or scheduled aggregation

### Security

- **Authentication Required**: Must be logged in
- **Authorization Check**: Must be admin role
- **Read-Only**: No write operations
- **No Personal Data**: No user names, emails, or patient data exposed
- **No Tenant Data**: Individual tenant details are not exposed, only aggregates

---

## How to Access

1. Log in as an admin user
2. Navigate to `/metrics` or click "Enterprise Metrics" link (if available in navigation)
3. Dashboard displays automatically

---

## Metric Definitions Summary

| Metric | Type | Time Range | Data Source |
|--------|------|------------|-------------|
| Total Tenants | Count | All-time | `tenants` table |
| Active Tenants | Count | Last 30 days | `tenants` + `screen_usage_logs` + `subscriptions` |
| Active Paid Subscriptions | Count | Current | `subscriptions` + `plans` |
| Monthly Recurring Revenue | Currency | Current | `subscriptions` + `plans` |
| Active Screens Today | Count | Current (last 30 sec) | `active_screens` |
| Total Screen Hours | Hours | All-time | `screen_usage_logs` |
| Usage by Type | Hours | All-time | `screen_usage_logs` |
| Subscription Breakdown | Count | Current | `subscriptions` + `plans` |
| Tenants by Status | Count | Current | `tenants` |

---

## Limitations and Caveats

### General Limitations

1. **Real-Time Metrics**: All metrics are calculated in real-time. For large datasets, this may impact performance.

2. **Data Completeness**: Metrics depend on data being properly logged. Missing or incomplete logs will affect accuracy.

3. **Tenant Isolation**: While metrics are system-wide, the underlying data still maintains tenant isolation. This ensures data security.

4. **No Historical Trends**: Current implementation shows current state only. Historical trends would require additional implementation.

### Specific Limitations

1. **MRR Calculation**:
   - Does not account for manual billing
   - Does not reflect actual payments received
   - Assumes all subscriptions are paid on time
   - Does not include one-time fees

2. **Active Tenants**:
   - 30-day window may not capture all activity types
   - Depends on proper logging of screen usage and subscriptions

3. **Screen Usage**:
   - Only includes completed sessions
   - Active sessions not yet closed are excluded from totals
   - Based on heartbeat tracking (may miss brief disconnections)

---

## Future Enhancements

Potential improvements for the metrics dashboard:

1. **Historical Trends**: Add charts showing metrics over time
2. **Date Range Filters**: Allow filtering metrics by date range
3. **Export Functionality**: Export metrics to CSV/Excel
4. **Caching**: Cache metrics for better performance
5. **Real-Time Updates**: WebSocket updates for real-time metrics
6. **Custom Date Ranges**: Allow custom date ranges for active tenants
7. **Revenue Tracking**: Track actual revenue vs. MRR projection
8. **Usage Trends**: Show usage patterns over time (daily, weekly, monthly)

---

## Support and Questions

For questions about metrics calculations or dashboard functionality, contact the development team or refer to the source code in:
- `app/Http/Controllers/MetricsController.php`
- `resources/views/metrics/dashboard.blade.php`

