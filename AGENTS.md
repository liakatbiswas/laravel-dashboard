# AGENTS.md — EFA Bangladesh

Instructions for AI coding agents (Claude Code, Codex, Cursor, etc.) working in this repository.
Read this file fully before making any change.

**File layout:** Sections 1–16 are the project's own rules (EFA architecture decisions). The block at the bottom, between `<laravel-boost-guidelines>` tags, is generated and refreshed by **Laravel Boost** — never edit inside those tags. Section 2.1 explains how the two fit together and which wins on conflict.

- **Project:** EFA Bangladesh — efabd.com — "Smart Skills for a Sustainable Future"
- **Repo:** https://github.com/liakatbiswas/efa-crafts
- **Local path (Windows, Laravel Herd):** `C:\Users\Nafi\Dropbox\Herd\efabd`
- **Current stage:** PHASE 06 — Laravel Project Foundation (implementation not yet started unless the repo proves otherwise)

---

## 1. Communication Rules

- Explain decisions, findings and plans to the maintainer **in Bangla**. Keep technical terms, Laravel terms, code, commands, file paths and identifiers in **English**.
- Code, comments, commit messages, variable names, migrations: **English**.
- Do not ask A/B/C questions for things already decided in this file or in the `docs/` architecture documents.
- Do one logical step at a time. Do not jump ahead to later steps.
- When code is requested, give complete, consolidated, copy-paste-ready files.
- Never claim a test, migration, lint run or command succeeded unless you actually executed it and saw the output. If you cannot run something, say so.
- Never claim a feature exists because it appears in a planning document. **Planned ≠ implemented.** Check the repo.

## 2. Source of Truth & Precedence

Architecture documents (keep them in `docs/architecture/`):

| File                                   | Role                                                 |
| -------------------------------------- | ---------------------------------------------------- |
| `EFA-MASTER-ARCHITECTURE-DECISIONS.md` | PHASE 01–03: long-term vision, 70 decisions          |
| `PHASE_04.md`                          | Database standards, **Version 1 scope**              |
| `PHASE_05.md`                          | Final V1 database blueprint (37 tables), constraints |
| `PHASE_06.md`                          | **Current** implementation spec (project foundation) |
| `PHASE_07.md`                          | Identity, auth, roles, OTP architecture              |

**The Master document describes the long-term vision, not Version 1.** Where documents conflict, apply this precedence (highest first):

1. Explicit instructions from the maintainer in the current conversation
2. `PHASE_06.md` (implementation spec, simplifications)
3. `PHASE_07.md` (identity/auth/RBAC/OTP — authoritative for these topics)
4. `PHASE_05.md` then `PHASE_04.md` (database + V1 scope)
5. `EFA-MASTER-ARCHITECTURE-DECISIONS.md`

Known conflicts and how to resolve them (do not silently pick your own):

| Topic                                           | Master doc says                                   | Resolution                                                                                           |
| ----------------------------------------------- | ------------------------------------------------- | ---------------------------------------------------------------------------------------------------- |
| Modules                                         | 10 modules (Crafts, Inventory, Education, …)      | **V1 has only `CMS`, `Training`, `Commerce` + `Core`/`Shared`**                                      |
| Repository / DTO / Events / Contract per module | Selective, Contracts for cross-module             | **Not required by default** (PHASE 06 §4.1). Add only with a concrete need                           |
| `User` model location                           | `app/Modules/Core/Models/User.php` (PHASE 05 §17) | **Keep `App\Models\User`** (PHASE 06 §6, PHASE 07 §20)                                               |
| `users.phone`                                   | Nullable unique (PHASE 05)                        | **Required, unique, E.164** (PHASE 07 §7)                                                            |
| Permission names                                | Examples vary (`training.course.create`)          | **`module.resource.action`**, plural resource (`training.courses.create`), catalog in PHASE 07 §11.3 |
| OTP / SMS provider                              | Required                                          | Architecture-ready; **provider not chosen yet** — do not pick one                                    |
| Session / Redis / Queue                         | Redis eventually                                  | Start simple (database); do not install Redis yet                                                    |

If you find a new conflict, **stop and report it** instead of resolving it by guessing.

### 2.1 Working together with Laravel Boost

The Boost block (bottom of this file) holds generic Laravel/tooling rules. Follow it for _how to work in the repo_ (Artisan, Pint, Herd, tests, PHP style). The EFA rules above decide _what to build and how it is structured_. Where they conflict, use this table — don't improvise.

| Topic                 | Boost says                                                                                                                      | EFA decision (wins)                                                                                                                                                                                                                                                                                                                                                                                       |
| --------------------- | ------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Test framework        | "This project uses PHPUnit"                                                                                                     | Target is **Pest** (Decision 39, PHASE 06 §14). Until Pest is actually installed, write **PHPUnit** tests (`php artisan make:test --phpunit`). Installing Pest = dependency change → **ask approval first**, planned for step 06.12. After Pest is installed, use `--pest` and ignore the PHPUnit section. Confirm with `composer show --direct`                                                          |
| New base folders      | Don't create without approval                                                                                                   | `app/Core`, `app/Modules/{CMS,Training,Commerce}`, `app/Shared` are **pre-approved** in PHASE 06 §3. Any _other_ new base folder needs approval                                                                                                                                                                                                                                                           |
| Dependencies          | Don't change without approval                                                                                                   | Same. Spatie Permission, Fortify, Pest, Larastan, a phone-number library are **planned**, but still ask before each install and verify Laravel 13/PHP 8.5 compatibility                                                                                                                                                                                                                                   |
| Model creation        | Also create factories and seeders                                                                                               | Create a **factory** for every model. Create a **seeder only** when it is in the approved list (roles/permissions, admin user, training center, category seeders). No demo seeders for ordinary models                                                                                                                                                                                                    |
| `make:model` location | Default `app/Models`                                                                                                            | Modules: pass the namespaced path, e.g. `php artisan make:model Modules/Training/Models/TrainingCourse -mf --no-interaction`, then verify the file landed in `app/Modules/Training/Models/`. `User` stays in `app/Models`                                                                                                                                                                                 |
| Documentation files   | Only if explicitly requested                                                                                                    | Applies to you. Don't generate ERD/schema/README docs unprompted; the maintainer will ask when needed (PHASE 04 §18)                                                                                                                                                                                                                                                                                      |
| APIs                  | Use API Resources + versioning                                                                                                  | **No public API in V1.** Use this only when an API is explicitly approved                                                                                                                                                                                                                                                                                                                                 |
| Tinker / scripts      | Prefer tests over tinker                                                                                                        | Agree. Never use tinker to create records on a shared database                                                                                                                                                                                                                                                                                                                                            |
| PHP version           | "Running on PHP 8.4" (detected from the local runtime)                                                                          | Project target is **PHP 8.5+** (Master doc, PHASE 06 §2). This is a **version mismatch to report**, not to fix silently. Don't change PHP via `herd php:*`, `composer.json` `require.php`, or the lockfile without approval. Until resolved, write code that runs on 8.4 and avoid 8.5-only syntax. Part of step 06.1 compatibility verification                                                          |
| Test-writing scope    | "Add tests when meaningful; don't add beyond changed behavior and failure modes; styling-only changes need no tests"            | Boost's restraint applies to ordinary changes. For the **critical workflows and checks listed in §12 and §14** (auth, roles/primary role, policies, OTP, admission approval, payments, stock/orders, certificates, migrations) tests are **mandatory** (Decision 45, PHASE 06 §14). Still no padding beyond the behavior and its important failure modes                                                  |
| Boost MCP tools       | Prefer MCP tools (`database-schema`, `database-query`, `search-docs`, `get-absolute-url`, `browser-logs`) over shell/file reads | Agree, use them. `database-schema` before writing any migration or model (PHASE 05 is the blueprint, but verify the real DB). `database-query` is **read-only**; never use it or tinker to modify data. `search-docs` before using Fortify/Sanctum/Spatie/Pest/Tailwind v4 APIs — this is how "verify compatibility, never guess versions" is done. Never print `.env` values or secrets from tool output |
| Skills activation     | Activate the matching skill from `**/skills/**` when working in its domain                                                      | Agree (e.g. `testing-best-practices` before writing tests). Skills can't override §2 precedence or §11 Security                                                                                                                                                                                                                                                                                           |
| Deployment            | Laravel Cloud + `deploying-to-cloud` skill                                                                                      | **Do not deploy, and do not activate this skill or configure Cloud**, unless the maintainer asks. Hosting is requirement-based and undecided (Decision 52); CI/CD comes later (GitHub Actions). Local dev only for now                                                                                                                                                                                    |
| Pint                  | `vendor/bin/pint --dirty --format agent`, never `--test`                                                                        | Boost wins. Run it before finishing any PHP change                                                                                                                                                                                                                                                                                                                                                        |
| `.ai/rules`           | Read `@.ai/rules/index.md` before editing if the folder exists                                                                  | Follow it too; if its rules conflict with §2 precedence or §11 Security, report the conflict                                                                                                                                                                                                                                                                                                              |

If Boost output and this file disagree and the table doesn't cover it, **stop and ask**.

---

## 3. Tech Stack (verify before installing anything)

| Layer         | Choice                                                                                               |
| ------------- | ---------------------------------------------------------------------------------------------------- |
| Backend       | Laravel 13; PHP 8.5+ is the **target** (Boost currently reports the local runtime as 8.4 — see §2.1) |
| Database      | MySQL 8.4 LTS (single DB, `utf8mb4`)                                                                 |
| Auth          | Laravel Fortify (phone + password), Sanctum foundation only                                          |
| Authorization | Spatie Laravel Permission (guard `web`) + Policies                                                   |
| Frontend      | **Blade Components only**, Tailwind CSS **v4**, Vite                                                 |
| JS            | Vanilla JS; Alpine.js only when needed                                                               |
| Tests         | Pest is the target; **PHPUnit is what Boost currently detects** — see §14                            |
| Quality       | Laravel Pint, PHPStan + Larastan                                                                     |
| Local env     | Laravel Herd (Windows)                                                                               |
| VCS           | Git + GitHub Flow (`main`, `feature/*`, `bugfix/*`, `hotfix/*`)                                      |

**Forbidden:** Vue, React, Livewire, Inertia, microservices, separate Composer packages per module, Tailwind v3-style config.

- Check official compatibility (Laravel 13 / PHP 8.5) before adding any package. **Never guess package versions.**
- Do not add packages, services or infrastructure "for later". Redis, Docker, queues workers, payment gateways, SMS providers are **not** part of the current phase.
- Tailwind v4 uses CSS-first configuration (`@import "tailwindcss"` / `@theme`), not a v3 `tailwind.config.js` by default.

## 4. Version 1 Scope

**Included**

- Public website + basic CMS (pages, posts, categories, media)
- Training: courses, batches, trainers, admission applications, enrollments, class sessions, manual attendance, leave applications, basic exams/results, certificates, basic invoices/payments
- Basic Crafts shop: categories, products, images, guest checkout, carts, orders, basic order payments

**Excluded — do NOT build, scaffold, or create tables for**
Wholesale, export management, raw-material inventory, production/BOM, warehouse, custom quotations, sample orders, entrepreneurship, full education/LMS, projects & partnerships, accounting/ledger, payment gateway integration, refunds automation, QR attendance, multi-currency, CMS versioning/editorial workflow, notification engine (WhatsApp/push), analytics, public API endpoints, external integrations.

Keep architecture _expandable_, but never implement hypothetical features. No empty directories "for the future".

## 5. Architecture: Simplified Modular Monolith

One Laravel app, one codebase, one MySQL database.

```
app/
├── Core/        # Enums, Models (non-User), Services — cross-cutting, no business-module deps
├── Modules/
│   ├── CMS/
│   ├── Training/
│   └── Commerce/
├── Shared/      # Traits, Helpers, Support — generic only
├── Http/        # Controllers, Middleware, Requests (framework-level)
├── Models/User.php
└── Providers/
```

- Create subdirectories inside a module **only when needed** (Controllers, Models, Requests, Services, Policies, Enums, Routes, Views, Database/Migrations).
- `Core` and `Shared` are **not dumping grounds**. Business-specific logic belongs to its module.
- Composer autoload stays as-is: `App\` → `app/`. **Do not add per-module PSR-4 mappings.**

### Request flow

```
Route → Middleware → FormRequest → Controller → [Service when needed] → Eloquent Model → DB
```

- **Simple CRUD:** FormRequest → Controller → Eloquent. No Service/Repository/DTO needed.
- **Business workflows:** use a **Service** (admission approval, payments, order placement, stock changes, role/primary-role changes, OTP, phone change, account status, number generation).
- Controllers: HTTP only. **No business logic in Controllers or Blade views.**
- Policies for record-level authorization. Transactions for multi-step operations.
- Repository, DTO, Events/Listeners, Jobs, extra Service Providers, custom base classes: **only with a justified, concrete requirement.**

### Module dependency rules

1. `Core` must not depend on business modules.
2. No circular dependencies between modules.
3. Controllers never call other modules' controllers.
4. Avoid cross-module Eloquent relationships; keep them limited and intentional.
5. Cross-module operations go through an explicit integration point (a public service or contract) — do not reach into another module's internals.
6. Do not build a custom module framework, auto-discovery or module manager package.

### Module registration

- Use Laravel's native service providers (registered in `bootstrap/providers.php`).
- A module provider may register only: routes, views, migrations.
- Module migrations path (`app/Modules/<Module>/Database/Migrations`): **verify actually discovered** (`php artisan migrate:status`) via `loadMigrationsFrom()` before relying on it. Do not assume subdirectories are auto-loaded.

## 6. Existing Project Preservation

The Laravel project already exists. **Do not create a new Laravel project. Do not delete or overwrite working files without justification.**

Before changing anything, inspect: `composer.json`, `composer.lock`, `package.json`, `package-lock.json`, `app/Models/User.php`, `app/Http/`, `app/Providers/`, `bootstrap/providers.php`, `routes/`, `database/migrations/`, `database/seeders/`, `config/auth.php`, `config/fortify.php`, `config/permission.php`, `tests/`.

No database schema changes during the initial inspection.

## 7. Mandatory Workflow

1. **Inspect** the relevant existing files.
2. **Report** findings, issues, conflicts with the architecture documents.
3. **Plan** — short and specific.
4. **Wait for approval** before anything risky, destructive, or architecture-changing (schema changes, moving/deleting files, installing packages, changing config).
5. **Implement** only the current sub-step.
6. **Validate** — run tests, Pint, PHPStan, and relevant app checks.
7. **Report** what changed and what remains.

Do not repeatedly ask the maintainer for routine Git command output; run it yourself if you have shell access. If you have no filesystem access, ask for specific files — do not pretend to inspect.

Before a major feature, state: its architecture, dependencies, database impact, and security considerations.

### PHASE 06 order (do not skip ahead)

06.1 Compatibility verification → 06.2 Git init → 06.3 Folder architecture → 06.4 Structure migration → 06.5 Module registration → 06.6 Environment/config → 06.7 MySQL connection → 06.8 Fortify → 06.9 Spatie → 06.10 Core User & profiles → 06.11 Migrations & seeders → 06.12 Testing & quality → 06.13 Final review.

Decisions for 06.1–06.3 are already made; revalidate only if needed, don't redo.

## 8. Commands

Run from the project root. Confirm these exist in the repo before relying on them.

```bash
composer install && npm install          # dependencies
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed         # local only, never on production
php artisan test --compact               # narrowest scope first: add a file path or --filter=Name
vendor/bin/pint --dirty --format agent   # format changed files (Boost rule; do NOT use --test)
vendor/bin/phpstan analyse                 # static analysis (once Larastan is installed)
npm run dev                              # Vite dev (or `composer run dev`)
npm run build                            # production build
```

Herd serves the app at the project's `.test` domain (derived from the project directory name; use the Boost `get-absolute-url` tool for the exact URL before sharing it). **Never run `artisan serve` or any command to serve the site** — it is always available. Use `herd` CLI for services/PHP versions.

Pass `--no-interaction` to every `php artisan make:*` command. Prefer `make:` commands over hand-created files. Use named routes and `route()` for links.

If a frontend change doesn't appear or Vite manifest is missing: `npm run build` or ask the maintainer to run `npm run dev`.

Before reporting work as done: Pint run on changed files, PHPStan clean (once installed), relevant tests passing — all actually executed.

## 9. Database Standards

- Laravel **migrations are the source of truth** for schema. Keep schema documentation in sync.
- Tables: plural snake*case with module prefix (`training*`, `commerce*`, `cms*`). Columns: snake_case. FK: `{singular}_id`. Booleans: `is_`/`has\_`.
- PK: `$table->id()` (BIGINT UNSIGNED). ULID only for justified public identifiers.
- Status: **string column + PHP backed Enum + Eloquent enum cast + FormRequest enum validation.** Never DB-native `ENUM`. Transition rules live in Services.
- Money: `DECIMAL(12,2)`. **Never FLOAT/DOUBLE.** Percentage: `DECIMAL(5,2)`.
- Timestamps stored in UTC, displayed in `Asia/Dhaka`. Soft deletes only when justified.
- JSON only for genuinely flexible data or immutable snapshots.
- Spatie tables (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) keep their **standard names and schema**.
- Specify FK delete behavior **explicitly** in every migration. Never cascade-delete financial, academic, certificate or order-history records (use `restrict`). `set null` for optional creators / CMS category; `cascade` acceptable for cart items.
- Index FKs and real query patterns only. No duplicate indexes, no speculative indexing.
- Migration order and unique-constraint list: follow `PHASE_05.md` §12, §13, §16.
- Never edit an already-run migration that may exist on a shared/production DB; add a new one.
- No uncontrolled schema changes on production. Production migrations are reviewed.

## 10. Identity, Auth & Authorization (PHASE 07)

- **One** `App\Models\User` (uses Spatie `HasRoles`). No separate auth models for Student/Trainer/Staff/Admin.
- Profiles: `student_profiles`, `trainer_profiles`, `staff_profiles` — `user_id` unique FK each. A user may hold several. Removing a role never deletes a profile.
- **Login = phone + password.** No email login. Phone stored in **E.164** (`+8801712345678`); accept `01…`, `8801…`, `+8801…` and normalize with a proper library, not string replace.
- `users.status` ∈ `active | inactive | suspended` (string + backed Enum). Only `active` accounts may access protected areas. Super Admin bypass must **never** reactivate/override account status.
- Roles: Super Admin, Admin, Staff, Trainer, Student. Multiple roles allowed + one `primary_role_id`, which **must** be among the assigned roles — enforce in the role-management Service inside a transaction, plus tests (a FK can't enforce it).
- Only Super Admin manages roles/permissions; prevent self-lockout.
- Permissions: `module.resource.action`, based on real V1 functionality only (see PHASE 07 §11.3). Direct user permissions are exceptional.
- Authorization layers: Spatie permission → Gate/Policy (record-level) → middleware → FormRequest `authorize()`. Hiding a button is **not** authorization. A "view" permission ≠ access to every record (Student → own records; Trainer → assigned batches).
- **Do not enable default Fortify public registration or email login.** Registration is controlled:
    - Student: admission application → OTP → admin approval → account created/linked → activation.
    - Trainer / Staff: admin invitation/creation → phone verification → password setup → admin activation.
    - Buyer: guest checkout; account optional.
- Admission approval is one DB transaction (validate → create/link user → profile → enrollment → update application → audit). Never create a duplicate user for an existing phone.
- Identifiers (`student_number`, `trainer_number`, `employee_number`, order/invoice/payment/certificate numbers): generated by a service, safe under concurrency, DB-unique. **Never `MAX(id)+1`.**

### OTP

- Purposes are isolated: `verify_phone`, `reset_password`, `change_phone`, `complete_invitation`.
- Defaults: 6 digits, 5 min expiry, 5 attempts, 60 s resend cooldown, hashed storage, single use, rate limited.
- Table `core_phone_verification_challenges`; consume with transaction + row lock.
- Generic responses for password reset (don't reveal whether an account exists). Invalidate sessions after password reset.
- A failed SMS send must never be reported as success. Keep SMS behind an adapter (`PhoneOtpService → SMS delivery layer → provider`). Credentials via `.env` only.
- Do not update `users.phone` before the new number's OTP is verified.

## 11. Security Rules (non-negotiable)

1. Never log or store plaintext passwords, OTPs, tokens or secrets (also not in audit logs or exceptions).
2. Never commit `.env` or any secret. No hardcoded credentials, including admin passwords.
3. Validate all input with FormRequests; protect against mass assignment (explicit `$fillable`, never `$guarded = []` on sensitive models; never mass-assign `status`, role, or `primary_role_id` from request data).
4. Authorize server-side for every protected operation.
5. Consider CSRF, XSS (escape Blade output; avoid `{!! !!}` on user content), SQL injection (Eloquent/query builder only — no concatenated SQL), open redirects, IDOR.
6. Sensitive files (student documents, certificates, private reports) must not be publicly accessible; public and private storage are separated.
7. Payment and order processing must be **idempotent**; use `DB::transaction()` and `lockForUpdate()` for stock decrement, payment totals, OTP consumption, number generation. No external API calls inside long DB transactions.
8. Production: `APP_DEBUG=false`, HTTPS, secure + HttpOnly cookies, appropriate SameSite, session regeneration on login, invalidate on logout.
9. Deactivate accounts; don't delete them. Don't delete payment history.
10. Collect only necessary personal data.

## 12. Business Rules to Enforce in Services (+ tests)

- Application's course == enrollment batch's course. One application → at most one enrollment.
- `UNIQUE(student_profile_id, batch_id)` for enrollments; `student_number` lives only on `student_profiles`.
- Attendance session and enrollment belong to the same batch; unique `(class_session_id, student_enrollment_id)`.
- Exam and enrollment share a batch; marks ≤ total marks; unique `(examination_id, student_enrollment_id)`.
- One certificate per enrollment, only if eligible; unique certificate number; public verification by number/ULID.
- Invoice paid amount is **computed** from qualifying payments (don't store `paid_amount`); payments must not exceed due amount; fee snapshot at enrollment/invoice time.
- Order items snapshot name, SKU, unit price, quantity; `product_id` nullable to preserve history.
- Order confirmation + stock deduction in one transaction with row lock; cancellation restores stock transactionally. No overselling.
- Page and post slugs share one public namespace — validate global slug conflicts in the application.
- `commerce_customers.user_id` nullable + unique (guest checkout); guest carts use a non-guessable `guest_token`.

## 13. Coding Conventions

- PSR-12 and Laravel conventions. **Check sibling files first** and match their structure, naming and style; reuse existing components before writing new ones.
- PHP style (from Boost): always use curly braces for control structures; constructor property promotion (no empty zero-arg constructors); explicit return types and parameter types everywhere; **TitleCase Enum cases** (`Active`, `UnderReview`); prefer PHPDoc (with array shapes) over inline comments; descriptive names (`isEligibleForCertificate`, not `check()`). Use `declare(strict_types=1)` only if the project already does.
- Constructor dependency injection.
- Prefer Laravel-native solutions over custom abstractions. Don't add a layer, pattern, interface or package unless a concrete requirement justifies it.
- Eloquent models own relationships, casts, scopes — **not** business workflows.
- Enums live in their module (`Modules/Training/Enums`, …); cross-cutting enums in `Core/Enums` or `Shared`.
- Seeders: standard Laravel structure, **idempotent** (`firstOrCreate` / `updateOrCreate`), no demo data in production seeders, no hardcoded production admin password (read from env / prompt / one-time command).
- Default locale `bn`, secondary `en`; timezone `Asia/Dhaka`; Bangla/English content uses `_bn` / `_en` columns (no translation table in V1). Use `utf8mb4`.
- UI: Blade components in `resources/views/components`, module views inside the module, mobile-first, light + dark mode, Tailwind v4.
- Public URLs: `/bn/...` and `/en/...` (language-aware).
- Commits: small and focused; branch per `feature/*` / `bugfix/*`; never push directly to `main`.

## 14. Testing

Target framework is **Pest**; until it is installed (planned in 06.12, needs approval) write PHPUnit tests — see §2.1. Test real behavior; no decorative tests. Most tests should be **feature tests** (`php artisan make:test`, `--unit` only for pure logic). Use model **factories** (check for existing states first). Run the narrowest set of tests that covers the change and rerun after each fix. Don't write tinker/verification scripts when a test covers it. Priority for this phase:

- App boot, DB connection, fresh migrate / rollback / re-migrate, seeder idempotency
- Phone login (valid, wrong password, unknown phone, unverified, inactive, suspended), session regeneration, logout
- Roles: multiple roles, primary-role validity and change, role/profile consistency, Super Admin bypass (does not override account status)
- Policies: student own-record vs cross-record, trainer assigned/unassigned batch
- OTP (when implemented): expiry, attempts, reuse, purpose isolation, cooldown, concurrency
- Module route registration and view loading
- Critical workflows (admission approval, payments, stock/order) must have automated tests before they are considered done

## 15. Definition of Done (PHASE 06)

- Existing Laravel project preserved; approved folder structure in place
- Module registration works; migrations discovered and run
- Environment/config stable; MySQL connection verified
- Fortify (phone login) and Spatie authorization work
- `User` + profile foundation works; initial migrations and seeders execute
- Pest, Pint, PHPStan pass (actually executed)
- No unresolved critical foundation issues; implementation decisions documented

## 16. Final Principle

Priorities, in order: **correct business logic → data integrity → security → maintainability → testability → expandability.**

Keep the codebase simple, modular, secure and production-ready. Do not add complexity without a genuine requirement. Do not implement hypothetical features. Do not turn simple CRUD into a multi-layer architecture. When in doubt, inspect the repo, report, and ask for approval — don't guess.

---

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:

- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
    - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
