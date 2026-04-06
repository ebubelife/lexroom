# 🛠️ LexRoom Artisan Commands Reference

This guide lists the most useful Artisan commands for managing the LexRoom application, including custom commands created for this project.

## 🧹 Custom Maintenance Commands

These commands were specifically built for LexRoom to handle database and storage cleanup.

| Command | Description |
|:--- |:--- |
| `php artisan app:clear-rooms-and-files` | **DANGER:** Deletes all rooms, messages, evidence files, and reports. Use this to reset the app's "case" state without deleting users. |
| `php artisan app:clear-rooms-and-files --force` | Same as above but skips the confirmation prompt. |
| `php artisan rooms:decrement-timers` | Decrements the active timers for all rooms. (Usually runs automatically via the scheduler). |

---

## 👤 User Management (via Tinker)

For tasks that don't have a dedicated command, we use `php artisan tinker`.

### Delete a User by Email
```bash
php artisan tinker --execute="App\Models\User::where('email', 'user@example.com')->delete();"
```

### Delete a User by ID
```bash
php artisan tinker --execute="App\Models\User::destroy(123);"
```

### Verify a User's Email Manually
```bash
php artisan tinker --execute="App\Models\User::where('email', 'user@example.com')->update(['email_verified_at' => now()]);"
```

---

## 🚀 Standard Development Commands

Commonly used Laravel commands for daily development.

### Database & Migrations
| Command | Description |
|:--- |:--- |
| `php artisan migrate` | Run outstanding migrations. |
| `php artisan migrate:rollback` | Roll back the last migration. |
| `php artisan migrate:fresh` | **DANGER:** Drops all tables and re-runs all migrations from scratch. |
| `php artisan db:seed` | Seed the database with initial/test data. |

### Application State
| Command | Description |
|:--- |:--- |
| `php artisan optimize:clear` | Clear all caches (route, config, view, etc.). Use this if the UI isn't updating. |
| `php artisan route:list` | Show a list of all registered routes and their names. |
| `php artisan storage:link` | Create the symbolic link for public file access (required for profile images). |

### Generators (Creating Files)
| Command | Description |
|:--- |:--- |
| `php artisan make:model Name -m` | Create a Model and its Migration file. |
| `php artisan make:controller NameController` | Create a new Controller. |
| `php artisan make:command Name` | Create a new custom Artisan command. |

---

## 📅 Scheduled Tasks
To run the room timers and other background tasks locally, you should have this running in a separate terminal:
```bash
php artisan schedule:work
```
