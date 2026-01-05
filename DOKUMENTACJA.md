# Wirtualna Półka - Dokumentacja Projektu

## Spis Treści
1. [Opis Projektu](#opis-projektu)
2. [Architektura Systemu](#architektura-systemu)
3. [Technologie](#technologie)
4. [Struktura Projektu](#struktura-projektu)
5. [Endpointy API](#endpointy-api)
6. [Model Danych](#model-danych)
7. [Instalacja i Uruchomienie](#instalacja-i-uruchomienie)
8. [Dane Logowania](#dane-logowania)
9. [Diagramy Przepływu](#diagramy-przepływu)

---

## Opis Projektu

**Wirtualna Półka** to system zarządzania magazynem typu "Digital Twin" (cyfrowy bliźniak), który umożliwia wizualizację i zarządzanie produktami na wirtualnych półkach sklepowych. System pozwala na:

- **Logowanie i autoryzację** użytkowników
- **Zarządzanie produktami** (dodawanie, edytowanie, usuwanie)
- **Kategoryzację produktów** w grupy tematyczne
- **Monitorowanie stanów magazynowych** z ostrzeżeniami o niskim stanie
- **Przesyłanie zdjęć produktów** do wizualizacji

---

## Architektura Systemu

System oparty jest na architekturze **SPA (Single Page Application)** z rozdzielonym frontendem i backendem:

```
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│    Frontend     │  HTTP   │     Backend     │   SQL   │    Database     │
│    (Vue 3)      │◄───────►│   (Laravel 12)  │◄───────►│    (SQLite)     │
│    Port 5173    │   API   │    Port 8000    │         │                 │
└─────────────────┘         └─────────────────┘         └─────────────────┘
```

### Warstwy aplikacji:
- **Prezentacji (Frontend)**: Vue 3, Pinia, Tailwind CSS
- **Logiki biznesowej (Backend)**: Laravel 12, Sanctum (autoryzacja)
- **Danych (Database)**: SQLite

---

## Technologie

### Backend
| Technologia | Wersja | Zastosowanie |
|-------------|--------|--------------|
| PHP | 8.2+ | Język programowania |
| Laravel | 12.x | Framework webowy |
| Sanctum | - | Autoryzacja tokenowa API |
| SQLite | 3.x | Baza danych (lekka, plikowa) |

### Frontend
| Technologia | Wersja | Zastosowanie |
|-------------|--------|--------------|
| Vue.js | 3.x | Framework UI (Composition API) |
| Pinia | - | Zarządzanie stanem aplikacji |
| Tailwind CSS | 4.x | Stylowanie komponentów |
| Axios | - | Komunikacja HTTP z API |
| Vue Router | - | Routing po stronie klienta |

---

## Struktura Projektu

```
StoreManaging/
├── backend/                    # Serwer Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── AuthController.php      # Logowanie/wylogowanie
│   │   │       ├── ProductController.php   # CRUD produktów
│   │   │       └── CategoryController.php  # CRUD kategorii
│   │   └── Models/
│   │       ├── Product.php                 # Model produktu
│   │       ├── Category.php                # Model kategorii
│   │       └── User.php                    # Model użytkownika
│   ├── database/
│   │   ├── migrations/                     # Migracje bazy danych
│   │   └── seeders/                        # Dane początkowe
│   └── routes/
│       └── api.php                         # Definicje endpointów API
│
├── frontend/                   # Klient Vue.js
│   └── src/
│       ├── components/                     # Komponenty wielokrotnego użytku
│       │   ├── ProductCard.vue
│       │   ├── ProductForm.vue
│       │   └── Modal.vue
│       ├── views/                          # Widoki (strony)
│       │   ├── LoginView.vue               # Strona logowania
│       │   └── ShelfView.vue               # Główny widok półki
│       ├── stores/                         # Stan aplikacji (Pinia)
│       │   ├── auth.ts                     # Stan autoryzacji
│       │   └── product.ts                  # Stan produktów
│       └── router/
│           └── index.ts                    # Konfiguracja routingu
│
├── README.md                   # Dokumentacja (EN)
└── DOKUMENTACJA.md             # Dokumentacja (PL)
```

---

## Endpointy API

### Autoryzacja

| Metoda | Endpoint | Opis | Autoryzacja |
|--------|----------|------|-------------|
| `POST` | `/api/login` | Logowanie użytkownika | ❌ Publiczny |
| `POST` | `/api/logout` | Wylogowanie użytkownika | ✅ Wymagana |
| `GET` | `/api/user` | Pobranie danych zalogowanego użytkownika | ✅ Wymagana |

### Produkty

| Metoda | Endpoint | Opis | Autoryzacja |
|--------|----------|------|-------------|
| `GET` | `/api/products` | Lista wszystkich produktów | ✅ Wymagana |
| `GET` | `/api/products?category_id={id}` | Filtrowanie po kategorii | ✅ Wymagana |
| `GET` | `/api/products?search={query}` | Wyszukiwanie produktów | ✅ Wymagana |
| `POST` | `/api/products` | Dodanie nowego produktu | ✅ Wymagana |
| `GET` | `/api/products/{id}` | Szczegóły produktu | ✅ Wymagana |
| `PUT/PATCH` | `/api/products/{id}` | Aktualizacja produktu | ✅ Wymagana |
| `DELETE` | `/api/products/{id}` | Usunięcie produktu | ✅ Wymagana |

### Kategorie

| Metoda | Endpoint | Opis | Autoryzacja |
|--------|----------|------|-------------|
| `GET` | `/api/categories` | Lista wszystkich kategorii | ❌ Publiczny |
| `GET` | `/api/categories/{id}` | Szczegóły kategorii | ❌ Publiczny |
| `POST` | `/api/categories` | Dodanie nowej kategorii | ✅ Wymagana |
| `DELETE` | `/api/categories/{id}` | Usunięcie kategorii | ✅ Wymagana |

---

## Specyfikacja Struktury Danych

Poniżej przedstawiono szczegółową specyfikację wszystkich tabel w bazie danych SQLite.

---

### 1. Tabela: `users`

Tabela przechowująca dane uwierzytelniające dla użytkowników systemu (zarządców magazynu).

| Pole | Typ | Opis |
|------|-----|------|
| `id` | BIGINT, Primary Key | Unikalny identyfikator użytkownika |
| `name` | VARCHAR(255) | Imię i nazwisko użytkownika |
| `email` | VARCHAR(255), Unique | Adres e-mail (używany do logowania) |
| `password` | VARCHAR(255) | Hasło przechowywane w formie zaszyfrowanej (bcrypt) |
| `email_verified_at` | TIMESTAMP, Nullable | Data weryfikacji adresu e-mail |
| `remember_token` | VARCHAR(100), Nullable | Token "zapamiętaj mnie" dla sesji |
| `created_at` | TIMESTAMP | Data utworzenia konta |
| `updated_at` | TIMESTAMP | Data ostatniej modyfikacji rekordu |

---

### 2. Tabela: `admins`

Tabela rezerwowa dla przyszłej implementacji oddzielnych kont administratorów (obecnie nieużywana).

| Pole | Typ | Opis |
|------|-----|------|
| `id` | BIGINT, Primary Key | Unikalny identyfikator administratora |
| `username` | VARCHAR(255), Unique | Nazwa użytkownika / login |
| `email` | VARCHAR(255), Unique | Adres e-mail |
| `password_hash` | VARCHAR(255) | Hasło przechowywane w formie zaszyfrowanej |
| `created_at` | TIMESTAMP | Data utworzenia konta |
| `updated_at` | TIMESTAMP | Data ostatniej modyfikacji rekordu |

> [!NOTE]
> Ta tabela istnieje w schemacie, ale system aktualnie korzysta z tabeli `users` do autoryzacji przez Laravel Sanctum.

---

### 3. Tabela: `categories`

Słownik działów magazynowych (np. Nabiał, Napoje, Pieczywo).

| Pole | Typ | Opis |
|------|-----|------|
| `id` | BIGINT, Primary Key | Unikalny identyfikator kategorii |
| `name` | VARCHAR(255) | Nazwa kategorii (np. "Nabiał") |
| `slug` | VARCHAR(255), Unique | Uproszczona nazwa do adresów URL (np. "nabial") |
| `created_at` | TIMESTAMP | Data utworzenia kategorii |
| `updated_at` | TIMESTAMP | Data ostatniej modyfikacji rekordu |

---

### 4. Tabela: `products`

Główna tabela asortymentowa przechowująca informacje o produktach w magazynie.

| Pole | Typ | Opis |
|------|-----|------|
| `id` | BIGINT, Primary Key | Unikalny identyfikator produktu |
| `name` | VARCHAR(255) | Nazwa produktu (np. "Mleko 3.2%") |
| `description` | TEXT, Nullable | Szczegółowy opis produktu |
| `image` | VARCHAR(255), Nullable | Ścieżka do pliku z wizualizacją produktu (np. `/storage/products/mleko.jpg`) |
| `category_id` | BIGINT, Foreign Key, Nullable | Przypisanie do kategorii (klucz obcy do `categories.id`) |
| `price` | DECIMAL(10,2) | Cena jednostkowa produktu w PLN |
| `stock_quantity` | INTEGER | Aktualna ilość towaru w magazynie |
| `minimum_stock` | INTEGER, Default: 5 | Minimalny stan magazynowy (próg ostrzegawczy) |
| `created_at` | TIMESTAMP | Data dodania produktu do systemu |
| `updated_at` | TIMESTAMP | Data ostatniej modyfikacji rekordu |

**Klucze obce:**
- `category_id` → `categories.id` (ON DELETE SET NULL)

---

### 5. Tabela: `sessions`

Tabela przechowująca dane sesji użytkowników (zarządzana automatycznie przez Laravel).

| Pole | Typ | Opis |
|------|-----|------|
| `id` | VARCHAR(255), Primary Key | Unikalny identyfikator sesji |
| `user_id` | BIGINT, Foreign Key, Nullable | Identyfikator zalogowanego użytkownika |
| `ip_address` | VARCHAR(45), Nullable | Adres IP użytkownika |
| `user_agent` | TEXT, Nullable | Informacje o przeglądarce użytkownika |
| `payload` | LONGTEXT | Zaszyfrowane dane sesji |
| `last_activity` | INTEGER, Index | Znacznik czasu ostatniej aktywności (UNIX timestamp) |

---

### 6. Tabela: `personal_access_tokens`

Tabela tokenów dostępu API (Laravel Sanctum) używanych do autoryzacji żądań.

| Pole | Typ | Opis |
|------|-----|------|
| `id` | BIGINT, Primary Key | Unikalny identyfikator tokenu |
| `tokenable_type` | VARCHAR(255) | Typ modelu powiązanego (np. `App\Models\User`) |
| `tokenable_id` | BIGINT | Identyfikator powiązanego modelu (np. ID użytkownika) |
| `name` | TEXT | Nazwa tokenu (np. "auth_token") |
| `token` | VARCHAR(64), Unique | Zaszyfrowany token dostępu |
| `abilities` | TEXT, Nullable | Uprawnienia tokenu w formacie JSON |
| `last_used_at` | TIMESTAMP, Nullable | Data ostatniego użycia tokenu |
| `expires_at` | TIMESTAMP, Nullable | Data wygaśnięcia tokenu |
| `created_at` | TIMESTAMP | Data utworzenia tokenu |
| `updated_at` | TIMESTAMP | Data ostatniej modyfikacji rekordu |

---

### Diagram Relacji (ERD)

```
┌─────────────────┐
│     users       │
│─────────────────│
│ id (PK)         │
│ name            │
│ email           │
│ password        │
└────────┬────────┘
         │
         │ 1:N (przez personal_access_tokens)
         ▼
┌─────────────────────────┐
│  personal_access_tokens │
│─────────────────────────│
│ id (PK)                 │
│ tokenable_id (FK)       │
│ token                   │
└─────────────────────────┘

┌─────────────────┐         ┌─────────────────┐
│   categories    │         │    products     │
│─────────────────│         │─────────────────│
│ id (PK)         │◄───────┤│ id (PK)         │
│ name            │   1:N   │ name            │
│ slug            │         │ category_id (FK)│
└─────────────────┘         │ price           │
                            │ stock_quantity  │
                            │ minimum_stock   │
                            └─────────────────┘
```

**Opis relacji:**
- **users ↔ personal_access_tokens**: Jeden użytkownik może posiadać wiele tokenów dostępu (relacja 1:N przez polimorfizm)
- **categories ↔ products**: Jedna kategoria może zawierać wiele produktów (relacja 1:N). Produkt może nie mieć przypisanej kategorii (`category_id = NULL`), co oznacza kategorię "Inne"

---

## Instalacja i Uruchomienie

### Wymagania Wstępne

- **PHP** 8.2 lub nowszy
- **Composer** (menedżer pakietów PHP)
- **Node.js** 18+ i **npm**
- **Git**

### Backend

```bash
# 1. Przejdź do katalogu backend
cd backend

# 2. Zainstaluj zależności PHP
composer install

# 3. Skopiuj plik konfiguracji środowiska
cp .env.example .env

# 4. Wygeneruj klucz aplikacji
php artisan key:generate

# 5. Utwórz bazę danych SQLite
touch database/database.sqlite

# 6. Uruchom migracje i seedery (dane początkowe)
php artisan migrate --seed

# 7. Utwórz link do przechowywania plików publicznych
php artisan storage:link

# 8. Uruchom serwer deweloperski
php artisan serve
```

Serwer będzie dostępny pod adresem: `http://localhost:8000`

### Frontend

```bash
# 1. Przejdź do katalogu frontend
cd frontend

# 2. Zainstaluj zależności Node.js
npm install

# 3. Uruchom serwer deweloperski
npm run dev
```

Aplikacja będzie dostępna pod adresem: `http://localhost:5173`

---

## Dane Logowania

> [!IMPORTANT]
> Poniższe dane są ustawiane automatycznie przez seeder bazy danych.

| Pole | Wartość |
|------|---------|
| **E-mail** | `admin@spoko.pl` |
| **Hasło** | `admin` |

### Tworzenie nowego użytkownika (opcjonalnie)

```bash
php artisan tinker
```

```php
User::factory()->create([
    'name' => 'Nowy Admin',
    'email' => 'nowy@email.pl',
    'password' => Hash::make('twoje-haslo')
]);
```

---

## Diagramy Przepływu

Poniżej znajdują się diagramy przepływu ilustrujące główne procesy w systemie:

### Przegląd Diagramów

![Diagramy przepływu - przegląd](docs/diagramy_small.png)

### Szczegółowe Diagramy

![Diagramy przepływu - szczegóły](docs/diagramy_large.png)

Diagramy przedstawiają następujące procesy:
1. **Usuwanie produktu** - przepływ usuwania produktu z potwierdzeniem
2. **Edycja produktu** - walidacja i aktualizacja danych produktu
3. **Dodawanie produktu** - tworzenie nowego produktu z walidacją
4. **Logowanie i wylogowanie** - autoryzacja użytkownika z tokenem
5. **Struktura modułów** - architektura komponentów systemu

---

## Licencja

Ten projekt jest udostępniony do celów edukacyjnych.

---

*Ostatnia aktualizacja: Styczeń 2026*
