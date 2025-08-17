<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_login();
$db = get_db();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /customers/index.php'); exit; }

$stmt = $db->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) { header('Location: /customers/index.php'); exit; }

$stmt = $db->prepare('SELECT * FROM accounts WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$id]);
$accounts = $stmt->fetchAll();

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<h2>Customer: <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></h2>
		<p>Email: <?php echo htmlspecialchars((string)$customer['email']); ?> | Phone: <?php echo htmlspecialchars((string)$customer['phone']); ?></p>
		<p>Address:<br><?php echo nl2br(htmlspecialchars((string)$customer['address'])); ?></p>
		<p><a class="btn" href="/accounts/create.php?customer_id=<?php echo (int)$customer['id']; ?>">Open New Account</a></p>
		<h3>Accounts</h3>
		<table class="table">
			<thead>
				<tr><th>ID</th><th>Number</th><th>Type</th><th>Status</th><th>Balance</th><th>Actions</th></tr>
			</thead>
			<tbody>
			<?php foreach ($accounts as $a): ?>
				<tr>
					<td><?php echo (int)$a['id']; ?></td>
					<td><?php echo htmlspecialchars($a['account_number']); ?></td>
					<td><?php echo htmlspecialchars($a['type']); ?></td>
					<td><?php echo htmlspecialchars($a['status']); ?></td>
					<td><?php echo number_format((float)$a['balance'], 2); ?></td>
					<td>
						<a href="/accounts/edit.php?id=<?php echo (int)$a['id']; ?>">Edit</a>
						|
						<a href="/transactions/index.php?account_id=<?php echo (int)$a['id']; ?>">Transactions</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>