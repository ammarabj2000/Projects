<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_login();
$db = get_db();

$status = $_GET['status'] ?? '';
$type = $_GET['type'] ?? '';
$params = [];
$sql = 'SELECT a.*, c.first_name, c.last_name FROM accounts a JOIN customers c ON c.id = a.customer_id';
$conds = [];
if ($status !== '') { $conds[] = 'a.status = ?'; $params[] = $status; }
if ($type !== '') { $conds[] = 'a.type = ?'; $params[] = $type; }
if ($conds) { $sql .= ' WHERE ' . implode(' AND ', $conds); }
$sql .= ' ORDER BY a.created_at DESC LIMIT 100';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$accounts = $stmt->fetchAll();

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<div class="row" style="align-items:center">
			<h2 style="margin:0">Accounts</h2>
			<div style="margin-left:auto">
				<a class="btn" href="/accounts/create.php">Open Account</a>
			</div>
		</div>
		<form class="row" method="get" style="gap:8px;margin:12px 0">
			<select name="status">
				<option value="">All Statuses</option>
				<?php foreach (['active','frozen','closed'] as $s): ?>
				<option value="<?php echo $s; ?>" <?php echo $status===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
				<?php endforeach; ?>
			</select>
			<select name="type">
				<option value="">All Types</option>
				<?php foreach (['savings','checking'] as $t): ?>
				<option value="<?php echo $t; ?>" <?php echo $type===$t?'selected':''; ?>><?php echo ucfirst($t); ?></option>
				<?php endforeach; ?>
			</select>
			<button class="btn" type="submit">Filter</button>
		</form>
		<table class="table">
			<thead>
				<tr><th>ID</th><th>Number</th><th>Customer</th><th>Type</th><th>Status</th><th>Balance</th><th>Actions</th></tr>
			</thead>
			<tbody>
			<?php foreach ($accounts as $a): ?>
				<tr>
					<td><?php echo (int)$a['id']; ?></td>
					<td><?php echo htmlspecialchars($a['account_number']); ?></td>
					<td><?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?></td>
					<td><?php echo htmlspecialchars($a['type']); ?></td>
					<td><?php echo htmlspecialchars($a['status']); ?></td>
					<td><?php echo number_format((float)$a['balance'], 2); ?></td>
					<td>
						<a href="/accounts/edit.php?id=<?php echo (int)$a['id']; ?>">Edit</a>
						|
						<a href="/transactions/index.php?account_id=<?php echo (int)$a['id']; ?>">Transactions</a>
						|
						<a href="/accounts/delete.php?id=<?php echo (int)$a['id']; ?>" style="color:#d00" onclick="return confirm('Delete this account?')">Delete</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>