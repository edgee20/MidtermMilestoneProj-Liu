# Lutong Bahay

A simple Filipino community recipe website for a PHP midterm project. Traditional PHP pages handle requests; six small classes contain reusable database operations.

## Features

- Registration, login, logout, and session-based access.
- Recipe CRUD with ordered ingredient rows and dynamic ingredient inputs.
- Search by title/description and filter by category.
- Author-only recipe and comment editing/deletion.
- Save/unsave through Fetch and personal Favorites.
- Estimated read time at 200 words per minute.
- Prepared statements, transactions, validation, escaping, and CSRF.
- Responsive layout with local system fonts, CSS Grid, and Flexbox.

## XAMPP setup

1. Start Apache and MySQL in XAMPP (PHP 8.0+, PDO MySQL, and mbstring).
2. Keep this folder in C:\xampp\htdocs\MidtermMilestoneProj-Liu.
3. For a new installation, open http://localhost/phpmyadmin/ and import database.sql. It creates lutong_bahay and eight categories. Import once into an empty database; existing tables are not overwritten.
4. Check config/database.php: localhost, lutong_bahay, root, empty password.
5. Open **http://localhost/MidtermMilestoneProj-Liu/**, register, and log in.

The database was imported in the original development environment. No demo accounts are included. Renaming the folder changes the URL. PHP must run through Apache, not Explorer.

PHP/database timestamps use Philippine time. Cookies use HttpOnly and SameSite=Lax, plus Secure on HTTPS. Passwords require at least eight characters and at most 72 bytes (bcrypt's limit). Outside local development, use HTTPS and private credentials.

## Structure

| Location                | Purpose                                               |
| ----------------------- | ----------------------------------------------------- |
| Root PHP pages          | Authentication, browsing, recipe forms/actions        |
| config/                 | Session setup and centralized connection              |
| classes/                | Database, User, Recipe, Category, Comment, Favorite   |
| includes/               | Shared auth, helpers, header/footer, recipe form/list |
| api/toggle_favorite.php | Authenticated POST endpoint returning JSON            |
| assets/                 | Plain CSS and vanilla JavaScript                      |
| database.sql            | Setup schema and categories                           |
| lutong_bahay_export.sql | Genuine phpMyAdmin Quick SQL export                   |
| DEFENSE_GUIDE.md        | Lesson mapping and request walkthrough                |

## Database and PHP concepts

Exactly six tables: users, categories, recipes, ingredients, comments, favorites. Users/categories have many recipes; recipes have many ingredients/comments. Favorites joins users and recipes in a many-to-many relationship. Its composite primary key prevents duplicates.

Ingredients are separate ordered rows. Foreign keys preserve relationships; recipe deletion cascades to dependent rows. Referenced categories cannot be deleted. Recipe creation/editing uses transactions to keep recipe and ingredient changes together.

Pages handle HTTP and validate input. Classes encapsulate PDO operations. See DEFENSE_GUIDE.md for the concepts taught in Weeks 5-7.

## Manual checks before defense

1. Register two accounts. Try duplicate email, wrong password, and guest access.
2. Create/edit/delete a recipe, add/remove ingredients, and combine search/filter.
3. Add/edit/delete comments; confirm B cannot change A's content.
4. Save/unsave without reloading; check personal Favorites and error feedback.
5. Check phone widths, keyboard focus, and deletion confirmations.

## Export and submission

A genuine phpMyAdmin Quick SQL export is included. If the schema changes:

1. Open phpMyAdmin and select lutong_bahay.
2. Choose **Export → Quick → SQL → Export**.
3. Save as lutong_bahay_export.sql in the project root.

Use a clean database with categories and no private member data. Include both SQL files, source, README, and defense guide. Exclude credentials and temporary files. Add screenshots only if actually taken, review changes, then commit/push.
