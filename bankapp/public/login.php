<?php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/csrf.php';

start_secure_session();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$token = $_POST['csrf_token'] ?? null;
	if (!verify_csrf_token($token)) {
		$error = 'Invalid CSRF token.';
	} elseif (authenticate($email, $password)) {
		header('Location: /index.php');
		exit;
	} else {
		$error = 'Invalid credentials.';
	}
}

include __DIR__ . '/partials/header.php';
?>
	<div class="card">
		<h2>Login</h2>
		<?php if ($error): ?>
			<div class="flash error"><?php echo htmlspecialchars($error); ?></div>
		<?php endif; ?>
		<form method="post">
			<?php echo csrf_field(); ?>
			<label>Email</label>
			<input type="email" name="email" required>
			<label>Password</label>
			<input type="password" name="password" required>
			<div style="margin-top:12px">
				<button class="btn" type="submit">Login</button>
			</div>
		</form>
	</div>
<?php include __DIR__ . '/partials/footer.php'; ?>