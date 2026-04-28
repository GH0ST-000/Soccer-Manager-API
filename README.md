# Soccer Manager API

A RESTful Soccer Manager API built with Laravel 13, JWT authentication, a clean Service + Repository + Action architecture, and **100% test coverage** (Pest).

## Highlights

- JWT auth via `tymon/jwt-auth` (register, login, logout with token blacklist)
- Each user owns exactly one team, auto-created on registration with 20 players (3 GK, 6 DEF, 6 MID, 5 ATT) and a $5,000,000 budget
- Atomic transfer flow with row locking, budget validation, ownership transfer, and a 10–100% random market value increase
- Localization: English (default) and Georgian via `Accept-Language` header (`en` / `ka`)
- 100% line coverage and 100% type coverage enforced

## Architecture

```
app/
├── Actions/         # Single-purpose workflows (Register, CreateInitialTeam, GeneratePlayers, BuyPlayer, ...)
├── Enums/           # PlayerPosition, TransferListingStatus
├── Http/
│   ├── Controllers/Api
│   ├── Middleware/  # LocalizationMiddleware
│   ├── Requests/    # Form requests (validation)
│   └── Resources/   # API JSON resources
├── Models/          # Eloquent models
├── Policies/        # Ownership-based authorization
├── Repositories/    # Database abstraction (interfaces + implementations)
└── Services/        # Business logic facade
```

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

Make sure `JWT_SECRET` is present in `.env`.

### Demo accounts (seeded)

Running `php artisan migrate --seed` (or `migrate:fresh --seed`) creates 5 users, each with a team of 20 players and 3 active transfer listings.

| Email                | Password   |
| -------------------- | ---------- |
| `test@example.com`   | `password` |
| `luka@example.com`   | `password` |
| `nika@example.com`   | `password` |
| `giorgi@example.com` | `password` |
| `mariam@example.com` | `password` |

## Run tests (100% coverage required)

```bash
composer test
```

This executes:

- `pest --type-coverage --min=100`
- `pest --parallel --coverage --exactly=100.0`
- `pint --parallel --test` and `rector --dry-run`
- `phpstan` (level max with Larastan)

## API

All authenticated endpoints require `Authorization: Bearer <token>`.

### Auth

| Method | Path           | Description                            |
| ------ | -------------- | -------------------------------------- |
| POST   | `/api/register`| Register, create team & roster, return JWT |
| POST   | `/api/login`   | Authenticate and return JWT            |
| POST   | `/api/logout`  | Invalidate the current JWT             |

### Team

| Method | Path        | Description              |
| ------ | ----------- | ------------------------ |
| GET    | `/api/team` | Show authenticated team  |
| PUT    | `/api/team` | Update team name/country |

### Players

| Method | Path                   | Description                       |
| ------ | ---------------------- | --------------------------------- |
| GET    | `/api/players`         | List players for current team     |
| PUT    | `/api/players/{id}`    | Update first/last name or country |

### Transfer Market

| Method | Path                                         | Description                              |
| ------ | -------------------------------------------- | ---------------------------------------- |
| GET    | `/api/transfer-list`                         | List active transfer listings (filterable) |
| POST   | `/api/players/{id}/transfer-list`            | List one of your players for sale        |
| DELETE | `/api/players/{id}/transfer-list`            | Cancel an active listing                 |
| POST   | `/api/transfer-list/{id}/buy`                | Purchase a player atomically             |

Filters supported on `/api/transfer-list`: `team_name`, `player_name`, `country`, `min_price`, `max_price`, `per_page`.

## Localization

Send `Accept-Language: en` (default) or `Accept-Language: ka` to localize all response messages.

## Database Schema

- `users` — id, name, email, password
- `teams` — id, user_id (unique), name, country, budget
- `players` — id, team_id, first_name, last_name, country, position, age, market_value
- `transfer_listings` — id, player_id, seller_team_id, asking_price, status (`active`/`sold`/`cancelled`)
- `transactions` — id, player_id, seller_team_id, buyer_team_id, price, old_value, new_value
# Soccer-Manager-API
