# CodingGo - Agent Rules & Workflows

This document contains project-specific rules, UI/UX guidelines, and core system architectures for the CodingGo platform. Always adhere to these principles when maintaining or adding new features to ensure consistency.

## 1. UI/UX & Styling Guidelines
CodingGo uses a custom, modern Vanilla CSS design system (no Tailwind unless requested).
- **CSS Variables**: Always use the defined CSS variables for colors to support Dark Mode automatically.
  - Backgrounds: `var(--dash-bg)`, `var(--dash-sidebar)`, `var(--dash-bg-hover)`
  - Text: `var(--dash-text)`, `var(--dash-text-muted)`
  - Brand/Accents: `var(--dash-primary)`, `var(--dash-primary-dark)`, `var(--dash-warning)`
  - Borders: `var(--dash-border)`
- **Shapes & Shadows**: 
  - Main cards/widgets: `border-radius: 16px;` with subtle shadow `box-shadow: 0 4px 6px rgba(0,0,0,0.02);`
  - Buttons/Inputs: `border-radius: 8px;`
- **Icons**: Use inline SVGs (like Heroicons or Lucide). Do not rely on external font icon libraries.
- **Micro-interactions**: Add subtle hover effects (`transition: background 0.2s;`, `transform: translateY(-4px);`) to interactive elements.

## 2. RBAC (Role-Based Access Control) System
The platform strictly enforces access based on User Roles and Age/Categories.

### Categories Structure
The learning levels are sequentially: `SD` < `SMP` < `SMA` < `Umum`.

### Access Logic (`includes/auth_helpers.php`)
- **Admin**: `$_SESSION['user_role'] === 'admin'`. Admins have unrestricted access to all dashboard pages and all course categories.
- **User Allowed Categories**: Retrieved via `getUserAllowedCategories($user_db)`.
  1. **Manual Override**: If an Admin sets `allowed_categories` in the `users` table, this strict array is returned (e.g., `['SD']`).
  2. **Automatic (Age-based)**: If no manual override exists, access is calculated using `calculateAge(birth_date)`. (e.g., Age 16 gets `['SD', 'SMP', 'SMA']`).
  
### Data Fetching Rule
Whenever fetching a list of courses or recommendations for a user:
1. Always fetch their `$allowed = getUserAllowedCategories($user_db);`
2. Prepare a SQL `IN` clause: `WHERE c.category IN (?, ?, ...)`
3. Never show restricted courses to a user.
4. **Important Bug Prevention**: Do not blindly trust `$category === 'Semua'` to return all courses without wrapping it in the `$allowed` check (as fixed in `course_list.php`).

## 3. Database & SQL Rules
- Always use PDO Prepared Statements (`$pdo->prepare()`) to prevent SQL Injection.
- Do not use raw `$pdo->query()` with variable interpolation.
- Default file inclusion: `require_once 'config/db.php';`

## 4. File Structure Convention
- `pages/` : Contains all main view components routed via `index.php?page=...`
- `includes/` : Reusable layout components (`sidebar.php`, `topbar.php`, `footer_dash.php`) and logic helpers (`auth_helpers.php`, `materi_icons.php`).
- `index.php` : The main router. It checks auth and includes the correct page and layout.

## 5. Adding New Pages (Workflow)
1. Create the new file in `pages/new_page.php`.
2. Add the page slug (`'new_page'`) to the `$dashboard_pages` array in `index.php`.
3. If it requires a sidebar link, update `includes/sidebar.php` ensuring the `active` state logic matches `$_GET['page']`.
4. Ensure the UI matches the Dashboard layout wrapping standards (e.g., `<div class="dash-left">` and `<div class="dash-right">`).
