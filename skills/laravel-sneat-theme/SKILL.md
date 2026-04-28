---
name: laravel-sneat-theme
description: Use when adapting a Laravel Blade application to the local Sneat Bootstrap HTML Laravel Admin Template stored at /Users/triyono/Projek/tema/sneat-bootstrap-html-laravel-admin-template-free-main. Applies Sneat's layout stack, Vite assets, menu JSON, shared provider wiring, and page structure while preserving the target app's business logic, routes, and data flow.
---

# Laravel Sneat Theme

## Overview

This skill adapts Laravel admin or auth screens to the local Sneat theme source at `/Users/triyono/Projek/tema/sneat-bootstrap-html-laravel-admin-template-free-main`.

Use it when the task involves:

- moving an existing Laravel app to Sneat-styled Blade layouts
- building new dashboard, auth, settings, table, or form pages in Sneat style
- wiring Sneat sidebar/navbar/footer partials into a Laravel app
- migrating Sneat assets, Vite inputs, menu JSON, or provider setup

## Source of truth

Treat the local Sneat folder as the canonical reference:

- source root: `/Users/triyono/Projek/tema/sneat-bootstrap-html-laravel-admin-template-free-main`
- start with [references/source-map.md](references/source-map.md)
- use [references/integration-checklist.md](references/integration-checklist.md) before copying files

Read only the specific source files needed for the current task instead of loading the whole template into context.

## Workflow

1. Audit the target Laravel app first.
   Check Laravel version, existing auth flow, Blade layout structure, asset pipeline, and whether the app already uses Bootstrap, Tailwind, Livewire, Inertia, or Filament.

2. Pull only the Sneat primitives needed for the task.
   For most admin pages this means `commonMaster`, `contentNavbarLayout`, shared `sections/*`, menu JSON, public images, and the related Vite inputs.

3. Preserve application logic.
   Keep the target app's controllers, form actions, validation, policies, route names, and model bindings. Import Sneat for layout and presentation, not for domain behavior.

4. Rewire assets deliberately.
   Sneat relies on `resources/assets/vendor`, `resources/assets/css`, page-specific JS files, Vite imports in Blade, and `public/assets/img` for images. Mirror only what each page actually uses.

5. Validate before finishing.
   Check for missing Vite entries, broken `asset()` paths, unregistered providers, missing shared view data, and demo content still pointing to ThemeSelection routes.

## Integration rules

- Use `@extends('layouts/contentNavbarLayout')` for authenticated admin pages.
- Use `@extends('layouts/blankLayout')` for auth, error, or maintenance screens.
- Keep page-specific dependencies inside `vendor-style`, `vendor-script`, `page-style`, and `page-script` sections.
- Register shared menu data through a provider when using `resources/menu/verticalMenu.json`.
- Replace demo menu links, dashboard numbers, copy, and branding unless the user explicitly wants the stock demo content.
- Respect the upstream theme license and attribution requirements from the source project.

## Important caveats

- The inspected Sneat source uses Laravel `^12.0` and registers `MenuServiceProvider` in `bootstrap/providers.php`.
- `Helper::updatePageConfig($pageConfigs)` is referenced in Sneat layouts, but the corresponding PHP helper implementation was not found in the inspected source tree. If the target app does not already provide that helper, either implement an equivalent helper or remove that call and use local layout defaults.
- Do not mix Sneat's Blade/Bootstrap stack blindly into apps built around another UI system. If the target app uses Tailwind-heavy auth scaffolding or component frameworks, adapt incrementally.

## References

- [references/source-map.md](references/source-map.md): key source files and how they fit together
- [references/integration-checklist.md](references/integration-checklist.md): safe migration order and verification steps
