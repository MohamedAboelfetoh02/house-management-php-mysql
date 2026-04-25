<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Handle Create
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_house'])) {
    $address = $conn->real_escape_string($_POST['address']);
    $owner_id = $conn->real_escape_string($_POST['owner_id']);
    $photo = $conn->real_escape_string($_POST['photo']);

    $sql = "INSERT INTO HOUSES (address, owner_id, photo) VALUES ('$address', '$owner_id', '$photo')";
    $conn->query($sql);
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_house'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $address = $conn->real_escape_string($_POST['address']);
    $owner_id = $conn->real_escape_string($_POST['owner_id']);
    $photo = $conn->real_escape_string($_POST['photo']);

    $sql = "UPDATE HOUSES SET address='$address', owner_id='$owner_id', photo='$photo' WHERE id=$id";
    $conn->query($sql);
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id = $conn->real_escape_string($_GET['delete_id']);
    $conn->query("DELETE FROM HOUSES WHERE id=$id");

    // Reset auto-increment counter
    $maxIdResult = $conn->query("SELECT IFNULL(MAX(id), 0) AS max_id FROM HOUSES");
    $maxIdRow = $maxIdResult->fetch_assoc();
    $newAutoIncrement = $maxIdRow['max_id'] + 1;
    $conn->query("ALTER TABLE HOUSES AUTO_INCREMENT = $newAutoIncrement");
}

// Fetch all houses
$houses = $conn->query("SELECT HOUSES.*, OWNERS.name AS owner_name FROM HOUSES LEFT JOIN OWNERS ON HOUSES.owner_id = OWNERS.id");

// Fetch all owners for dropdown
$owners = $conn->query("SELECT id, name FROM OWNERS");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Houses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            text-align: center;
        }
        .container {
            margin: 20px auto;
            width: 95%;
            max-width: 800px;
            background: white;
            color: #333;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
            word-wrap: break-word;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #6a11cb;
            color: white;
        }
        .form-group {
            margin: 10px 0;
        }
        input[type="text"], select {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            background-color: #6a11cb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2575fc;
        }
        @media (max-width: 480px) {
            table {
                font-size: 12px;
            }
            th, td {
                padding: 5px;
            }
        }
        .message {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
    <script>
        function confirmDelete(deleteUrl) {
            if (confirm("Are you sure you want to delete this house?")) {
                window.location.href = deleteUrl;
            }
        }
    </script>
</head>
<body>
    <h1>Manage Houses</h1>
    <div class="container">
        <form method="POST">
            <h3>Add New House</h3>
            <div class="form-group">
                <input type="text" name="address" placeholder="House Address" required>
            </div>
            <div class="form-group">
                <select name="owner_id" required>
                    <option value="">Select Owner</option>
                    <?php while ($owner = $owners->fetch_assoc()): ?>
                        <option value="<?php echo $owner['id']; ?>"><?php echo $owner['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="photo" placeholder="Photo Filename (e.g., photo1.jpg)" required>
            </div>
            <button type="submit" name="add_house">Add House</button>
        </form>

        <h3>All Houses</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Address</th>
                <th>Owner</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $houses->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['address']; ?></td>
                <td><?php echo $row['owner_name'] ? $row['owner_name'] : 'Unassigned'; ?></td>
                <td><img src="<?php echo $row['photo']; ?>" alt="House Photo" style="width: 80px; height: 50px;"></td>
                <td>
                    <a href="javascript:void(0);" 
                       onclick="confirmDelete('houses.php?delete_id=<?php echo $row['id']; ?>')" 
                       style="color: red;">Delete</a> |
                    <a href="houses.php?edit_id=<?php echo $row['id']; ?>" style="color: green;">Edit</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if (isset($_GET['edit_id'])): 
            $edit_id = $conn->real_escape_string($_GET['edit_id']);
            $edit_query = $conn->query("SELECT * FROM HOUSES WHERE id=$edit_id");
            $edit_data = $edit_query->fetch_assoc();
        ?>
        <form method="POST">
            <h3>Edit House</h3>
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
            <div class="form-group">
                <input type="text" name="address" value="<?php echo $edit_data['address']; ?>" required>
            </div>
            <div class="form-group">
                <select name="owner_id" required>
                    <option value="">Select Owner</option>
                    <?php
                    $owners->data_seek(0); // Reset owners result set
                    while ($owner = $owners->fetch_assoc()):
                    ?>
                        <option value="<?php echo $owner['id']; ?>" <?php echo $owner['id'] == $edit_data['owner_id'] ? 'selected' : ''; ?>>
                            <?php echo $owner['name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="photo" value="<?php echo $edit_data['photo']; ?>" required>
            </div>
            <button type="submit" name="update_house">Update House</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
