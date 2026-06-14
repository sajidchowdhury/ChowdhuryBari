# Admin Panel Setup & Documentation

## Overview
This is a **secure, modern, premium admin panel** built for the Chowdhury Para Development Society website. It includes:

- **User Management**: Create, read, update, delete users
- **Role-Based Access Control (RBAC)**: Admin, Moderator, User roles
- **Permission System**: Granular permission control
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Security**: CSRF protection, authorization gates, secure password hashing

## Installation Steps

### 1. Register Admin Routes
Add this to `routes/web.php`:

```php
require __DIR__.'/admin.php';
```

### 2. Register Admin Middleware
In `app/Http/Kernel.php`, add to `$routeMiddleware`:

```php
'is_admin' => \App\Http\Middleware\IsAdmin::class,
```

Then update the admin route group in `routes/admin.php`:

```php
Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // routes here
});
```

### 3. Run Database Migration
```bash
php artisan migrate
```

This adds the following columns to the users table:
- `phone` - User phone number
- `address` - User address
- `role` - User role (user, moderator, admin)
- `is_active` - Account status
- `permissions` - JSON stored permissions

### 4. Set Up Authentication
Ensure Laravel's authentication is set up:

```bash
php artisan tinker
```

Then create an admin user:

```php
$user = User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
    'is_active' => true,
]);
```

## File Structure

```
resources/views/admin/
├── layout.blade.php          # Main admin layout
├── dashboard.blade.php       # Dashboard page
└── users/
    ├── index.blade.php       # Users list
    ├── create.blade.php      # Create user form
    ├── edit.blade.php        # Edit user form
    └── show.blade.php        # View user details

app/Http/Controllers/Admin/
├── AdminController.php       # Dashboard controller
└── UserController.php        # User management controller

app/Http/Middleware/
└── IsAdmin.php              # Admin access middleware

app/Providers/
└── AuthServiceProvider.php  # Authorization gates

routes/
└── admin.php                # Admin routes
```

## Security Features

### 1. Authentication
- Only logged-in, verified users can access admin panel
- Middleware checks `auth` and `verified` guards

### 2. Authorization
- **Gates** control what actions users can perform:
  - `view-admin`: View admin panel (admin/moderator)
  - `manage-users`: Manage users (admin only)
  - `edit-content`: Edit content (admin/moderator)
  - `view-reports`: View reports (admin/moderator)
  - `system-settings`: Change system settings (admin only)

### 3. CSRF Protection
- All forms include `@csrf` token
- Laravel automatically validates CSRF tokens

### 4. Password Security
- Passwords are hashed using bcrypt
- Uses Laravel's built-in password validation rules
- Requires minimum 8 characters with mixed case

### 5. User Deletion Prevention
- Users cannot delete their own accounts
- Only admins can delete other accounts
- Confirmation required before deletion

## User Roles

### Admin
- Full system access
- Can manage users
- Can change system settings
- Can view all reports

### Moderator
- Can edit content
- Can view reports
- Can moderate user activity
- Cannot manage users or system settings

### User
- Limited access
- Can view own profile
- Basic permissions only

## Using Authorization in Views

### Check if user is admin
```blade
@if(auth()->user()->role === 'admin')
    <div>Admin only content</div>
@endif
```

### Use gates in views
```blade
@can('manage-users')
    <a href="{{ route('admin.users.index') }}">Manage Users</a>
@endcan

@cannot('manage-users')
    <p>You don't have permission to manage users</p>
@endcannot
```

### Check specific permission
```blade
@can('check-permission', 'edit_content')
    <button>Edit Content</button>
@endcan
```

## API Examples

### In Controllers
```php
use Illuminate\Support\Facades\Gate;

public function someAction() {
    Gate::authorize('manage-users');
    // Your code here
}
```

### In Routes
```php
Route::middleware('auth')->group(function () {
    Route::get('admin/users', [UserController::class, 'index'])
        ->middleware('can:manage-users');
});
```

## Extending the Admin Panel

### Add New Menu Item
Edit `resources/views/admin/layout.blade.php` sidebar section:

```blade
<a href="{{ route('admin.something') }}" class="sidebar-link">
    <i class="fas fa-icon"></i>
    <span>New Page</span>
</a>
```

### Add New Gate
Edit `app/Providers/AuthServiceProvider.php`:

```php
Gate::define('new-permission', function ($user) {
    return $user->role === 'admin';
});
```

### Add New Database Field
1. Create migration: `php artisan make:migration add_field_to_users`
2. Add column in migration
3. Add to `$fillable` in User model
4. Update forms and views

## Customization

### Change Colors
Edit the CSS variables in `resources/views/admin/layout.blade.php`:

```css
:root {
    --green: #065F46;
    --green-light: #059669;
    --gold: #C9A227;
    --gold-light: #E8D48B;
}
```

### Change Sidebar Width
Edit Tailwind width class: `w-64` to preferred width

### Add More Permissions
Update the create/edit user forms with new permission checkboxes:

```blade
<label>
    <input type="checkbox" name="permissions[]" value="new_permission">
    <span>New Permission</span>
</label>
```

## Troubleshooting

### "Unauthorized" Error
- Check user role in database
- Ensure middleware is properly registered
- Verify gates are defined in AuthServiceProvider

### Routes Not Found
- Register `require __DIR__.'/admin.php'` in web.php
- Clear route cache: `php artisan route:clear`

### Database Migration Issues
- Check migration file names are unique
- Run: `php artisan migrate --fresh` (development only)

### Password Hash Not Working
- Ensure User model uses `Authenticatable`
- Verify password casting is enabled

## Performance Tips

1. **Pagination**: Users list paginates by 15 per page
2. **Database Indexing**: Add index on `role` and `is_active` columns
3. **Query Optimization**: Use eager loading for relationships
4. **Caching**: Cache user permissions if frequently checked

## Future Enhancements

- [ ] Two-factor authentication (2FA)
- [ ] Activity logging for all admin actions
- [ ] Email notifications for user actions
- [ ] User import/export functionality
- [ ] Admin action history/audit trail
- [ ] Custom fields for users
- [ ] Advanced reporting and analytics
- [ ] API token management for users
- [ ] Bulk user operations
- [ ] User role templates

## Support & Testing

### Run Tests
```bash
php artisan test
```

### Clear Application Cache
```bash
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

### Database Reset (Development)
```bash
php artisan migrate:fresh --seed
```

---

**Admin Panel Version**: 1.0.0
**Created**: June 2024
**Built with**: Laravel, Tailwind CSS, Font Awesome
