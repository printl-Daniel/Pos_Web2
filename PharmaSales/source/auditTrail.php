<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="assets/style/style.css" />
</head>

<body>
  <?php
  session_start();
  if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit();
  }
  include '../server/connection.php';

  $username = $_POST['username'] ?? '';
  $action = $_POST['action'] ?? '';
  $startDate = $_POST['startDate'] ?? '';
  $endDate = $_POST['endDate'] ?? '';

  $query = "SELECT a.auditTrailID, u.username, a.action, a.auditTimestamp FROM audit_trail a JOIN user u ON a.username = u.username WHERE 1";

  if (!empty($username)) {
    $query .= " AND u.username LIKE :username";
  }
  if (!empty($action)) {
    $query .= " AND a.action LIKE :action";
  }
  if (!empty($startDate)) {
    $query .= " AND a.auditTimestamp >= :startDate";
  }
  if (!empty($endDate)) {
    // Add one day to include the end date in the results
    $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
    $query .= " AND a.auditTimestamp < :endDate";
  }

  $stmt = $conn->prepare($query);

  if (!empty($username)) {
    $stmt->bindValue(':username', "%$username%");
  }
  if (!empty($action)) {
    $stmt->bindValue(':action', "%$action%");
  }
  if (!empty($startDate)) {
    $stmt->bindValue(':startDate', $startDate);
  }
  if (!empty($endDate)) {
    $stmt->bindValue(':endDate', $endDate);
  }

  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  ?>
  <div class="header">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
      <div class="container header-content">
        <div class="logo">
          <img src="../assets/images/Logo1.png" class="img-fluid" width="200px" alt="Logo" />
        </div>
        <div class="collapse navbar-collapse mid-content" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item fs-6">
              <a href="homePage.php" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <div>Back to Home</div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
  <div class="main">
    <div class="page-name">
      Audit Trail
    </div>
    <div class="filter">
      <div class="username">
        <div class="section-name">Username</div>
        <div class="search">
          <input type="text" id="username" class="form-control" placeholder="Search" />
        </div>
      </div>
      <div class="action">
        <div class="section-name">Action</div>
        <div class="search">
          <input type="text" id="action" class="form-control" placeholder="Search" />
        </div>
      </div>
      <div class="date">
        <div class="section-name">Choose Date Range</div>
        <div class="search d-flex">
          <input type="date" class="form-control start-date" placeholder="Start Date" id="startDate" />
          <input type="date" class="form-control end-date" placeholder="End Date" id="endDate" />
        </div>
      </div>
    </div>
    <div class="con-table">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Action</th>
            <th>Timestamp</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($results as $row) {
            echo "<tr>
              <td>" . htmlspecialchars($row['auditTrailID']) . "</td>
              <td>" . htmlspecialchars($row['username']) . "</td>
              <td>" . htmlspecialchars($row['action']) . "</td>
              <td>" . htmlspecialchars($row['auditTimestamp']) . "</td>
            </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const usernameInput = document.getElementById('username');
      const actionInput = document.getElementById('action');
      const startDateInput = document.getElementById('startDate');
      const endDateInput = document.getElementById('endDate');

      function filterResults() {
        const username = usernameInput.value;
        const action = actionInput.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        fetch('auditTrail.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `username=${username}&action=${action}&startDate=${startDate}&endDate=${endDate}`
          })
          .then(response => response.text())
          .then(data => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');
            const newTableBody = doc.querySelector('tbody').innerHTML;
            document.querySelector('tbody').innerHTML = newTableBody;
          })
          .catch(error => console.error('Error:', error));
      }

      usernameInput.addEventListener('input', filterResults);
      actionInput.addEventListener('input', filterResults);
      startDateInput.addEventListener('change', filterResults);
      endDateInput.addEventListener('change', filterResults);
    });
  </script>
</body>
<style>
  .search .form-control {
    margin-right: -1px;
    /* Makes the inputs touch each other */
  }

  .search .start-date {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }

  .search .end-date {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }

  /* Optional: Adjust the focus styles to make them look unified */
  .search .start-date:focus+.end-date {
    border-left: none;
    /* Removes the left border of the end date when start date is focused */
  }

  .search .form-control:focus {
    position: relative;
    z-index: 2;
    /* Ensures the focused input overlaps the adjacent input slightly to cover the middle border */
  }

  .filter {
    display: flex;
    width: 100%;
    justify-content: start;
    align-items: center;
    padding: 1rem;
    gap: 2rem;
  }

  .page-name {
    width: 100%;
    height: 30%;
    font-size: 3rem;
    font-weight: 300;
    letter-spacing: 2px;
    display: flex;
    padding: 1rem;
    text-transform: uppercase;
  }

  .main {
    padding: 2rem;
  }

  .logo {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .mid-content {
    justify-content: end;
  }

  .header {
    align-items: center;
    height: 8vh;
    background-color: #85e8ca;
  }

  .header-content {
    align-items: center;
    padding: 1rem;
  }

  .header-content ul {
    gap: 5vw;
    letter-spacing: 1px;
    font-weight: 300;
  }

  .header-content ul li a {
    text-decoration: none;
    color: black;
    transition: all 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5vw;
  }

  .nav-item {
    cursor: pointer;
    position: relative;
  }

  .nav-item::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -2px;
    width: 0;
    height: 3px;
    background-color: #8ae7f2;
    transition: width 0.3s ease;
  }

  .nav-item:hover::after {
    width: 100%;
  }
</style>

</html>