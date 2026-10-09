# Verification

Executed October 9, 2026 against XAMPP PHP 8.0.30, MariaDB 10.4.32, and local Apache.

- All 25 application PHP files and the CLI smoke test passed php -l.
- PDO MySQL, mbstring, and curl extensions are available.
- Imported database.sql into the previously absent lutong_bahay database.
- Exactly six tables and eight category seeds verified.
- Login page returned HTTP 200.
- tests/smoke.php executed successfully: **46 checks passed**.

Tests cover real HTTP registration/login/logout, validation, duplicate email, password hashing, session persistence, CSRF rotation/rejection, guest guards, recipe CRUD, ingredient rows/order, combined search/category, two-account direct unauthorized edits/deletes, malformed IDs, comment CRUD/ownership, escaped script markup, favorite JSON/save/unsave/isolation/duplicate prevention, transaction failure rollback for create and edit, cascading deletion, read-time rounding/minimum.

Temporary accounts and content were removed by the test's finally block.

## Remaining manual verification

Browser rendering and JavaScript execution were not automated. Check responsive layouts, keyboard navigation, ingredient add/remove, favorites without reload, removal of the last Favorites card, confirmation prompts, and failed-network messages with the README checklist.

A genuine phpMyAdmin 5.2.1 Quick SQL export was downloaded through its actual export form and saved as lutong_bahay_export.sql. Verified six CREATE TABLE statements, category-only INSERT data, and seven matching foreign keys. Confirmed zero users, recipes, ingredients, comments, and favorites after testing. See README for regeneration instructions. No screenshot or visual result is fabricated. No GitHub push was performed.
