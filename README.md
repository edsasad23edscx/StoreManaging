# Wirtualna Półka - Virtual Shelf Project

## Overview
A "Digital Twin" store shelf management system.
- **Backend**: Laravel 12 (API, Sanctum)
- **Frontend**: Vue 3 (Composition API, Pinia, Tailwind 4)
- **Database**: SQLite

## Completed Work
- **Backend Structure**: Configured SQLite, created [Product](file:///c:/Users/dzevi/Desktop/StoreManaging/frontend/src/components/ProductCard.vue#4-12) model, migration, factory, seeder, and controllers ([AuthController](file:///c:/Users/dzevi/Desktop/StoreManaging/backend/app/Http/Controllers/AuthController.php#8-39), [ProductController](file:///c:/Users/dzevi/Desktop/StoreManaging/backend/app/Http/Controllers/ProductController.php#8-66)).
- **Frontend Structure**: Setup Axios, Pinia Stores (`auth`, `product`), Reusable Components (`ProductCard`, [Modal](file:///c:/Users/dzevi/Desktop/StoreManaging/frontend/src/views/ShelfView.vue#40-44), `Input`), and Views (`LoginView`, `ShelfView`).
- **Styling**: Premium "Dark/Light" mode compatible design with Tailwind CSS.

## How to Run

> [!IMPORTANT]
> The development environment seems to be missing [php](file:///c:/Users/dzevi/Desktop/StoreManaging/backend/routes/api.php), `npm`, and `git` in the system PATH. You must ensure these are accessible or run the commands in an environment that supports them.

### Backend
1. Navigate to backend:
   ```bash
   cd backend
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Setup Database:
   ```bash
   # Ensure database.sqlite exists
   touch database/database.sqlite
   php artisan migrate --seed
   ```
4. Generate Key:
   ```bash
   php artisan key:generate
   ```
5. Serve:
   ```bash
   php artisan serve
   ```

### Frontend
1. Navigate to frontend:
   ```bash
   cd frontend
   ```
2. Install dependencies:
   ```bash
   npm install
   ```
3. Run Dev Server:
   ```bash
   npm run dev
   ```

## Credentials
- The Seeder allows creating a user. You may need to create one manually using `php artisan tinker`:
  ```php
  User::factory()->create(['email' => 'admin@store.com', 'password' => 'password']);
  ```
- Use `admin@store.com` / `password` to login.
