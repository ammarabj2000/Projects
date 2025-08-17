<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();
$db = get_db();

$customerId = (int)($_GET['customer_id'] ?? 0);
$error = '';

function generate_account_number(): string {
	return (string)random_int(1000000000, 9999999999);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
		$error = 'Invalid CSRF token.';
	} else {
		$customerId = (int)($_POST['customer_id'] ?? 0);
		$type = $_POST['type'] ?? 'savings';
		$accountNumber = generate_account_number();
		if ($customerId <= 0) {
			$error = 'Customer is required.';
		} else {
			$stmt = $db->prepare('INSERT INTO accounts (customer_id, account_number, type, status, balance) VALUES (?, ?, ?, "active", 0.00)');
			try {
				$stmt->execute([$customerId, $accountNumber, $type]);
				header('Location: /accounts/index.php');
				exit;
			} catch (Throwable $e) {
				$error = 'Could not open account.';
			}
		}
	}
}

$customers = $db->query('SELECT id, first_name, last_name FROM customers ORDER BY first_name')->fetchAll();

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<h2>Open Account</h2>
		<?php if ($error): ?><div class="flash error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
		<form method="post">
			<?php echo csrf_field(); ?>
			<label>Customer</label>
			<select name="customer_id" required>
				<option value="">Select a customer</option>
				<?php foreach ($customers as $c): ?>
				<option value="<?php echo (int)$c['id']; ?>" <?php echo $customerId===(int)$c['id']?'selected':''; ?>><?php echo htmlspecialchars($c['first_name'] . ' ' . $c['last_name']); ?></option>
				<?php endforeach; ?>
			</select>
			<label>Account Type</label>
			<select name="type" required>
				<option value="savings">Savings</option>
				<option value="checking">Checking</option>
			</select>
			<div style="margin-top:12px">
				<button class="btn" type="submit">Open</button>
				<a class="btn secondary" href="/accounts/index.php">Cancel</a>
			</div>
		</form>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>