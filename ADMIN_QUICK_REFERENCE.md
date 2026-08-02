# Admin Panel - Quick Reference Guide

## 🚀 Terminal Commands

### Initial Setup
```bash
# Run migration to add admin fields to users table
php artisan migrate

# Seed test admin/moderator/user accounts
php artisan db:seed --class=AdminSeeder

# Clear caches after deployment
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Development
```bash
# Start development server
php artisan serve

# Open tinker shell for quick testing
php artisan tinker

# Run tests
php artisan test

# Make a new controller
php artisan make:controller Admin/YourController

# Make a new middleware
php artisan make:middleware YourMiddleware
```

### Database
```bash
# Create new migration
php artisan make:migration add_new_field_to_users

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh start (⚠️ deletes all data)
php artisan migrate:fresh --seed
```

---

## 👥 User Management via Tinker

```bash
php artisan tinker
```

### Create New Admin
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'New Admin',
    'email' => 'newadmin@example.com',
    'password' => Hash::make('SecurePassword@123'),
    'role' => 'admin',
    'is_active' => true,
    'phone' => '+880 1700000000',
    'permissions' => json_encode(['edit_content', 'manage_users', 'view_reports', 'system_settings']),
]);
```

### Change User Role
```php
$user = User::find(2);
$user->role = 'admin';
$user->save();
```

### Deactivate User Account
```php
User::find(3)->update(['is_active' => false]);
```

### Reset User Password
```php
$user = User::find(1);
$user->password = Hash::make('NewPassword@123');
$user->save();
```

### Give User Permissions
```php
$user = User::find(1);
$user->permissions = json_encode(['edit_content', 'view_reports']);
$user->save();
```

### List All Admin Users
```php
User::where('role', 'admin')->get();
```

### Delete User
```php
User::find(5)->delete();
```

### Export User Data
```php
User::all()->toJson();
```

---

## 🔐 Authorization Code Snippets

### In Controllers
```php
use Illuminate\Support\Facades\Gate;

class YourController extends Controller {
    public function someMethod() {
        // Check authorization
        Gate::authorize('manage-users');
        
        // Your logic here
    }
    
    // Alternative method
    public function another() {
        $this->authorize('manage-users');
    }
}
```

### In Views
```blade
{{-- Show element if authorized --}}
@can('manage-users')
    <button>Manage Users</button>
@endcan

{{-- Show alternative if not authorized --}}
@cannot('manage-users')
    <p>You don't have permission</p>
@endcannot

{{-- Check role directly --}}
@if(auth()->user()->role === 'admin')
    <div>Admin Only</div>
@endif

{{-- Check specific permission --}}
@if(Gate::allows('check-permission', 'edit_content'))
    <button>Edit</button>
@endif
```

### In Routes
```php
Route::get('admin/users', [UserController::class, 'index'])
    ->middleware('can:manage-users')
    ->name('admin.users.index');
```

---

## 📝 Common CRUD Operations

### Create User (from code)
```php
use App\Models\User;

$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'role' => 'user',
    'phone' => '+880 1234567890',
    'address' => 'Dhaka, Bangladesh',
    'is_active' => true,
]);
```

### Read User
```php
$user = User::find(1);
$user = User::where('email', 'john@example.com')->first();
$allUsers = User::all();
$admins = User::where('role', 'admin')->get();
```

### Update User
```php
$user = User::find(1);
$user->update([
    'name' => 'Jane Doe',
    'phone' => '+880 9876543210',
]);
```

### Delete User
```php
User::find(1)->delete();
User::where('role', 'user')->delete(); // Delete all regular users
```

---

## 🎨 Customizing Admin Panel

### Change Theme Colors
**File**: `resources/views/admin/layout.blade.php`

```css
:root {
    --green: #065F46;        /* Primary color */
    --green-light: #059669;  /* Light variant */
    --gold: #C9A227;         /* Accent color */
    --gold-light: #E8D48B;   /* Light accent */
}
```

### Add New Sidebar Link
**File**: `resources/views/admin/layout.blade.php`

```blade
<a href="{{ route('admin.something') }}" class="sidebar-link @if(request()->routeIs('admin.something')) active @endif">
    <i class="fas fa-icon-name"></i>
    <span>Menu Item</span>
</a>
```

### Add New Gate/Permission
**File**: `app/Providers/AuthServiceProvider.php`

```php
Gate::define('my-permission', function ($user) {
    return $user->role === 'admin' || $user->role === 'moderator';
});
```

### Add New Form Field
**File**: `resources/views/admin/users/create.blade.php`

```blade
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Field Name</label>
    <input type="text" name="field_name" class="input-field" value="{{ old('field_name') }}">
    @error('field_name')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
```

---

## 🧪 Testing Examples

### Test Login
```php
// In test file
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);

$this->actingAs($user)
    ->get('/admin')
    ->assertStatus(200);
```

### Test Authorization
```php
$admin = User::factory()->create(['role' => 'admin']);
$user = User::factory()->create(['role' => 'user']);

$this->actingAs($user)
    ->get('/admin/users')
    ->assertForbidden();

$this->actingAs($admin)
    ->get('/admin/users')
    ->assertOk();
```

### Test User Creation
```php
$admin = User::factory()->create(['role' => 'admin']);

$this->actingAs($admin)
    ->post('/admin/users', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'SecurePass@123',
        'password_confirmation' => 'SecurePass@123',
        'role' => 'user',
        'is_active' => true,
    ])
    ->assertRedirect('/admin/users');

$this->assertDatabaseHas('users', [
    'email' => 'new@example.com',
]);
```

---

## 🔧 Troubleshooting Commands

```bash
# Check if routes are registered
php artisan route:list | grep admin

# Check user roles
php artisan tinker
> User::all(['id', 'name', 'email', 'role'])->toArray();

# Verify middleware is working
php artisan route:list --path=/admin

# Check database columns
php artisan tinker
> Schema::getColumns('users');

# View all gates
php artisan tinker
> Gate::has('manage-users');

# Clear everything and start fresh
php artisan cache:clear && php artisan route:clear && php artisan view:clear
```

---

## 📚 Useful Blade Templates

### Admin Check
```blade
@admin
    <!-- Only shown to admins -->
@else
    <!-- Not admin -->
@endadmin
```

### Loading Spinner
```blade
<div class="animate-spin">
    <i class="fas fa-spinner"></i>
</div>
```

### Success Message
```blade
@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif
```

### Error Display
```blade
@if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
```

---

## 🚨 Security Reminders

✅ **Always**
- Hash passwords with `Hash::make()`
- Validate input on all forms
- Use `@csrf` on all forms
- Check authorization before actions
- Sanitize user output

❌ **Never**
- Store plain text passwords
- Use `eval()` or `exec()`
- Skip CSRF tokens
- Trust user input directly
- Expose sensitive data in views

---

## 📞 Common Issues & Fixes

### "Class not found: AdminController"
```bash
# Check if file exists in: app/Http/Controllers/Admin/AdminController.php
# If not, create it or check namespace

# Also try:
composer dump-autoload
```

### "Route not found"
```bash
# Ensure routes are registered in web.php
# Then clear route cache
php artisan route:clear
```

### "SQLSTATE[42S22]: Column not found"
```bash
# Run migrations
php artisan migrate

# Or check if column exists
php artisan tinker
> Schema::hasColumn('users', 'role');
```

### "Unauthorized" when accessing admin
```bash
# Check user role
php artisan tinker
> User::find(1)->role;

# Should return 'admin' or 'moderator'
# If not, update it
> User::find(1)->update(['role' => 'admin']);
```

---

**Last Updated**: June 2024
**Version**: 1.0.0
