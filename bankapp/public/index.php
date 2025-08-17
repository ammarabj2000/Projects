<?php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_login();
$user = current_user();

$db = get_db();
$stats = [
	'customers' => (int)$db->query('SELECT COUNT(*) AS c FROM customers')->fetch()['c'],
	'accounts' => (int)$db->query('SELECT COUNT(*) AS c FROM accounts')->fetch()['c'],
	'transactions' => (int)$db->query('SELECT COUNT(*) AS c FROM transactions')->fetch()['c'],
];

include __DIR__ . '/partials/header.php';
?>
	<div class="card">
		<h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
		<p>Quick stats</p>
		<div class="row">
			<div class="col card">
				<strong>Customers</strong>
				<div><?php echo $stats['customers']; ?></div>
			</div>
			<div class="col card">
				<strong>Accounts</strong>
				<div><?php echo $stats['accounts']; ?></div>
			</div>
			<div class="col card">
				<strong>Transactions</strong>
				<div><?php echo $stats['transactions']; ?></div>
			</div>
		</div>
		<p>
			<a class="btn" href="/customers/create.php">New Customer</a>
			<a class="btn secondary" href="/accounts/create.php">New Account</a>
		</p>
	</div>
<?php include __DIR__ . '/partials/footer.php'; ?>