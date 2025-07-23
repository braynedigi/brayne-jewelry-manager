# Real-Time Updates Setup Guide

## Overview
The jewelry management system now includes real-time updates using WebSocket technology. This enables:
- Live notifications when order status changes
- Real-time dashboard updates without page refresh
- Immediate popup notifications for status changes

## Configuration

### 1. Environment Variables
Add the following to your `.env` file:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=log

# For production with Pusher (recommended)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=mt1

# For local development (using log driver)
BROADCAST_DRIVER=log
```

### 2. Pusher Setup (Recommended for Production)
1. Sign up at [pusher.com](https://pusher.com)
2. Create a new app
3. Copy your app credentials to `.env`
4. Update `BROADCAST_DRIVER=pusher`

### 3. Local Development Setup
For local development, the system uses the `log` driver which logs events to `storage/logs/laravel.log`.

## Features Implemented

### 1. Real-Time Notifications
- **Order Status Changes**: Immediate notifications when order status is updated
- **Role-Based Channels**: Different notifications for admin, factory, and distributor users
- **Toast Notifications**: Beautiful popup notifications with action buttons

### 2. Live Dashboard Updates
- **Real-Time Counters**: Order counts update automatically
- **Status Transitions**: Orders move between status columns in real-time
- **Chart Updates**: Dashboard charts update with new data

### 3. WebSocket Events
- `OrderStatusChanged`: Broadcasts when order status changes
- `NewNotification`: Broadcasts general notifications

### 4. Channel Authorization
- `private-admin`: Admin users only
- `private-factory`: Factory users only
- `private-distributor.{id}`: Specific distributor
- `private-user.{id}`: User-specific notifications
- `private-notifications`: General notifications

## Usage

### 1. Automatic Updates
Real-time updates work automatically when:
- Order status is changed via the web interface
- Status is updated through the API
- Factory updates production status

### 2. Manual Broadcasting
To manually broadcast events:

```php
// Broadcast order status change
broadcast(new OrderStatusChanged($order, $oldStatus, $newStatus, $user, $notes))->toOthers();

// Broadcast general notification
broadcast(new NewNotification($title, $message, $type, $targetUser))->toOthers();
```

### 3. Frontend Integration
The real-time components are automatically included in the main layout:
- `components.real-time-notifications`: Handles notifications
- `components.live-dashboard`: Handles dashboard updates

## Testing

### 1. Local Testing
1. Set `BROADCAST_DRIVER=log` in `.env`
2. Check `storage/logs/laravel.log` for broadcast events
3. Update order status to see events logged

### 2. Production Testing
1. Set up Pusher credentials
2. Update order status
3. Verify real-time notifications appear

## Troubleshooting

### 1. Notifications Not Appearing
- Check browser console for JavaScript errors
- Verify Pusher credentials are correct
- Ensure user is authenticated

### 2. Dashboard Not Updating
- Check if user has proper role permissions
- Verify channel authorization is working
- Check network connectivity to Pusher

### 3. Performance Issues
- Monitor Pusher usage limits
- Consider using Redis for high-traffic applications
- Implement rate limiting if needed

## Security

### 1. Channel Authorization
All channels require proper authorization:
- Users can only access channels they're authorized for
- Role-based access control is enforced
- Order-specific channels verify ownership

### 2. Data Validation
- All broadcast data is validated before sending
- Sensitive information is filtered out
- User permissions are checked before broadcasting

## Future Enhancements

### 1. Additional Events
- Product updates
- Customer notifications
- System alerts

### 2. Advanced Features
- Typing indicators
- Read receipts
- Message history

### 3. Performance Optimizations
- Redis clustering
- Load balancing
- Caching strategies 