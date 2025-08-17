<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();
$db = get_db();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /accounts/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
		$error = 'Invalid CSRF token.';
	} else {
		$type = $_POST['type'] ?? 'savings';
		$status = $_POST['status'] ?? 'active';
		$stmt = $db->prepare('UPDATE accounts SET type=?, status=? WHERE id=?');
		$stmt->execute([$type, $status, $id]);
		header('Location: /accounts/index.php');
		exit;
	}
}

$stmt = $db->prepare('SELECT a.*, c.first_name, c.last_name FROM accounts a JOIN customers c ON c.id = a.customer_id WHERE a.id = ?');
$stmt->execute([$id]);
$account = $stmt->fetch();
if (!$account) { header('Location: /accounts/index.php'); exit; }

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<h2>Edit Account</h2>
		<?php if ($error): ?><div class="flash error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
		<p>Account: <?php echo htmlspecialchars($account['account_number']); ?> (<?php echo htmlspecialchars($account['first_name'] . ' ' . $account['last_name']); ?>)</p>
		<form method="post">
			<?php echo csrf_field(); ?>
			<label>Type</label>
			<select name="type" required>
				<?php foreach (['savings','checking'] as $t): ?>
				<option value="<?php echo $t; ?>" <?php echo $account['type']===$t?'selected':''; ?>><?php echo ucfirst($t); ?></option>
				<?php endforeach; ?>
			</select>
			<label>Status</label>
			<select name="status" required>
				<?php foreach (['active','frozen','closed'] as $s): ?>
				<option value="<?php echo $s; ?>" <?php echo $account['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
				<?php endforeach; ?>
			</select>
			<div style="margin-top:12px">
				<button class="btn" type="submit">Save</button>
				<a class="btn secondary" href="/accounts/index.php">Cancel</a>
			</div>
		</form>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>