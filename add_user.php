<?php
include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();

// Check if user is logged in and is an admin
if (!login_check($mysqli) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Sanitize input function
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fetch departments
$departments_result = $mysqli->query("SELECT * FROM departments");
$departments = [];
while ($row = $departments_result->fetch_assoc()) {
    $departments[$row['department_id']] = $row['department_name'];
}

$colleges = fetch_data($mysqli, "colleges");
$departments = $colleges;

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'add':
                $name = test_input($_POST['name']);
                $email = test_input($_POST['Email']);
                $username = test_input($_POST['username']);
                $passInput = test_input($_POST['PassInput']);
                $password = password_hash($passInput, PASSWORD_DEFAULT);
                $is_admin = isset($_POST['is_admin']) ? 1 : 0;
                $post = test_input($_POST['post']);
                $department_name = test_input($_POST['department_name']);
                $college = test_input($_POST['college']);

                $stmt = $mysqli->prepare("SELECT Email, username FROM regop WHERE Email = ? OR username = ?");
                $stmt->bind_param('ss', $email, $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error = "Email or username already exists.";
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO regop (name, Email, PassInput, Password, username, is_admin, post, department_name, college) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param('sssssisss', $name, $email, $passInput, $password, $username, $is_admin, $post, $department_name, $college);
                    if ($stmt->execute()) {
                        $message = "New user added successfully.";
                    } else {
                        $error = "Failed to add user. Please try again.";
                    }
                }
                $stmt->close();
                break;

            case 'update':
                $original_username = test_input($_POST['original_username']);
                $name = test_input($_POST['name']);
                $email = test_input($_POST['Email']);
                $username = test_input($_POST['username']);
                $passInput = test_input($_POST['PassInput']);
                $password = password_hash($passInput, PASSWORD_DEFAULT);
                $is_admin = isset($_POST['is_admin']) ? 1 : 0;
                $post = test_input($_POST['post']);
                $department_name = test_input($_POST['department_name']);
                $college = test_input($_POST['college']);

                $stmt = $mysqli->prepare("UPDATE regop SET name=?, Email=?, PassInput=?, Password=?, username=?, is_admin=?, post=?, department_name=?, college=? WHERE username=?");
                $stmt->bind_param('sssssissss', $name, $email, $passInput, $password, $username, $is_admin, $post, $department_name, $college, $original_username);
                if ($stmt->execute()) {
                    $message = "User updated successfully.";
                } else {
                    $error = "Failed to update user.";
                }
                $stmt->close();
                break;

            case 'delete':
                $username = test_input($_POST['username']);
                $stmt = $mysqli->prepare("DELETE FROM regop WHERE username=?");
                $stmt->bind_param('s', $username);
                if ($stmt->execute()) {
                    $message = "User deleted successfully.";
                } else {
                    $error = "Failed to delete user.";
                }
                $stmt->close();
                break;
        }
    }
}

// Fetch all users
$result = $mysqli->query("SELECT * FROM regop");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Manage Users</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 25px;
            background-color: #f8f9fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        .container-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border-left: 4px solid #3498db;
            width: 100%;
            max-width: 800px;
            margin: 70px auto 20px;
        }

        .container-section h2 {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 20px;
            text-align: center;
            text-transform: capitalize;
        }

        .container-section h3 {
            color: #2c3e50;
            font-size: 1.2em;
            margin: 30px 0 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            padding: 10px;
            border: 1px solid #d6dce0;
            border-radius: 3px;
            font-size: 0.95em;
            box-shadow: none;
            transition: border-color 0.3s ease;
            width: 100%;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 10px;
            font-size: 0.95em;
            border-radius: 3px;
            transition: background-color 0.2s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: #e74c3c;
            border: none;
            padding: 5px 10px;
            font-size: 0.9em;
            border-radius: 3px;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .error-alert {
            background-color: #fff;
            border-left: 4px solid #e74c3c;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            color: #e74c3c;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }

        .success-alert {
            background-color: #fff;
            border-left: 4px solid #2ecc71;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            color: #2ecc71;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            display: <?php echo $message ? 'block' : 'none'; ?>;
        }

        .users-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .users-table th, .users-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .users-table th {
            background-color: #3498db;
            color: white;
        }

        .navbar-inverse {
            background-color: #2c3e50;
            border: none;
            border-radius: 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 500;
            letter-spacing: 0.5px;
            color: #fff !important;
        }

        .edit-container {
            max-width: 1000px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Department Details</a>
            </div>
        </div>
    </nav>

    <div class="container-section">
        <h2>Manage Users</h2>
        
        <div class="error-alert" id="errorAlert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <div class="success-alert" id="successAlert">
            <?php echo htmlspecialchars($message); ?>
        </div>

        <!-- Add User Form -->
        <h3>Add New User</h3>
        <form role="form" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <input class="form-control" placeholder="Name" name="name" type="text" required>
            </div>
            <div class="form-group">
                <input class="form-control" placeholder="Email" name="Email" type="email" required>
            </div>
            <div class="form-group">
                <input class="form-control" placeholder="Username" name="username" type="text" required>
            </div>
            <div class="form-group">
                <input class="form-control" placeholder="Password" name="PassInput" type="password" required>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_admin" value="1"> Is Admin</label>
            </div>
            <div class="form-group">
                <input class="form-control" placeholder="Post" name="post" type="text" required>
            </div>
            <div class="form-group">
                <select class="form-control" name="department_name" required>
                    <option value="">Select Department</option>
                    <?php foreach($departments as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept['id']); ?>"><?php echo htmlspecialchars($dept['collname']); ?></option>
                    <?php endforeach; ?>

                </select>
            </div>
            <div class="form-group">
                <input class="form-control" placeholder="College" name="college" type="text">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add User">
            </div>
        </form>
    </div>

    <div class="container-section edit-container">
        <!-- Users List -->
        <h3>Current Users</h3>
        <table class="users-table">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Admin</th>
                <th>Post</th>
                <th>Department</th>
                <th>College</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo $row['is_admin'] ? 'Yes' : 'No'; ?></td>
                <td><?php echo htmlspecialchars($row['post']); ?></td>
                <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                <td><?php echo htmlspecialchars($row['college']); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
                        <input type="submit" class="btn btn-danger btn-sm" value="Delete" onclick="return confirm('Are you sure?')">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <div class="form-group" style="margin-top: 20px;">
            <a href="details/dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script>
        function editUser(user) {
            if (confirm('Edit user ' + user.username + '?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.className = 'edit-form';
                form.innerHTML = `
                    <h3>Edit User: ${user.username}</h3>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="original_username" value="${user.username}">
                    <div class="form-group">
                        <input class="form-control" placeholder="Name" name="name" value="${user.name || ''}" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Email" name="Email" type="email" value="${user.Email}" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Username" name="username" value="${user.username}" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="PassInput" type="password" value="${user.PassInput}" required>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_admin" value="1" ${user.is_admin ? 'checked' : ''}> Is Admin</label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Post" name="post" value="${user.post || ''}" required>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="department_name" required>
                            <option value="">Select Department</option>
                            <?php foreach($departments as $id => $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>" ${user.department_name === '<?php echo $dept; ?>' ? 'selected' : ''}>
                                    <?php echo htmlspecialchars($dept); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="College" name="college" value="${user.college || ''}">
                    </div>
                    <input type="submit" class="btn btn-primary" value="Update User">
                `;
                document.querySelector('.edit-container').appendChild(form);
                window.scrollTo(0, document.body.scrollHeight);
            }
        }
    </script>
</body>
</html>