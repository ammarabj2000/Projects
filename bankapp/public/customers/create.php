<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();

$db = get_db();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
		$error = 'Invalid CSRF token.';
	} else {
		$first = trim($_POST['first_name'] ?? '');
		$last = trim($_POST['last_name'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$phone = trim($_POST['phone'] ?? '');
		$address = trim($_POST['address'] ?? '');
		if ($first === '' || $last === '') {
			$error = 'First and last name are required.';
		} else {
			$stmt = $db->prepare('INSERT INTO customers (first_name, last_name, email, phone, address) VALUES (?, ?, ?, ?, ?)');
			try {
				$stmt->execute([$first, $last, $email !== '' ? $email : null, $phone !== '' ? $phone : null, $address !== '' ? $address : null]);
				header('Location: /customers/index.php');
				exit;
			} catch (Throwable $e) {
				$error = 'Could not create customer. Possibly duplicate email.';
			}
		}
	}
}

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<h2>New Customer</h2>
		<?php if ($error): ?><div class="flash error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
		<form method="post">
			<?php echo csrf_field(); ?>
			<label>First Name</label>
			<input name="first_name" required>
			<label>Last Name</label>
			<input name="last_name" required>
			<label>Email</label>
			<input type="email" name="email">
			<label>Phone</label>
			<input name="phone">
			<label>Address</label>
			<textarea name="address"></textarea>
			<div style="margin-top:12px">
				<button class="btn" type="submit">Create</button>
				<a class="btn secondary" href="/customers/index.php">Cancel</a>
			</div>
		</form>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>