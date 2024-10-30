<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/style/style.css" />
</head>

<body>
    <?php
  session_start();
  if (!isset($_SESSION['authenticatedSP']) || $_SESSION['authenticatedSP'] !== true) {
    header('Location: posLogin.php');
    exit();
  }
  include '../server/connection.php';

  $customer = $_POST['customer'] ?? '';
  $salesperson = $_POST['salesperson'] ?? '';
  $product = $_POST['product'] ?? '';
  $quantity = $_POST['quantity'] ?? '';
  $discount = $_POST['discount'] ?? '';
  $totalPrice = $_POST['totalPrice'] ?? '';
  $startDate = $_POST['startDate'] ?? '';
  $endDate = $_POST['endDate'] ?? '';

  $query = "SELECT s.saleID, p.name, c.email, sp.spName, s.quantitySold, s.timestamp, s.totalPrice 
          FROM sales s 
          JOIN product p ON s.productID = p.productID 
          JOIN customer c ON s.customerID = c.customerID 
          JOIN salesperson sp ON s.salespersonID = sp.salespersonID
          WHERE 1";

  if (!empty($customer)) {
    $query .= " AND c.email LIKE :customer";
  }
  if (!empty($salesperson)) {
    $query .= " AND sp.spName LIKE :salesperson";
  }
  if (!empty($product)) {
    $query .= " AND p.name LIKE :product";
  }
  if (!empty($quantity)) {
    $query .= " AND s.quantitySold = :quantity";
  }

  if (!empty($totalPrice)) {
    $query .= " AND s.totalPrice = :totalPrice";
  }
  if (!empty($startDate)) {
    $query .= " AND s.timestamp >= :startDate";
  }
  if (!empty($endDate)) {
    $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
    $query .= " AND s.timestamp < :endDate";
  }

  $stmt = $conn->prepare($query);

  if (!empty($customer)) {
    $stmt->bindValue(':customer', "%$customer%");
  }
  if (!empty($salesperson)) {
    $stmt->bindValue(':salesperson', "%$salesperson%");
  }
  if (!empty($product)) {
    $stmt->bindValue(':product', "%$product%");
  }
  if (!empty($quantity)) {
    $stmt->bindValue(':quantity', $quantity);
  }
  if (!empty($totalPrice)) {
    $stmt->bindValue(':totalPrice', $totalPrice);
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
            <div class="container-fluid header-content">
                <div class="logo">
                    <img src="../assets/images/Logo1.png" class="img-fluid" width="200px" alt="Logo" />
                </div>
                <div class="collapse navbar-collapse mid-content" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item fs-6">
                            <a href="pos.php" class="nav-link">
                                <i class="fa-solid fa-house"></i>
                                <div>Back</div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="main">
        <div class="page-name">
            PURCHASE
        </div>
        <div class="filter">
            <div class="filter-field">
                <div class="section-name">Customer</div>
                <div class="search">
                    <input type="text" id="customer" class="form-control" placeholder="Search" />
                </div>
            </div>
            <div class="filter-field">
                <div class="section-name">Salesperson</div>
                <div class="search">
                    <input type="text" id="salesperson" class="form-control" placeholder="Search" />
                </div>
            </div>
            <div class="filter-field">
                <div class="section-name">Product</div>
                <div class="search">
                    <input type="text" id="product" class="form-control" placeholder="Search" />
                </div>
            </div>
            <div class="filter-field">
                <div class="section-name">Quantity</div>
                <div class="search">
                    <input type="number" id="quantity" class="form-control" placeholder="Search" />
                </div>
            </div>
            <div class="filter-field">
                <div class="section-name">Total Price</div>
                <div class="search">
                    <input type="number" step="0.01" id="totalPrice" class="form-control" placeholder="Search" />
                </div>
            </div>
            <div class="filter-field">
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
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Salesperson</th>
                        <th>Quantity</th>
                        <th>Timestamp</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
          foreach ($results as $row) {
            echo "<tr>
              <td>" . htmlspecialchars($row['saleID']) . "</td>
              <td>" . htmlspecialchars($row['name']) . "</td>
              <td>" . htmlspecialchars($row['email']) . "</td>
              <td>" . htmlspecialchars($row['spName']) . "</td>
              <td>" . htmlspecialchars($row['quantitySold']) . "</td>
              <td>" . htmlspecialchars($row['timestamp']) . "</td>
              <td>" . htmlspecialchars($row['totalPrice']) . "</td>
            </tr>";
          }
          ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterFields = ['customer', 'salesperson', 'product', 'quantity', 'totalPrice',
            'startDate',
            'endDate'
        ];
        const filterInputs = {};

        filterFields.forEach(field => {
            filterInputs[field] = document.getElementById(field);
            filterInputs[field].addEventListener('input', filterResults);
        });

        function filterResults() {
            const filters = {};
            filterFields.forEach(field => {
                filters[field] = filterInputs[field].value;
            });

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(filters).toString()
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
    });
    </script>
</body>
<style>
.con-table {
    overflow-y: auto;
    height: 62vh;
}

.search .form-control {
    margin-right: -1px;
}

.search .start-date {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.search .end-date {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.search .start-date:focus+.end-date {
    border-left: none;
}

.search .form-control:focus {
    position: relative;
    z-index: 2;
}

.filter {
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    justify-content: start;
    align-items: center;
    padding: 1rem 0 1rem 0;
    gap: 0.5rem;
}

.filter-field {
    display: flex;
    flex-direction: column;
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
    padding: 1rem 7rem 1rem 2rem;
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
    background-color: white;
    transition: width 0.3s ease;
}

.nav-item:hover::after {
    width: 100%;
}
</style>

</html>