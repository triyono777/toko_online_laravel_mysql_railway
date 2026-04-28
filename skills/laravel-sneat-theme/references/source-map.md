# Sneat source map

Canonical source root:
`/Users/triyono/Projek/tema/sneat-bootstrap-html-laravel-admin-template-free-main`

## Core layout files

- `resources/views/layouts/commonMaster.blade.php`
  Base HTML shell, meta tags, favicon, `data-assets-path`, and shared include points for styles and scripts.

- `resources/views/layouts/contentNavbarLayout.blade.php`
  Main admin layout with sidebar, navbar, footer, content wrapper, and feature toggles like `$isMenu`, `$isNavbar`, and `$isFooter`.

- `resources/views/layouts/blankLayout.blade.php`
  Lightweight layout for auth, error, and maintenance pages.

## Shared sections

- `resources/views/layouts/sections/styles.blade.php`
  Wires Google Fonts, Iconify CSS, core SCSS, vendor SCSS, and `resources/css/app.css` through Vite.

- `resources/views/layouts/sections/scriptsIncludes.blade.php`
  Loads `resources/assets/vendor/js/helpers.js` and `resources/assets/js/config.js` in the head.

- `resources/views/layouts/sections/scripts.blade.php`
  Loads vendor JS, `resources/assets/js/main.js`, and `resources/js/app.js` through Vite.

- `resources/views/layouts/sections/menu/verticalMenu.blade.php`
  Builds the sidebar from shared `menuData`.

- `resources/views/layouts/sections/menu/submenu.blade.php`
  Recursive submenu rendering for nested items.

- `resources/views/layouts/sections/navbar/navbar.blade.php`
- `resources/views/layouts/sections/navbar/navbar-partial.blade.php`
- `resources/views/layouts/sections/footer/footer.blade.php`
  Shared chrome around authenticated pages.

## Menu and shared data

- `resources/menu/verticalMenu.json`
  Menu definition consumed by the sidebar partial.

- `app/Providers/MenuServiceProvider.php`
  Reads `verticalMenu.json`, decodes it, and shares it to all views as `menuData`.

- `bootstrap/providers.php`
  Registers `App\Providers\MenuServiceProvider::class` in the source project.

## Asset pipeline

- `vite.config.js`
  Sweeps page JS, vendor JS, library JS, SCSS, CSS, and fonts from `resources/assets/vendor` into Laravel Vite inputs.

- `resources/assets/css/demo.css`
- `resources/assets/js/*.js`
- `resources/assets/vendor/js/*.js`
- `resources/assets/vendor/libs/**/*`
- `resources/assets/vendor/scss/**/*`
  Theme styling, component JS, and page scripts.

- `public/assets/img/**/*`
  Static images used by dashboard cards, avatars, layouts, icons, and auth illustrations.

## App config and content conventions

- `config/variables.php`
  Theme metadata used across titles, SEO tags, footer links, and branding text.

- `routes/web.php`
  Demo route map showing the pattern `route -> controller -> view`.

- `app/Http/Controllers/**/*`
  Sneat uses one small controller per demo page. Treat this as a reference pattern, not a required architecture for the target app.

- `resources/views/content/**/*`
  Demo pages. Useful as markup references for dashboards, auth screens, forms, tables, and UI elements.

## Caveats

- The source project requires PHP `^8.2` and Laravel `^12.0`.
- The layouts reference `Helper::updatePageConfig($pageConfigs)`, but the corresponding PHP helper definition was not found in the inspected files. Plan to replace or implement that behavior during integration.
- Many menu items and page links point to ThemeSelection demos or premium-only pages. Remove or replace those links in production apps.
