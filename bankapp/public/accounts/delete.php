<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();
$db = get_db();
$id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
if ($id <= 0) { header('Location: /accounts/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
		$error = 'Invalid CSRF token.';
	} else {
		$stmt = $db->prepare('DELETE FROM accounts WHERE id = ?');
		$stmt->execute([$id]);
		header('Location: /accounts/index.php');
		exit;
	}
}

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<h2>Delete Account</h2>
		<?php if ($error): ?><div class="flash error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
		<p>Are you sure you want to delete this account? This will also delete related transactions.</p>
		<form method="post">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="id" value="<?php echo (int)$id; ?>">
			<button class="btn danger" type="submit">Delete</button>
			<a class="btn secondary" href="/accounts/index.php">Cancel</a>
		</form>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>