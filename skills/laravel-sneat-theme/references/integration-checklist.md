# Sneat integration checklist

## 1. Audit the target app

- Confirm Laravel version and whether providers belong in `bootstrap/providers.php` or `config/app.php`.
- Identify the current asset pipeline: Vite, Mix, or custom.
- Identify whether the app already uses Bootstrap, Tailwind, Livewire, Inertia, Filament, or Jetstream.
- Map existing auth routes, dashboard routes, layout files, and shared partials.

## 2. Bring over theme foundation

- Add or adapt `config/variables.php` for project-specific metadata.
- Add `resources/menu/verticalMenu.json` only if the app should use Sneat's sidebar model.
- Add `app/Providers/MenuServiceProvider.php` and register it.
- Resolve or remove the `Helper::updatePageConfig($pageConfigs)` dependency before relying on Sneat layouts.

## 3. Bring over layouts and partials

- Start with `resources/views/layouts/commonMaster.blade.php`.
- Add `contentNavbarLayout.blade.php` for authenticated pages.
- Add `blankLayout.blade.php` for login, register, error, and maintenance pages.
- Copy only the partials actually used by the target page set: menu, navbar, footer, styles, and scripts.

## 4. Bring over assets

- Copy required directories from `resources/assets/vendor`.
- Copy `resources/assets/css/demo.css`.
- Copy only the needed page scripts from `resources/assets/js`.
- Copy referenced static images from `public/assets/img`.
- Update the target `vite.config.js` so every imported SCSS, CSS, JS, and font file is included in the input graph.

## 5. Port pages incrementally

- For admin screens, build pages on top of `layouts/contentNavbarLayout`.
- For auth screens, build on `layouts/blankLayout`.
- Keep the target app's forms, validation errors, route names, CSRF handling, and request methods.
- Replace hard-coded demo strings, fake metrics, and external demo links.

## 6. Sanitize demo dependencies

- Remove GitHub star buttons and external ThemeSelection links unless explicitly requested.
- Replace menu entries that point to premium demo URLs.
- Replace `config('variables.*')` branding with project values.

## 7. Verify

- Run `php artisan optimize:clear`.
- Run `php artisan route:list` and check route names used in menus and links.
- Run `npm run build` or `npm run dev` and fix missing Vite inputs immediately.
- Run `php artisan test` if the app has tests.
- Open the affected pages and look for missing images, broken dropdowns, missing styles, or JS console failures.
