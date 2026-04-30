# WordPress Plugin Development Rules

## Project Context
- This is a WordPress plugin named "Sticky Youtube".
- WordPress Coding Standards (WPCS) should be followed.
- Minimum PHP version: 8.0
- Target WordPress version: 6.4+

## File Access Rules (Ignore/Scan)
- **IGNORE CONTENT:**
  - `/node_modules/**` : Directory structure only. Do not read file content.
  - `/vendor/**` : Directory structure only.
  - `*.log` : Do not read unless explicitly asked.
  - `/.git/**` : Completely ignore.
  - `/.antigravity/**` : Completely ignore.
  - `/.vscode/**` : Completely ignore.

- **PRIORITIZE:**
  - `sticky-youtube.php` (Entry point)
  - `/src/**` (Core logic)
  - `/languages/**` (Localization files)

## Coding Guidelines
- Always use `syt_` prefix for functions to avoid collisions.
- Use WordPress Nonces for all form submissions and Ajax calls.
- For DB operations, use `$wpdb` global object.
- Use `esc_html()`, `esc_attr()`, etc., for all outputs.

## Environment Note
- Local testing environment: Local (by Flywheel).
- Test Site URL: http://karasunouta.local (Use this for browser-agent tasks)