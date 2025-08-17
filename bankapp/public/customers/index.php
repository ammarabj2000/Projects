<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_login();

$db = get_db();
$q = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT * FROM customers';
if ($q !== '') {
	$sql .= ' WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?';
	$like = '%' . $q . '%';
	$params = [$like, $like, $like];
}
$sql .= ' ORDER BY created_at DESC LIMIT 100';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<div class="row" style="align-items:center">
			<h2 style="margin:0">Customers</h2>
			<div style="margin-left:auto">
				<a class="btn" href="/customers/create.php">New Customer</a>
			</div>
		</div>
		<form method="get" class="row" style="gap:8px;margin:12px 0">
			<input type="text" name="q" placeholder="Search by name/email" value="<?php echo htmlspecialchars($q); ?>" />
			<button class="btn" type="submit">Search</button>
		</form>
		<table class="table">
			<thead>
				<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
			</thead>
			<tbody>
			<?php foreach ($customers as $c): ?>
				<tr>
					<td><?php echo (int)$c['id']; ?></td>
					<td><?php echo htmlspecialchars($c['first_name'] . ' ' . $c['last_name']); ?></td>
					<td><?php echo htmlspecialchars((string)$c['email']); ?></td>
					<td><?php echo htmlspecialchars((string)$c['phone']); ?></td>
					<td>
						<a href="/customers/edit.php?id=<?php echo (int)$c['id']; ?>">Edit</a>
						|
						<a href="/customers/show.php?id=<?php echo (int)$c['id']; ?>">View</a>
						|
						<a style="color:#d00" href="/customers/delete.php?id=<?php echo (int)$c['id']; ?>" onclick="return confirm('Delete this customer?')">Delete</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>