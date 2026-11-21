<?php
require_once '../files/dbConnect.php';
require_once '../includes/functions.php';
header('Content-Type: application/json');

function out($code, $data)
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if (!isset($_SESSION['admin_username']))
    out(401, ['error' => 'Your session expired. Please sign in again.']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    out(405, ['error' => 'POST only.']);
if (!storage_enabled())
    out(500, ['error' => 'File storage is not configured on the server.']);

$in = json_decode(file_get_contents('php://input'), true);
$type = $in['media_type'] ?? 'pdf';
if (!isset(media_types()[$type]))
    $type = 'pdf';
$name = (string) ($in['file_name'] ?? '');
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
if (!in_array($ext, media_exts($type), true))
    out(422, ['error' => 'That file type is not allowed for ' . type_noun($type) . '.']);

$safe = substr(preg_replace('/[^A-Za-z0-9._-]+/', '_', pathinfo($name, PATHINFO_FILENAME)), 0, 50);
if ($safe === '')
    $safe = 'file';
$fileKey = 'books/' . uniqid() . '_' . $safe . '.' . $ext;
$coverKey = 'covers/' . uniqid() . '.jpg';

out(200, [
    'file' => ['key' => $fileKey, 'url' => storage_presign('PUT', $fileKey, 3600)],
    'cover' => ['key' => $coverKey, 'url' => storage_presign('PUT', $coverKey, 3600)],
]);
