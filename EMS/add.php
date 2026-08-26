<?php

include 'db.php';

$error = '';

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

    $photoName = '';

    /* PHOTO UPLOAD */

    if (!empty($_FILES['photo']['name'])) {

        $allowed = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        $ext = strtolower(
            pathinfo(
                $_FILES['photo']['name'],
                PATHINFO_EXTENSION
            )
        );

        if (!in_array($ext, $allowed)) {

            $error =
                "Only JPG, JPEG, PNG and WEBP images are allowed.";

        }

        elseif (
            $_FILES['photo']['size']
            > 2 * 1024 * 1024
        ) {

            $error =
                "Photo must be smaller than 2 MB.";

        }

        else {

            $photoName =
                uniqid('emp_', true)
                . '.'
                . $ext;

            if (
                !move_uploaded_file(
                    $_FILES['photo']['tmp_name'],
                    'uploads/' . $photoName
                )
            ) {

                $error =
                    "Photo upload failed.";

            }

        }

    }


    /* INSERT */

    if ($error === '') {

        $stmt = $conn->prepare(
            "INSERT INTO employees
            (name,email,phone,department,
             designation,salary,joining_date,photo)
            VALUES (?,?,?,?,?,?,?,?)"
        );

        $stmt->bind_param(
            "sssssdss",
            $name,
            $email,
            $phone,
            $department,
            $designation,
            $salary,
            $joining_date,
            $photoName
        );

        if ($stmt->execute()) {

            header(
                "Location: index.php"
            );

            exit;

        }

        $error =
            "Could not save employee.";
    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Add Employee</title>

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
Add New Employee
</h1>

<p class="muted">
Create a new employee record.
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
📷
</div>


<div>

<label>
Profile Photo
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
    placeholder="CSE"
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
    placeholder="Software Developer"
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
Save Employee
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