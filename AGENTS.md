<laravel-boost-guidelines>
=== .ai/configure-nightwatch rules ===

# Nightwatch

- Activate the `configure-nightwatch` skill when setting up Laravel Nightwatch, managing data volume, protecting sensitive data (PII), configuring sampling rates, filtering rules, or redaction policies, or optimizing event collection for production workloads.

=== .ai/debug-using-debugbar rules ===

# Debugbar

- Activate the `debug-using-debugbar` skill when investigating a bug, a slow page or endpoint, an N+1 or duplicate query, a failing or slow SQL statement, a 500 or unexpected status code, or when asked to optimize a request. Also covers queued jobs and Artisan commands. Use it even when the user does not mention "debugbar" or "profiling".

=== .ai/filament-development rules ===

# Filament

- Activate the `filament-development` skill when creating, modifying, testing, or debugging Filament panels, resources, relation managers, pages, schemas, forms, tables, actions, infolists, widgets, imports, or exports.

=== .ai/find-skills rules ===

# Find Skills

- Activate the `find-skills` skill when the user asks questions like "how do I do X", "find a skill for X", or "is there a skill that can...", or expresses interest in extending capabilities. It helps discover and install agent skills.

=== .ai/graphify rules ===

# Graphify

This project has a graphify knowledge graph at `graphify-out/`. Before using Read, Grep, Glob, or the shell to explore the codebase, run graphify first:

- `graphify query "<question>"` for a scoped subgraph on any codebase or architecture question
- `graphify path "<A>" "<B>"` for the dependency path between two symbols
- `graphify explain "<concept>"` for every node related to a concept

Apply this to every subagent that explores code. Include the rule in the subagent prompt. Do not skip graphify because files are already known or because a plan is already being executed. The graph surfaces cross-file dependencies and inferred edges that grep and Read cannot find.

Use Read, Grep, or Glob directly only after graphify has oriented you and you need specific lines, or when `graphify-out/graph.json` does not exist yet.

- If `graphify-out/wiki/index.md` exists, navigate it instead of reading raw files.
- Read `graphify-out/GRAPH_REPORT.md` only for a broad architecture review when query, path, and explain are not enough.
- After modifying code files, run `graphify update .` so the graph stays current. That update is AST-only.

=== .ai/hf-cli rules ===

# Hugging Face CLI

- Activate the `hf-cli` skill whenever handling authentication; managing local cache or Hugging Face Buckets; running or scheduling jobs on Hugging Face infrastructure; managing repos, discussions, pull requests, collections, papers, datasets, spaces, or webhooks; deploying and managing HF Inference Endpoints; or any task related to the Hugging Face ecosystem and AI/ML in general.
- Use it even when the user doesn't explicitly ask for a CLI command. It replaces the deprecated `huggingface-cli`.

=== .ai/i-have-adhd rules ===

# ADHD Output

- Activate the `i-have-adhd` skill when the user invokes `/i-have-adhd`. Keep it on for the rest of the session until they say "stop adhd mode" or "normal mode". Lead with the next action, number multi-step work, restate state across turns, suppress tangents, give specific time estimates, and make wins visible.

=== .ai/infer-conventions rules ===

# Infer Conventions

- Activate the `infer-conventions` skill only when the user explicitly asks to detect, infer, document, or standardize this app's conventions or coding style, set up or grow `.ai/rules`, or resolve mixed patterns such as Form Requests versus inline validation. Do not start a convention sweep as part of another task.

=== .ai/medialibrary rules ===

# Media Library

- `spatie/laravel-medialibrary` associates files with Eloquent models, with support for collections, conversions, and responsive images.
- Always activate the `medialibrary-development` skill when working with media uploads, conversions, collections, responsive images, or any code that uses the `HasMedia` interface or `InteractsWithMedia` trait.

=== .ai/opencode rules ===

# OpenCode

- Activate the `opencode` skill for any question about OpenCode itself: how it works, using or configuring it, migrating from V1 to V2, troubleshooting, developing plugins or integrations, using the OpenCode SDK, clients, server, or API, and contributing to the codebase.
- Also use it for OpenCode agents, commands, skills, tools, permissions, MCP servers, providers, models, themes, keybinds, formatters, the CLI, TUI, desktop app, and web app.

=== .ai/ponytail rules ===

# Ponytail

- Activate the `ponytail` skill on any coding task (writing, fixing, refactoring, reviewing, choosing dependencies) and when the user says "ponytail", "be lazy", "simplest solution", or "yagni", or complains about over-engineering or bloat.
- It runs in lazy senior dev mode: the smallest change that fully solves the task, and a reply a busy human understands in one read. End replies with what was skipped or not checked, and any known risk.
- Levels: `lite`, `full` (default), `ultra`. Switch with `/ponytail lite|full|ultra`; disable with "stop ponytail" or "normal mode".

=== .ai/report rules ===

# Report

- Activate the `report` skill when the user wants to report an OpenCode issue or bug. It collects standard diagnostics, adds user-specific reproduction context, and publishes the issue with GitHub CLI.

=== .ai/tailwindcss-development rules ===

# Tailwind CSS

- Always activate the `tailwindcss-development` skill when the user's message includes 'tailwind' in any form, and for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work.
- The core use case is writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue).
- Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.

=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

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

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

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

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

=== spatie/laravel-medialibrary/core rules ===

## Media Library

- `spatie/laravel-medialibrary` associates files with Eloquent models, with support for collections, conversions, and responsive images.
- Always activate the `medialibrary-development` skill when working with media uploads, conversions, collections, responsive images, or any code that uses the `HasMedia` interface or `InteractsWithMedia` trait.

</laravel-boost-guidelines>
