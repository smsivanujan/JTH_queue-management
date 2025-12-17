# WebSocket Real-time Updates Implementation

This document explains the WebSocket implementation using Laravel Reverb for real-time updates in the queue management system.

## Overview

The system has been upgraded from polling-based updates to WebSocket real-time updates using Laravel Reverb. Polling remains as a fallback mechanism.

## Architecture

### Components

1. **Laravel Reverb**: WebSocket server for real-time broadcasting
2. **Broadcast Events**: `QueueUpdated` and `OPDLabUpdated` events
3. **Channel Authorization**: Tenant-isolated private channels
4. **Frontend**: Laravel Echo with Pusher JS for WebSocket connections

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

For production, use:
```env
REVERB_SCHEME=https
REVERB_PORT=443
```

### Starting Reverb Server

Run the Reverb server:

```bash
php artisan reverb:start
```

For production, run it as a service or use a process manager like Supervisor.

## Events

### QueueUpdated Event

**Location**: `app/Events/QueueUpdated.php`

**Broadcasts on**: `private-tenant.{tenantId}.queue.{clinicId}`

**Event Name**: `queue.updated`

**Payload**:
```json
{
  "subQueues": [
    {
      "clinic_id": 1,
      "queue_number": 1,
      "current_number": 5,
      "next_number": 6
    }
  ],
  "clinic_id": 1
}
```

**Fired when**:
- Queue "Next" button is clicked
- Queue "Previous" button is clicked
- Queue is reset

### OPDLabUpdated Event

**Location**: `app/Events/OPDLabUpdated.php`

**Broadcasts on**: `private-tenant.{tenantId}.opd-lab`

**Event Name**: `opd-lab.updated`

**Payload**:
```json
{
  "testLabel": "Urine Test",
  "tokens": [
    {
      "number": 1,
      "color": "white"
    }
  ],
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

**Fired when**:
- OPD Lab tokens are displayed via the `/opd-lab/broadcast` endpoint

## Channel Authorization

### Tenant Isolation

All channels are tenant-scoped to ensure data isolation:

- Queue channels: `tenant.{tenantId}.queue.{clinicId}`
- OPD Lab channels: `tenant.{tenantId}.opd-lab`

### Authorization Rules

**Location**: `routes/channels.php`

1. **Authenticated Users**: Must belong to the tenant
2. **Public Screens**: Must have a valid `screen_token` passed via `X-Screen-Token` header

The authorization automatically checks:
- Tenant membership for authenticated users
- Screen token validity and active status (within 30 seconds) for public screens

## Frontend Implementation

### Queue Screen

**Location**: `resources/views/public/queue-screen.blade.php`

**WebSocket Flow**:
1. Initializes Laravel Echo with Reverb configuration
2. Subscribes to `tenant.{tenantId}.queue.{clinicId}` channel
3. Listens for `queue.updated` events
4. Updates DOM instantly when events are received
5. Disables polling when WebSocket is connected
6. Re-enables polling if WebSocket disconnects

**Fallback Logic**:
- If WebSocket fails to connect → Uses polling (3-second interval)
- If WebSocket disconnects → Re-enables polling
- If offline → Uses offline fallback mechanism

### OPD Lab Screen

**Location**: `resources/views/public/opd-lab-screen.blade.php`

**WebSocket Flow**:
1. Initializes Laravel Echo with Reverb configuration
2. Subscribes to `tenant.{tenantId}.opd-lab` channel
3. Listens for `opd-lab.updated` events
4. Updates DOM instantly when events are received

**Broadcast Trigger**:
- When tokens are displayed in the main OPD Lab page, a POST request is sent to `/opd-lab/broadcast`
- The endpoint fires the `OPDLabUpdated` event
- All subscribed screens receive the update instantly

## Polling Fallback

Polling remains active as a fallback mechanism:

1. **When Active**:
   - WebSocket is not available or fails to connect
   - WebSocket disconnects
   - Offline state (handled by offline fallback)

2. **When Disabled**:
   - WebSocket is connected and active
   - Offline state (uses graceful retry instead)

3. **Polling Intervals**:
   - Normal: 3 seconds
   - Offline graceful retry: 15 seconds (handled by offline fallback)

## Security Considerations

1. **Tenant Isolation**: All channels are tenant-scoped
2. **Private Channels**: Only authorized users/screens can subscribe
3. **Screen Token Validation**: Public screens must provide valid, active screen tokens
4. **Signed URLs**: Public screens still use signed URLs for initial access
5. **No Sensitive Data**: Only display data is broadcast (queue numbers, token numbers)

## Testing

### Test WebSocket Connection

1. Start Reverb server: `php artisan reverb:start`
2. Open queue screen in browser
3. Check browser console for "WebSocket connected" message
4. Trigger a queue update (click "Next" button)
5. Verify instant update on all subscribed screens

### Test Polling Fallback

1. Stop Reverb server
2. Refresh queue screen
3. Check browser console for "WebSocket disconnected - polling re-enabled" message
4. Verify updates still work via polling

### Test Tenant Isolation

1. Subscribe to a channel from a different tenant
2. Verify authorization fails
3. Verify no data is received

## Production Deployment

1. Configure Reverb environment variables in `.env`
2. Set `BROADCAST_CONNECTION=reverb`
3. Start Reverb server as a service (Supervisor/systemd)
4. Configure reverse proxy (Nginx) for WebSocket support:
   ```nginx
   location /app {
       proxy_pass http://127.0.0.1:8080;
       proxy_http_version 1.1;
       proxy_set_header Upgrade $http_upgrade;
       proxy_set_header Connection "Upgrade";
       proxy_set_header Host $host;
   }
   ```
5. Update frontend configuration with production Reverb host/port

## Troubleshooting

### WebSocket Connection Fails

- Check Reverb server is running
- Verify environment variables are correct
- Check browser console for errors
- Verify network/firewall allows WebSocket connections

### Authorization Fails

- Check screen_token is valid and active
- Verify tenant_id matches
- Check `routes/channels.php` authorization logic
- Verify screen token is passed in Echo auth headers

### Events Not Received

- Check event is being fired (use Laravel logs)
- Verify channel name matches subscription
- Check event name matches listener
- Verify tenant isolation is correct

