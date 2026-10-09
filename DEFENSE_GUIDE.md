# Defense guide

## Explain the project in one minute

Lutong Bahay is a recipe-sharing website using PHP, MySQL, HTML, CSS, and vanilla JavaScript. PHP pages receive requests, validate input, use small classes to execute prepared SQL, and return HTML or redirects. Sessions remember the logged-in member. Six tables separate users, categories, recipes, ingredients, comments, and favorites. Authors alone can change their content. JavaScript adds ingredient fields and saves favorites through Fetch without reloading.

## Lesson mapping

| Concept | Code to show |
| --- | --- |
| Week 5 HTTP lifecycle | create-recipe.php receives POST, validates, saves, redirects; GET renders |
| Superglobals | GET search in index.php; POST actions; SESSION user_id in auth.php |
| Sessions/cookies | app.php starts before HTML; login regenerates ID; logout clears storage/cookie |
| Includes/strict types/arrays/loops | Shared includes; strict declarations; ingredients[] and foreach |
| Frontend/backend validation | HTML required/minlength; authoritative recipeErrors in functions.php |
| XSS defense | e() and nl2br(e(...)); HttpOnly/SameSite |
| Week 6 classes/objects/constructor/$this | new Recipe($db); constructor stores PDO collaborator |
| Encapsulation | Private PDO; public methods; private saveIngredients |
| Composition | Recipe has a PDO connection; it is not a Database |
| Single responsibility | Classes handle database logic; pages handle HTTP/display |
| Week 7 relationships | One-to-many ingredients and many-to-many favorites |
| Normalization | Ingredient rows and category/author keys instead of copied names/lists |
| Keys/constraints | database.sql; unique email; favorites composite primary key |
| SQL CRUD/JOIN | Recipe INSERT/SELECT/UPDATE/DELETE; INNER author/category, LEFT favorite |
| Prepared statements | Placeholder SQL and separate execute values |
| Transactions | Recipe create/update begin, commit, rollback on failure |
| Fetch/JSON | script.js → toggle_favorite.php → updated button |

Inheritance, abstract classes, and interfaces were covered in Week 6 but are not forced into this project. Recipes, users, and comments lack a useful is-a relationship. Composition is the appropriate covered principle. A one-to-one table is also unnecessary. Explain these choices; do not claim every slide concept is implemented.

## Trace creating a recipe

1. Browser GET requests create-recipe.php.
2. Shared setup starts the session and checks login before output.
3. PHP renders the form. Ingredient names end in [] so PHP receives an array.
4. POST submits fields. PHP checks CSRF and validates again.
5. Recipe begins a transaction, inserts the recipe, gets its ID, and inserts ingredients with their order.
6. Commit saves all changes; an error rolls them all back.
7. A 303 redirect loads detail through GET. Refresh does not repeat the POST.

## Trace a favorite

Fetch sends recipe ID, desired state, and CSRF token. The session supplies user ID. PHP checks login, token, recipe, and desired state. Favorite adds/removes a row and returns JSON. JavaScript updates the button and removes unsaved cards from Favorites. The composite key prevents duplicates. Desired state makes request retries safe.

## Questions to practice

**Why PDO?** It connects PHP to MySQL and supports prepared statements/transactions.

**What prevents SQL injection?** Input is bound separately from SQL. Search adds fixed SQL fragments but binds search values.

**Why escape validated text?** Validation checks business rules; escaping keeps HTML characters from executing.

**Why CSRF?** An unpredictable session token protects mutations from requests without the legitimate form's token. POST alone is insufficient.

**Why server ownership?** Hidden buttons can be bypassed. PHP checks the session identity against the author; mutations also restrict SQL by author ID.

**Why normalize ingredients?** Each row has one ingredient, its recipe, and order. Comma-separated text is harder to maintain as relational data.

**INNER vs LEFT JOIN?** Recipes require an author/category. Favorites are optional for the viewer; LEFT JOIN keeps unsaved recipes.

**Why transactions?** A recipe saved without all its ingredients is inconsistent. Commit/rollback keeps all steps together.

**What is encapsulation?** Public methods expose useful operations; private storage/insertion details stay inside the class.

**Why no framework?** Traditional pages keep the request lifecycle visible and code small enough to explain.

**Read time?** Combine title/description/ingredients/instructions, count Unicode words, divide by 200, round up, minimum one minute. Exclude comments; do not store redundant derived data.

## Demo order

Explain SQL relationships. Show Database and a Recipe method. Trace a transaction. Demonstrate login, recipe CRUD, comments, and favorites. Use account B to show author restrictions. Explain one complete request line by line instead of memorizing all files.
