<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();
$db = get_db();

$accountId = (int)($_GET['account_id'] ?? 0);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
		$error = 'Invalid CSRF token.';
	} else {
		$accountId = (int)($_POST['account_id'] ?? 0);
		$amount = (float)($_POST['amount'] ?? 0);
		$type = $_POST['type'] ?? '';
		$description = trim($_POST['description'] ?? '');
		if ($accountId <= 0 || $amount <= 0 || !in_array($type, ['deposit','withdraw'], true)) {
			$error = 'Invalid input.';
		} else {
			try {
				$db->beginTransaction();

				// Lock the account row to avoid race conditions
				$stmt = $db->prepare('SELECT id, balance, status FROM accounts WHERE id = ? FOR UPDATE');
				$stmt->execute([$accountId]);
				$account = $stmt->fetch();
				if (!$account) { throw new Exception('Account not found'); }
				if ($account['status'] !== 'active') { throw new Exception('Account is not active'); }

				$newBalance = (float)$account['balance'];
				if ($type === 'deposit') {
					$newBalance += $amount;
				} else {
					if ($amount > $newBalance) { throw new Exception('Insufficient funds'); }
					$newBalance -= $amount;
				}

				$upd = $db->prepare('UPDATE accounts SET balance = ? WHERE id = ?');
				$upd->execute([$newBalance, $accountId]);

				$ins = $db->prepare('INSERT INTO transactions (account_id, amount, type, description) VALUES (?, ?, ?, ?)');
				$ins->execute([$accountId, $amount, $type, $description !== '' ? $description : null]);

				$db->commit();
				$success = ucfirst($type) . ' successful.';
			} catch (Throwable $e) {
				$db->rollBack();
				$error = 'Transaction failed: ' . $e->getMessage();
			}
		}
	}
}

// Account dropdown and current account info
$accounts = $db->query('SELECT a.id, a.account_number, c.first_name, c.last_name FROM accounts a JOIN customers c ON c.id = a.customer_id ORDER BY c.first_name')->fetchAll();

$selectedAccount = null;
if ($accountId > 0) {
	$stmt = $db->prepare('SELECT a.*, c.first_name, c.last_name FROM accounts a JOIN customers c ON c.id = a.customer_id WHERE a.id = ?');
	$stmt->execute([$accountId]);
	$selectedAccount = $stmt->fetch();
}

$transactions = [];
if ($accountId > 0) {
	$stmt = $db->prepare('SELECT * FROM transactions WHERE account_id = ? ORDER BY created_at DESC LIMIT 100');
	$stmt->execute([$accountId]);
	$transactions = $stmt->fetchAll();
}

include __DIR__ . '/../partials/header.php';
?>
	<div class="card">
		<h2>Transactions</h2>
		<?php if ($error): ?><div class="flash error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
		<?php if ($success): ?><div class="flash success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
		<form method="get" class="row" style="gap:8px;margin-bottom:12px">
			<select name="account_id" required>
				<option value="">Select account</option>
				<?php foreach ($accounts as $a): ?>
				<option value="<?php echo (int)$a['id']; ?>" <?php echo $accountId===(int)$a['id']?'selected':''; ?>><?php echo htmlspecialchars($a['account_number'] . ' - ' . $a['first_name'] . ' ' . $a['last_name']); ?></option>
				<?php endforeach; ?>
			</select>
			<button class="btn" type="submit">Load</button>
		</form>

		<?php if ($selectedAccount): ?>
			<div class="card">
				<strong>Account</strong>: <?php echo htmlspecialchars($selectedAccount['account_number']); ?> | 
				<strong>Owner</strong>: <?php echo htmlspecialchars($selectedAccount['first_name'] . ' ' . $selectedAccount['last_name']); ?> |
				<strong>Status</strong>: <?php echo htmlspecialchars($selectedAccount['status']); ?> |
				<strong>Balance</strong>: <?php echo number_format((float)$selectedAccount['balance'], 2); ?>
			</div>

			<h3>New Transaction</h3>
			<form method="post" class="row" style="gap:8px;margin-bottom:12px">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="account_id" value="<?php echo (int)$selectedAccount['id']; ?>">
				<select name="type" required>
					<option value="deposit">Deposit</option>
					<option value="withdraw">Withdraw</option>
				</select>
				<input type="number" name="amount" min="0.01" step="0.01" placeholder="Amount" required>
				<input type="text" name="description" placeholder="Description (optional)">
				<button class="btn" type="submit">Submit</button>
			</form>

			<table class="table">
				<thead>
					<tr><th>ID</th><th>Type</th><th>Amount</th><th>Description</th><th>Date</th></tr>
				</thead>
				<tbody>
				<?php foreach ($transactions as $t): ?>
					<tr>
						<td><?php echo (int)$t['id']; ?></td>
						<td><?php echo htmlspecialchars($t['type']); ?></td>
						<td><?php echo number_format((float)$t['amount'], 2); ?></td>
						<td><?php echo htmlspecialchars((string)$t['description']); ?></td>
						<td><?php echo htmlspecialchars($t['created_at']); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>