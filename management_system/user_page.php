<?php
session_start();
include 'server/connection.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'u') {
    // Redirect to login page if not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$admin_id = $_SESSION['user_id'];

// Fetch the admin details (optional, if you want to display admin info)
$query_admin = "SELECT * FROM users WHERE user_id = '$admin_id' AND user_type = 'u'";
$result_admin = mysqli_query($conn, $query_admin);

if ($result_admin && mysqli_num_rows($result_admin) == 1) {
    $admin = mysqli_fetch_assoc($result_admin);
} else {
    echo "users not found!";
    exit();
}




// Check if there's a search term
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the SQL query to include the search filter
$sql = "SELECT students_id, student_name, course, status, block FROM students";
if ($searchTerm) {
    $sql .= " WHERE student_name LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR course LIKE '%" . $conn->real_escape_string($searchTerm) . "%' OR block LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
}


$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="css/userpage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand text-warning fw-bold">Welcome, User!</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <form class="d-flex mx-auto" role="search" method="GET">
                    <input class="form-control me-2 rounded-pill border-warning" type="search" id="searchInput" name="search" placeholder="Search..." aria-label="Search" value="<?= htmlspecialchars($searchTerm); ?>">
                    <button class="btn btn-warning rounded-pill" id="searchBtn" type="submit">Search</button>
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-bold" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    
<!-- Add Student Button (now triggers modal) -->
<div class="add-student-btn">
  <button class="btn btn-warning rounded-pill" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="add_student.php" method="POST">
          <div class="mb-3">
            <label for="studentName" class="form-label">Student Name</label>
            <input type="text" class="form-control" id="studentName" name="student_name" required>
          </div>
          <div class="mb-3">
            <label for="course" class="form-label">Course</label>
            <input type="text" class="form-control" id="course" name="course" required>
          </div>
          <div class="mb-3">
            <label for="block" class="form-label">Block</label>
            <input type="text" class="form-control" id="block" name="block" required>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
              <option value="r">Regular</option>
              <option value="i">Irregular</option>
              <option value="g">Graduate</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
      </div>
    </div>
  </div>
</div>

    <!-- Table Section -->
    <table>
    <thead>
        <tr>
            <th>View</th>
            <th>Student name</th>
            <th>Course & Block</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data for each student
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='user_view_student.php?id=" . $row['students_id'] . "'>View</a></td>";
                echo "<td>" . $row['student_name'] . "</td>";
                echo "<td>" . $row['course'] . " " . $row['block'] . "</td>";
                echo "<td>";
                
                // Check the status and display corresponding text
                if ($row['status'] == 'r') {
                    echo "Regular";
                } elseif ($row['status'] == 'i') {
                    echo "Irregular";
                } elseif ($row['status'] == 'g') {
                    echo "Graduate";
                } else {
                    echo "Unknown"; // In case there is an unexpected value
                }

                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No students found</td></tr>";
        }
        ?>
    </tbody>
</table>

    <!-- Footer -->
    <footer>
        &copy; 2024 Student Management System. All rights reserved.
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchBtn = document.getElementById("searchBtn");
        const searchInput = document.getElementById("searchInput");

        searchBtn.addEventListener("click", () => {
            const searchTerm = searchInput.value;
            window.location.href = `?search=${searchTerm}`; // Reload the page with the search query
        });

        // Optional: Add event listener for Enter key to trigger search
        searchInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                searchBtn.click();
            }
        });
    });
    </script>

</body>
</html>
