<?php
include_once 'connect.php';
include_once 'functions.php';
include_once 'new_header.php';

$conn = new mysqli($hostname, $username, $password, $database);

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $school = $conn->real_escape_string($_POST['school']);
    $departments = $_POST['departments'];

    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long!";
    }
    if (!preg_match("#[0-9]+#", $password)) {
        $errors[] = "Password must include at least one number!";
    }
    if (!preg_match("#[a-zA-Z]+#", $password)) {
        $errors[] = "Password must include at least one letter!";
    }
    if (!preg_match("#[A-Z]+#", $password)) {
        $errors[] = "Password must include at least one uppercase letter!";
    }
    if (!preg_match("#\W+#", $password)) {
        $errors[] = "Password must include at least one special character!";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    if (empty($errors)) {
        registration($email, $password, $school, $departments, $conn);
        $message = '<div class="alert alert-success">Registration successful!</div>';
    } else {
        $message = '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>';
    }   
}

// Handle edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $school = $_POST['school'];
    $departments = $_POST['departments'];

    $sql = "UPDATE regop SET Email = ?, school = ?, departments = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $email, $school, implode(',', $departments), $id);
    $stmt->execute();
    $stmt->close();

    header('Location: registration.php');
    exit();
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "UPDATE regop SET status = 'inactive' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    header('Location: registration.php');
    exit();
}

$users = fetch_data($conn, "regop", "id, Email, school, departments");
$schools = fetch_data($conn, "schools");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .alert {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .btn {
            margin-right: 5px;
        }
        .strength-bar {
            height: 10px;
            width: 100%;
            background-color: #ddd;
            margin-top: 5px;
        }
        .modal-header {
            background-color: #007bff;
            color: #fff;
        }
        .modal-footer .btn-primary {
            background-color: #007bff;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#departments').select2({
                placeholder: "Select department(s)",
                allowClear: true
            });

            $('#editDepartments').select2({
                placeholder: "Select department(s)",
                allowClear: true
            });
     
            document.getElementById('editSchool').addEventListener('change', function () {
                var schoolId = this.value;
                var departmentsSelect = document.getElementById('editDepartments');

                // Clear existing options
                departmentsSelect.innerHTML = '<option value="">Select department(s)</option>';

                if (schoolId) {
                    fetch('fetch_departments.php?school_id=' + schoolId)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(dept => {
                                let option = document.createElement('option');
                                option.value = dept.collno;
                                option.textContent = dept.collname;
                                departmentsSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            document.getElementById('category').addEventListener('change', function () {
                var schoolId = this.value;
                var departmentsSelect = document.getElementById('departments');

                // Clear existing options
                departmentsSelect.innerHTML = '<option value="">Select department(s)</option>';

                if (schoolId) {
                    fetch('fetch_departments.php?school_id=' + schoolId)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(dept => {
                                let option = document.createElement('option');
                                option.value = dept.collno;
                                option.textContent = dept.collname;
                                departmentsSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

        });

        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("userTable");
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strength-bar');
            let strength = 0;

            if (password.length >= 8) strength += 20;
            if (password.match(/[A-Z]/)) strength += 20;
            if (password.match(/[a-z]/)) strength += 20;
            if (password.match(/[0-9]/)) strength += 20;
            if (password.match(/[^A-Za-z0-9]/)) strength += 20;

            strengthBar.style.width = strength + '%';
            
            if (strength <= 40) {
                strengthBar.style.backgroundColor = '#ff4d4d';
            } else if (strength <= 80) {
                strengthBar.style.backgroundColor = '#ffd700';
            } else {
                strengthBar.style.backgroundColor = '#4CAF50';
            }
        }

        function editUser(id, email, school, departments) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editEmail').value = email;
            document.getElementById('editSchool').value = school;
            $('#editDepartments').val(departments.split(',')).trigger('change');
            $('#editUserModal').modal('show');
        }
    </script>
</head>
<body>
    <?php include_once 'new_header.php'; ?>

    <div class="container">
        <h1 class="text-center">User Registration</h1>
        <?php if (isset($message)) echo $message; ?>
        <form action="registration.php" method="post">

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" onkeyup="checkPasswordStrength()" required>
                <div class="strength-bar" id="strength-bar"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="school">School:</label>
                <select id="category" name="school" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach($schools as $school): ?>
                        <option value="<?php echo $school['id']; ?>"><?php echo $school['school_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="departments">Department(s):</label>
                    <select id="departments" name="departments[]" class="form-control" multiple="multiple" required>
                        <option value="">Select department(s)</option>
                    </select>
            </div>

            <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
        </form>

        <h2 class="text-center mt-5">Registered Users</h2>
        <div class="search-box">
            <label for="searchInput">Search:</label>
            <input type="text" id="searchInput" class="form-control" onkeyup="searchTable()" placeholder="Search for users..">
        </div>
        <table id="userTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Departments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $dept = "";

                while ($user = $users->fetch_assoc()):
                    $departments = explode(',', $user['departments']);
                    $dept_names = [];
                    foreach ($departments as $dept_id) {
                        $dept_names[] = fetch_values($conn, "department", "collname", "collno", $dept_id);
                    }
                    $dept = implode(' | ', $dept_names);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['Email']); ?></td>
                        <td><?php echo htmlspecialchars(fetch_values($conn, "schools", "school_name", "id", htmlspecialchars($user['school']))); ?></td>
                        <td><?php echo htmlspecialchars($dept); ?></td>
                        <td class="text-center" style="width: 30px" nowrap="nowrap">
                            <button class="btn btn-warning fa fa-edit btn-sm" onclick="editUser('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['Email']); ?>', '<?php echo htmlspecialchars($user['school']); ?>', '<?php echo htmlspecialchars($user['departments']); ?>')">Edit</button>
                            <a title="Edit" data-toggle="modal" onclick="editUser('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['Email']); ?>', '<?php echo htmlspecialchars($user['school']); ?>', '<?php echo htmlspecialchars($user['departments']); ?>')"><i class="fa fa-edit"></i></a>
                            <a href="registration.php?delete=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="registration.php" method="post">
                        <div class="modal-body">
                            <input type="hidden" id="editUserId" name="id">
                            <div class="form-group">
                                <label for="editEmail">Email:</label>
                                <input type="email" id="editEmail" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="editSchool">Category:</label>
                                <select id="editSchool" name="school" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php foreach($schools as $school): ?>
                                        <option value="<?php echo $school['id']; ?>"><?php echo $school['school_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                                                      
                            <div class="form-group">
                                <label for="editDepartments">Department(s):</label>
                                    <select id="editDepartments" name="departments[]" class="form-control" multiple="multiple" required>
                                        <option value="">Select department(s)</option>
                                    </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>