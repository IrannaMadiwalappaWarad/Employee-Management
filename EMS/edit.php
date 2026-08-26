<?php

include 'db.php';

$id =
    (int)($_GET['id'] ?? 0);


/* GET EMPLOYEE */

$stmt =
    $conn->prepare(
        "SELECT * FROM employees WHERE id=?"
    );

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$employee =
    $stmt->get_result()->fetch_assoc();


if (!$employee) {

    die("Employee not found.");

}


$error = '';


/* UPDATE */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name =
        trim($_POST['name']);

    $email =
        trim($_POST['email']);

    $phone =
        trim($_POST['phone']);

    $department =
        trim($_POST['department']);

    $designation =
        trim($_POST['designation']);

    $salary =
        (float)$_POST['salary'];

    $joining_date =
        $_POST['joining_date'];

    $photoName =
        $employee['photo'];


    /* NEW PHOTO */

    if (!empty($_FILES['photo']['name'])) {

        $allowed = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        $ext =
            strtolower(
                pathinfo(
                    $_FILES['photo']['name'],
                    PATHINFO_EXTENSION
                )
            );


        if (!in_array(
            $ext,
            $allowed
        )) {

            $error =
                "Invalid image type.";

        }

        elseif (
            $_FILES['photo']['size']
            > 2 * 1024 * 1024
        ) {

            $error =
                "Photo must be smaller than 2 MB.";

        }

        else {

            $newPhoto =
                uniqid('emp_', true)
                . '.'
                . $ext;


            if (
                move_uploaded_file(
                    $_FILES['photo']['tmp_name'],
                    'uploads/' . $newPhoto
                )
            ) {

                if (
                    $photoName &&
                    file_exists(
                        'uploads/' . $photoName
                    )
                ) {

                    unlink(
                        'uploads/' . $photoName
                    );

                }

                $photoName =
                    $newPhoto;

            }

        }

    }


    if ($error === '') {

        $stmt =
            $conn->prepare(
                "UPDATE employees
                 SET name=?,
                     email=?,
                     phone=?,
                     department=?,
                     designation=?,
                     salary=?,
                     joining_date=?,
                     photo=?
                 WHERE id=?"
            );


        $stmt->bind_param(
            "sssssdssi",
            $name,
            $email,
            $phone,
            $department,
            $designation,
            $salary,
            $joining_date,
            $photoName,
            $id
        );


        if ($stmt->execute()) {

            header(
                "Location: index.php"
            );

            exit;

        }

        $error =
            "Could not update employee.";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Edit Employee</title>

<link rel="stylesheet"
      href="style.css">

</head>

<body>

<div class="form-page">

<div class="form-card">

<div class="form-title">

<p class="eyebrow">
EMPLOYEE PROFILE
</p>

<h1>
Edit Employee
</h1>

<p class="muted">
Update employee information.
</p>

</div>


<?php if ($error): ?>

<div class="alert">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>


<form
    method="POST"
    enctype="multipart/form-data"
>


<div class="photo-upload">

<div
    id="preview"
    class="preview"
>

<?php

if (
    $employee['photo'] &&
    file_exists(
        'uploads/' .
        $employee['photo']
    )
):

?>

<img
    src="uploads/<?= htmlspecialchars(
        $employee['photo']
    ) ?>"
>

<?php else: ?>

📷

<?php endif; ?>

</div>


<div>

<label>
Change Profile Photo
</label>

<input
    type="file"
    name="photo"
    accept="image/*"
    onchange="previewPhoto(event)"
>

<small>
JPG, PNG or WEBP • Max 2 MB
</small>

</div>

</div>


<div class="grid">


<div>

<label>
Full Name *
</label>

<input
    type="text"
    name="name"
    value="<?= htmlspecialchars(
        $employee['name']
    ) ?>"
    required
>

</div>


<div>

<label>
Email *
</label>

<input
    type="email"
    name="email"
    value="<?= htmlspecialchars(
        $employee['email']
    ) ?>"
    required
>

</div>


<div>

<label>
Phone *
</label>

<input
    type="text"
    name="phone"
    value="<?= htmlspecialchars(
        $employee['phone']
    ) ?>"
    required
>

</div>


<div>

<label>
Department *
</label>

<input
    type="text"
    name="department"
    value="<?= htmlspecialchars(
        $employee['department']
    ) ?>"
    required
>

</div>


<div>

<label>
Designation *
</label>

<input
    type="text"
    name="designation"
    value="<?= htmlspecialchars(
        $employee['designation']
    ) ?>"
    required
>

</div>


<div>

<label>
Salary *
</label>

<input
    type="number"
    step="0.01"
    name="salary"
    value="<?= htmlspecialchars(
        $employee['salary']
    ) ?>"
    required
>

</div>


<div>

<label>
Joining Date *
</label>

<input
    type="date"
    name="joining_date"
    value="<?= htmlspecialchars(
        $employee['joining_date']
    ) ?>"
    required
>

</div>


</div>


<div class="form-actions">

<a
    class="btn secondary"
    href="index.php"
>
Cancel
</a>


<button
    class="btn primary"
    type="submit"
>
Update Employee
</button>

</div>


</form>

</div>

</div>


<script>

function previewPhoto(event) {

    const file =
        event.target.files[0];

    const preview =
        document.getElementById(
            'preview'
        );

    if (file) {

        const reader =
            new FileReader();

        reader.onload =
            function () {

                preview.innerHTML =
                    '<img src="' +
                    reader.result +
                    '">';

            };

        reader.readAsDataURL(file);

    }

}

</script>

</body>

</html>