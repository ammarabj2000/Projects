<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();
$db = get_db();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /customers/index.php'); exit; }

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
			$stmt = $db->prepare('UPDATE customers SET first_name=?, last_name=?, email=?, phone=?, address=? WHERE id=?');
			try {
				$stmt->execute([$first, $last, $email !== '' ? $email : null, $phone !== '' ? $phone : null, $address !== '' ? $address : null, $id]);
				header('Location: /customers/index.php');
				exit;
			} catch (Throwable $e) {
				$error = 'Could not update customer. Possibly duplicate email.';
			}
		}
	}
}

$stmt = $db->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) { header('Location: /customers/index.php'); exit; }

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<h2>Edit Customer</h2>
		<?php if ($error): ?><div class="flash error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
		<form method="post">
			<?php echo csrf_field(); ?>
			<label>First Name</label>
			<input name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
			<label>Last Name</label>
			<input name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
			<label>Email</label>
			<input type="email" name="email" value="<?php echo htmlspecialchars((string)$customer['email']); ?>">
			<label>Phone</label>
			<input name="phone" value="<?php echo htmlspecialchars((string)$customer['phone']); ?>">
			<label>Address</label>
			<textarea name="address"><?php echo htmlspecialchars((string)$customer['address']); ?></textarea>
			<div style="margin-top:12px">
				<button class="btn" type="submit">Save</button>
				<a class="btn secondary" href="/customers/index.php">Cancel</a>
			</div>
		</form>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>