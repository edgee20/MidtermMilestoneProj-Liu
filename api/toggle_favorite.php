<?php
declare(strict_types=1);
define('JSON_ENDPOINT', true);
require __DIR__ . '/../config/app.php';
header('Content-Type: application/json; charset=UTF-8');
function respond(int $status, array $data): void { http_response_code($status); echo json_encode($data); exit; }
if (!userId()) { respond(401, ['success' => false, 'message' => 'Please log in again.']); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); respond(405, ['success' => false, 'message' => 'Use POST.']); }
if (!validCsrf()) { respond(403, ['success' => false, 'message' => 'Your session expired. Reload and try again.']); }
$id = positiveId($_POST['recipe_id'] ?? null);
if (!$id) { respond(400, ['success' => false, 'message' => 'Invalid recipe ID.']); }
if (!$recipes->find($id)) { respond(404, ['success' => false, 'message' => 'Recipe not found.']); }
// Desired state makes retries safe instead of reversing the last result.
$desired = textInput($_POST, 'is_favorited');
if (!in_array($desired, ['0', '1'], true)) { respond(400, ['success' => false, 'message' => 'Invalid favorite state.']); }
if ($desired === '1') { $favorites->add(userId(), $id); } else { $favorites->remove(userId(), $id); }
respond(200, ['success' => true, 'is_favorited' => $desired === '1',
 'message' => $desired === '1' ? 'Recipe saved to favorites.' : 'Recipe removed from favorites.']);
