<?php
require_once __DIR__ . '/../../src/auth.php';
$user = current_user();
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="/css/styles.css" />
</head>
<body>
	<div class="container">
		<div class="nav">
			<div>
				<a href="/index.php"><strong><?php echo APP_NAME; ?></strong></a>
			</div>
			<div>
				<?php if ($user): ?>
					<a href="/index.php">Dashboard</a>
					|
					<a href="/customers/index.php">Customers</a>
					|
					<a href="/accounts/index.php">Accounts</a>
					|
					<a href="/transactions/index.php">Transactions</a>
					|
					<a href="/logout.php">Logout (<?php echo htmlspecialchars($user['name']); ?>)</a>
				<?php else: ?>
					<a href="/login.php">Login</a>
				<?php endif; ?>
			</div>
		</div>