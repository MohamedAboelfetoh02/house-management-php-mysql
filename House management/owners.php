<?php
session_start();
include 'db_connection.php'; // Added this line to include the database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Handle Create
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_owner'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "INSERT INTO OWNERS (name, phone, email) VALUES ('$name', '$phone', '$email')";
    $conn->query($sql);
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_owner'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "UPDATE OWNERS SET name='$name', phone='$phone', email='$email' WHERE id=$id";
    $conn->query($sql);
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id = $conn->real_escape_string($_GET['delete_id']);
    $conn->query("DELETE FROM OWNERS WHERE id=$id");

    // Reset auto-increment counter
    $maxIdResult = $conn->query("SELECT IFNULL(MAX(id), 0) AS max_id FROM OWNERS");
    $maxIdRow = $maxIdResult->fetch_assoc();
    $newAutoIncrement = $maxIdRow['max_id'] + 1;
    $conn->query("ALTER TABLE OWNERS AUTO_INCREMENT = $newAutoIncrement");
}

// Fetch all owners
$owners = $conn->query("SELECT * FROM OWNERS");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Owners</title>
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
        input[type="text"], input[type="email"] {
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
            if (confirm("Are you sure you want to delete this record?")) {
                window.location.href = deleteUrl;
            }
        }
    </script>
</head>
<body>
    <h1>Manage Owners</h1>
    <div class="container">
        <form method="POST">
            <h3>Add New Owner</h3>
            <div class="form-group">
                <input type="text" name="name" placeholder="Owner Name" required>
            </div>
            <div class="form-group">
                <input type="text" name="phone" placeholder="Phone" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <button type="submit" name="add_owner">Add Owner</button>
        </form>

        <h3>All Owners</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $owners->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <a href="javascript:void(0);" 
                       onclick="confirmDelete('owners.php?delete_id=<?php echo $row['id']; ?>')" 
                       style="color: red;">Delete</a> |
                    <a href="owners.php?edit_id=<?php echo $row['id']; ?>" style="color: green;">Edit</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <?php if (isset($_GET['edit_id'])): 
            $edit_id = $conn->real_escape_string($_GET['edit_id']);
            $edit_query = $conn->query("SELECT * FROM OWNERS WHERE id=$edit_id");
            $edit_data = $edit_query->fetch_assoc();
        ?>
        <form method="POST">
            <h3>Edit Owner</h3>
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
            <div class="form-group">
                <input type="text" name="name" value="<?php echo $edit_data['name']; ?>" required>
            </div>
            <div class="form-group">
                <input type="text" name="phone" value="<?php echo $edit_data['phone']; ?>" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" value="<?php echo $edit_data['email']; ?>" required>
            </div>
            <button type="submit" name="update_owner">Update Owner</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
