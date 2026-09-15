# Forge track (working copy)

This folder is the active theme to edit. `FORK/`, `Child/`, and `source/astra` stay as local backups — do not delete them.

Standalone parent theme branded **Forge**. Copy this folder to `wp-content/themes/forge` and activate it **alone**. Do not install official Astra next to it. Do not install the Astra Pro addon on this stack.

Based on Astra 4.13.8 (GPL). Theme Name in `style.css` is **Forge**. Author: Ben Roos (TechJunkyBen). Site: https://hire.techjunkyben.nl. Version `4.13.8-forge.1`. Settings option stays `astra-settings`. Text domain stays `astra`. PHP internals still use `astra_*` names on purpose.

## What this version does

- Header/footer builder slots raised to **10** (Astra’s own cap) by patching `inc/core/builder/class-astra-builder-helper.php`.
- Builder item `'clone' => true` in header/footer builder configs.
- Real header/footer **Divider** items (not Pro teasers). Simple layout/thickness/color controls.
- Customizer `pro_active` / `pricingBar` and dashboard Pro cards hidden.
- Does **not** load NPS, BSF analytics, Abilities/MCP, or the Customizer `astra-pro` upgrade section.
- Does **not** load Site Builder free preview (`class-astra-theme-builder-free.php`).
- Forces AI Assistant, Learn tab, abilities, and MCP off.
- Hides `ast-upgrade` / Toolkit “Learn More” Customizer controls via `inc/custom-astra-unlock.php`.
- `astra_showcase_upgrade_notices()` always returns false.

Does **not** fake `ASTRA_EXT_VER`. Does **not** copy Astra Pro. Sticky header, mega menu, custom layouts, page headers, Blog Pro stay unavailable.

## Files we patched vs `source/astra`

- `style.css` — name, version, modified-from note
- `screenshot.png` — Forge theme shot
- `inc/custom-astra-brand.php` — **new** (Forge name, author site, admin slug/icon)
- `inc/assets/images/forge-logo.svg` — **new**
- `functions.php` — skip NPS, analytics, abilities; load brand/unlock/divider helpers
- `inc/core/builder/class-astra-builder-helper.php` — counts always use the 10-cap path
- `inc/customizer/configurations/builder/header/configs/header-builder.php` — clone on
- `inc/customizer/configurations/builder/footer/configs/footer-builder.php` — clone on
- `inc/customizer/class-astra-customizer.php` — skip Pro upgrade section
- `inc/extras.php` — upgrade notices off
- `admin/class-astra-admin-loader.php` — skip theme-builder-free
- `admin/includes/class-astra-api-init.php` — AI/Learn defaults off
- `inc/custom-astra-unlock.php` — **new** (ours)

Prefer not-loading over deleting unused files so diffs against `source/astra` stay readable.

## Upstream merge

When a newer Astra is dropped into `source/astra`, diff against this folder and re-apply only the patches above. Do not overwrite FORK blindly.

## Parity checklist

- [ ] 10 header widgets, 10 footer builder widgets
- [ ] 10 menus / HTML / buttons / social / dividers in the builder palette
- [ ] No AI assistant
- [ ] No MCP
- [ ] No NPS / analytics
- [ ] No Upgrade / Toolkit Customizer section
- [ ] Menus 3–10 appear in the builder palette

## Install notes

Theme switch uses a new stylesheet slug. `astra-settings` (layout/colors) can carry over. Menu locations and Additional CSS are per-theme and may need re-assigning. Run on staging first. Keep GPL copyrights.
