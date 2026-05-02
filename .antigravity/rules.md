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
- Always use `syt_` prefix for global functions to avoid collisions, or preferably use PHP Namespaces (e.g., `namespace karasunouta\StickyYoutube;`).
- Use WordPress Nonces for all form submissions and Ajax calls.
- For DB operations, use `$wpdb` global object.
- Use `esc_html()`, `esc_attr()`, `esc_url()`, etc., for all outputs.

## Environment & Shell Command Note
- Local testing environment: Local (by Flywheel).
- Test Site URL: http://karasunouta.local (Use this for browser-agent tasks)
- **CRITICAL: Terminal & WP-CLI Execution Path**
  - The WordPress root directory is located on the D: drive at:
    `D:\Users\karasunouta\Local Sites\karasunouta\app\public`
  - When executing ANY `wp-cli` commands, you MUST use the `--path` argument to specify the WordPress root explicitly.
    Example: `wp plugin list --path="D:\Users\karasunouta\Local Sites\karasunouta\app\public"`
  - If you need to run other local shell commands requiring the project root, you MUST change to the D: drive first using `cd /d "D:\Users\karasunouta\Local Sites\karasunouta\app\public"` (if using cmd) or explicitly navigate to the correct path in PowerShell.