<?php

include 'db.php';

$id =
    (int)($_GET['id'] ?? 0);


/* GET PHOTO */

$stmt =
    $conn->prepare(
        "SELECT photo FROM employees WHERE id=?"
    );

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$row =
    $stmt->get_result()->fetch_assoc();


if ($row) {

    /* DELETE PHOTO */

    if (
        !empty($row['photo']) &&
        file_exists(
            'uploads/' . $row['photo']
        )
    ) {

        unlink(
            'uploads/' . $row['photo']
        );

    }


    /* DELETE EMPLOYEE */

    $stmt =
        $conn->prepare(
            "DELETE FROM employees WHERE id=?"
        );

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

}


header(
    "Location: index.php"
);

exit;

?>