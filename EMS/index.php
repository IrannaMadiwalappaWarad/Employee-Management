<?php

include 'db.php';

$search = trim($_GET['search'] ?? '');

if ($search !== '') {

    $stmt = $conn->prepare(
        "SELECT * FROM employees
         WHERE name LIKE ?
         OR email LIKE ?
         OR department LIKE ?
         OR designation LIKE ?
         ORDER BY id DESC"
    );

    $like = "%$search%";

    $stmt->bind_param(
        "ssss",
        $like,
        $like,
        $like,
        $like
    );

    $stmt->execute();

    $employees = $stmt->get_result();

} else {

    $employees = $conn->query(
        "SELECT * FROM employees ORDER BY id DESC"
    );
}

$total = $conn
    ->query("SELECT COUNT(*) AS c FROM employees")
    ->fetch_assoc()['c'];

$departments = $conn
    ->query("SELECT COUNT(DISTINCT department) AS c FROM employees")
    ->fetch_assoc()['c'];

$designations = $conn
    ->query("SELECT COUNT(DISTINCT designation) AS c FROM employees")
    ->fetch_assoc()['c'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Employee Management System</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<div class="layout">

<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="brand">

        <span>EM</span>

        <div>
            Employee
            <br>
            <small>Management</small>
        </div>

    </div>

    <nav>

        <a class="active"
           href="index.php">

           ⌂ Dashboard

        </a>

        <a href="add.php">

           ＋ Add Employee

        </a>

    </nav>

    <div class="side-note">
        Simple • Secure • Smart
    </div>

</aside>


<!-- MAIN -->

<main class="main">

<header class="topbar">

    <div>

        <p class="eyebrow">
            HR DASHBOARD
        </p>

        <h1>
            Employee Management
        </h1>

        <p class="muted">
            Manage your employees from one place.
        </p>

    </div>

    <a class="btn primary"
       href="add.php">

       ＋ Add Employee

    </a>

</header>


<!-- STATISTICS -->

<section class="stats">

    <div class="stat-card">

        <div class="icon blue">
            👥
        </div>

        <div>

            <span>
                Total Employees
            </span>

            <strong>
                <?= $total ?>
            </strong>

        </div>

    </div>


    <div class="stat-card">

        <div class="icon purple">
            🏢
        </div>

        <div>

            <span>
                Departments
            </span>

            <strong>
                <?= $departments ?>
            </strong>

        </div>

    </div>


    <div class="stat-card">

        <div class="icon orange">
            💼
        </div>

        <div>

            <span>
                Designations
            </span>

            <strong>
                <?= $designations ?>
            </strong>

        </div>

    </div>

</section>


<!-- EMPLOYEE TABLE -->

<section class="panel">

<div class="panel-head">

    <div>

        <h2>
            Employee Directory
        </h2>

        <p class="muted">
            All registered employees
        </p>

    </div>


    <form class="search"
          method="GET">

        <input
            type="text"
            name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search employee..."
        >

        <button type="submit">
            🔍
        </button>

    </form>

</div>


<div class="table-wrap">

<table>

<thead>

<tr>

<th>
Employee
</th>

<th>
Contact
</th>

<th>
Department
</th>

<th>
Designation
</th>

<th>
Salary
</th>

<th>
Joining Date
</th>

<th>
Actions
</th>

</tr>

</thead>


<tbody>

<?php

if ($employees &&
    $employees->num_rows > 0):

?>

<?php

while ($row =
       $employees->fetch_assoc()):

?>

<tr>

<td>

<div class="employee">

<?php

if (!empty($row['photo']) &&
    file_exists(
        "uploads/" . $row['photo']
    )):

?>

<img
    src="uploads/<?= htmlspecialchars($row['photo']) ?>"
    alt="Employee Photo"
>

<?php else: ?>

<div class="avatar">

<?= strtoupper(
    substr($row['name'], 0, 1)
) ?>

</div>

<?php endif; ?>


<div>

<strong>
<?= htmlspecialchars($row['name']) ?>
</strong>

<small>
#EMP<?= str_pad(
    $row['id'],
    4,
    '0',
    STR_PAD_LEFT
) ?>
</small>

</div>

</div>

</td>


<td>

<div>
<?= htmlspecialchars($row['email']) ?>
</div>

<small>
<?= htmlspecialchars($row['phone']) ?>
</small>

</td>


<td>

<span class="badge">

<?= htmlspecialchars(
    $row['department']
) ?>

</span>

</td>


<td>

<?= htmlspecialchars(
    $row['designation']
) ?>

</td>


<td>

₹<?= number_format(
    (float)$row['salary'],
    2
) ?>

</td>


<td>

<?= date(
    'd M Y',
    strtotime($row['joining_date'])
) ?>

</td>


<td class="actions">

<a
    class="edit"
    href="edit.php?id=<?= $row['id'] ?>"
>
    Edit
</a>


<a
    class="delete"
    href="delete.php?id=<?= $row['id'] ?>"
    onclick="return confirm('Delete this employee?')"
>
    Delete
</a>

</td>

</tr>

<?php endwhile; ?>


<?php else: ?>

<tr>

<td
    colspan="7"
    class="empty"
>

No employees found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</section>

</main>

</div>

</body>

</html>