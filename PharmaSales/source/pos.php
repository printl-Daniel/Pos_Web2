<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PharmaSales: Digital Inventory and Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/style/style.css" />
    <link rel="stylesheet" href="../assets/style/pos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .tax {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 5vh;
        padding: 1rem;
        background-color: #dcdcdc;
    }

    .tax-text {
        display: flex;
        gap: 2rem;
        height: 4vh;
        width: 40vw;
        align-items: center;
    }



    .tax input {
        border: none;
        text-align: center;
        width: 5vw;
    }

    .tax button {
        width: 5vw !important;
        border: none;
    }
    </style>
</head>

<body>
    <?php
  include 'Notification/notif.php';
  if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true || !isset($_SESSION['email'])) {
    header('Location: posLogin.php');
    exit();
  }
  $notif = new Notif();

  if (isset($_SESSION['success'])) {
    $notif->success($_SESSION['success']);
  } elseif (isset($_SESSION['error'])) {
    $notif->error($_SESSION['error']);
  }

  ?>
    <div class="header">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <div class="container-fluid header-content">
                <div class="name">
                    <img src="../assets/images/Logo1.png" class="img-fluid" width="230px" alt="Logo" />
                </div>
                <div class="collapse navbar-collapse mid-content" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item fs-6">
                            <a href="#" onclick="showLoginForm()"><i class="fa-solid fa-file-invoice"></i></a>
                        </li>
                        <li class="nav-item fs-6">
                            <a href="#" onclick="confirmLogout()"><i class="fa-solid fa-right-from-bracket"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="main container-fluid">
        <div class="left">
            <div class="left-top">
                <div class="search-bar">
                    <div class="section-name">+ Add Product</div>
                    <div class="search">
                        <input type="text" class="form-control" id="searchBarcode" placeholder="Search by barcode..." />
                        <div class="fs-5">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-cards">
                <?php
        include '../server/connection.php';
        $query = "SELECT product.productID, product.name, product.image, product.price, product.barcode FROM product JOIN inventory ON product.productID = inventory.productID WHERE inventory.quantity > 0";
        $result = $conn->query($query);

        if ($result->rowCount() > 0) {
          while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $img = $row["image"];
            $name = $row["name"];
            $price = $row["price"];
            $ID = $row["productID"];
            $barcode = $row["barcode"];

            echo '<div class="card" data-barcode="' . htmlspecialchars($barcode) . '">';
            echo '  <div class="card-img">';
            echo '    <img src="../' . htmlspecialchars($img) . '"class="img-fluid" "alt="Product Image" width="90rem";/>';
            echo '  </div>';
            echo '  <div class="card-body"style="text-align: center">';
            echo '    <h5 class="card-title fs-6">' . htmlspecialchars($name) . '</h5>';
            echo '    <p class="card-text fs-7">Price: ₱ ' . htmlspecialchars($price) . '</p>';
            echo '  </div>';
            echo '  <div class="add">';
            echo '    <button class="btn btn-primary add-item-btn" id="' . $ID . '" name="' . htmlspecialchars($name) . '" price="' . htmlspecialchars($price) . '">Add</button>';
            echo '  </div>';
            echo '</div>';
          }
        } else {
          echo "0 results";
        }
        ?>
            </div>
        </div>
        <div class="right">
            <div class="right-head fs-5">
                <p>Checkout</p>
            </div>
            <form action="../server/posSet.php" method="post">
                <div class="items">
                    <table class="table">
                        <thead class="fs-7">
                            <tr>
                                <th scope="col">Delete</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="right-bot">
                    <div class="discount">
                        <label for="discount">Discount(%)</label>
                        <input type="number" class="form-control" placeholder="0" value="0" name="discount" min="0" />
                    </div>
                    <div class="tax">
                        <div class="tax-text">
                            <div class="place-tax">Tax Rate: </div>
                            <input type="number" id="taxRateInput" class="form-control" placeholder="0" min="0"
                                step="0.01" disabled>
                        </div>
                        <button class="btn" type="button" onclick="confirmManagerCredentials()">Edit</button>
                    </div>
                    <div class="name-cust">
                        <label for="custMail">Customer Email: </label>
                        <input type="email" class="form-control" placeholder="Optional" name="custMail" />
                    </div>
                    <input type="hidden" name="totalPrice" id="hiddenTotalPrice" value="0">
                    <button type="submit" class="btn fs-6">Pay (₱ <span id="totalPrice">0</span>)</button>
                </div>
            </form>
        </div>
        <script>
        function showLoginForm() {
            Swal.fire({
                title: "Manager Login",
                html: `
        <form id="managerLoginForm" action="../server/logTran.php" method="post">
          <div class="form-group">
            <div>
              <label for="username">Username:</label>
              <input type="text" class="form-control" id="username" name="usernameLog" required>
            </div>
            <div>
              <label for="password">Password:</label>
              <input type="password" class="form-control" id="password" name="passwordLog" required>
            </div>
          </div>
        </form>`,
                showCancelButton: true,
                confirmButtonText: 'Login',
                customClass: {
                    title: 'my-custom-title',
                    confirmButton: 'confirm-button',
                },
                preConfirm: () => {
                    const username = Swal.getPopup().querySelector('#username').value;
                    const password = Swal.getPopup().querySelector('#password').value;
                    if (!username || !password) {
                        Swal.showValidationMessage("Please fill in both username and password.");
                        return false;
                    }
                    return {
                        username: username,
                        password: password
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById("managerLoginForm");
                    form.username.value = result.value.username;
                    form.password.value = result.value.password;
                    form.submit();
                }
            });
        }


        function confirmManagerCredentials() {
            Swal.fire({
                title: 'Manager Verification',
                html: `
            <input type="text" id="swal-input1" class="swal2-input" placeholder="Username">
            <input type="password" id="swal-input2" class="swal2-input" placeholder="Password">`,
                focusConfirm: false,
                preConfirm: () => {
                    const username = document.getElementById('swal-input1').value;
                    const password = document.getElementById('swal-input2').value;

                    return fetch('../server/posSet.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.verified) {
                                return true;
                            } else {
                                throw new Error(data.message ||
                                    'Invalid username or password. Please try again.');
                            }
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    enableTaxRateInput();
                    Swal.fire({
                        title: 'Verified!',
                        text: 'Tax rate input has been enabled.',
                        icon: 'success'
                    });
                }
            }).catch(error => {
                Swal.fire('Verification Failed', error.message, 'error');
            });
        }

        function enableTaxRateInput() {
            var taxRateInput = document.getElementById('taxRateInput');
            taxRateInput.disabled = !taxRateInput.disabled;
            if (!taxRateInput.disabled) {
                taxRateInput.focus();
            }
        }


        /// 

        document.getElementById('taxRateInput').addEventListener('change', function() {
            var taxRate = parseFloat(this.value);
            if (isNaN(taxRate) || taxRate < 0) {
                taxRate = 0;
                this.value = taxRate;
            }
            localStorage.setItem('taxRate', taxRate);
            updateTotalPrice();
        });

        document.getElementById('taxRateInput').addEventListener('change', function() {
            localStorage.setItem('taxRate', this.value);
            updateTotalPrice();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var savedTaxRate = localStorage.getItem('taxRate');
            if (savedTaxRate) {
                document.getElementById('taxRateInput').value = savedTaxRate;
            }
            updateTotalPrice();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.getElementById('searchBarcode');
            var productCardsContainer = document.querySelector('.product-cards');

            document.querySelector('.discount input[name="discount"]').addEventListener('change',
                updateTotalPrice);
            searchInput.addEventListener('input', function() {
                var searchValue = searchInput.value.toLowerCase();
                var productCards = document.querySelectorAll('.card');
                var visibleCards = [];

                productCards.forEach(function(card) {
                    var barcode = card.getAttribute('data-barcode').toLowerCase();
                    if (barcode.startsWith(searchValue)) {
                        card.style.display = '';
                        visibleCards.push(card);
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (visibleCards.length === 1 && visibleCards[0].getAttribute('data-barcode')
                    .toLowerCase() ===
                    searchValue) {
                    var addButton = visibleCards[0].querySelector('button');
                    if (addButton) {
                        triggerQuantityPrompt(addButton).then(() => {
                            searchInput.value = '';
                        });
                    }
                }
            });

            document.querySelectorAll('.add-item-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event
                        .preventDefault();
                    triggerQuantityPrompt(this);
                });
            });

            function triggerQuantityPrompt(button) {
                Swal.fire({
                    title: 'Enter Quantity',
                    input: 'number',
                    inputAttributes: {
                        min: 1,
                        step: 1
                    },
                    inputValue: 1,
                    showCancelButton: true,
                    confirmButtonText: 'Add',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        input: 'my-custom-input-class'
                    },
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        addItemToTable(button, result.value);
                    }
                });
            }
        });

        let rowCounter = 0;

        function addItemToTable(button, quantity) {
            var id = button.getAttribute('id');
            var name = button.getAttribute('name');
            var price = button.getAttribute('price');
            var discount = document.querySelector('.discount input[name="discount"]').value;

            var totalPriceForProduct = (price * quantity) * (1 - discount);

            var newRow = document.createElement('tr');
            var rowId = 'row-' + rowCounter++;
            newRow.id = rowId;

            var deleteCell = document.createElement('td');
            var nameCell = document.createElement('td');
            var quantityCell = document.createElement('td');
            var priceCell = document.createElement('td');

            deleteCell.innerHTML =
                `<button class="delete-btn" style="border: none; background: none; color: red;" type="button" onclick="event.stopPropagation(); confirmManagerCredentialsForDelete('${rowId}')"><i class="fa-solid fa-trash"></i></button>`;
            nameCell.textContent = name;
            priceCell.textContent = `₱ ${parseFloat(price).toFixed(2)}`;
            priceCell.classList.add('price');

            newRow.appendChild(deleteCell);
            newRow.appendChild(nameCell);
            newRow.appendChild(quantityCell);
            newRow.appendChild(priceCell);

            var quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.name = 'quantities[]';
            quantityInput.value = quantity;
            quantityInput.min = 1;
            quantityInput.style =
                "width: 3vw; text-align: center; border-radius: 8px; border-color: #dcdcdc; height: auto;";
            quantityInput.setAttribute('data-price', price);
            quantityInput.addEventListener('change', updateTotalPrice);
            quantityCell.appendChild(quantityInput);

            var productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'productIds[]';
            productIdInput.value = id;
            newRow.appendChild(productIdInput);

            var totalPriceInput = document.createElement('input');
            totalPriceInput.type = 'hidden';
            totalPriceInput.name = 'totalPricePerProduct[]';
            totalPriceInput.value = totalPriceForProduct;
            newRow.appendChild(totalPriceInput);

            var discountInput = document.createElement('input');
            discountInput.type = 'hidden';
            discountInput.name = 'discounts[]';
            discountInput.value = discount;
            newRow.appendChild(discountInput);

            document.querySelector('.table tbody').appendChild(newRow);

            updateTotalPrice();
        }

        function confirmManagerCredentialsForDelete(rowId) {
            Swal.fire({
                title: 'Manager Verification',
                html: `
      <input type="text" id="swal-input1" class="swal2-input" placeholder="Username">
      <input type="password" id="swal-input2" class="swal2-input" placeholder="Password">`,
                focusConfirm: false,
                preConfirm: () => {
                    const username = document.getElementById('swal-input1').value;
                    const password = document.getElementById('swal-input2').value;

                    return fetch('../server/posSet.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.verified) {
                                return true;
                            } else {
                                throw new Error(data.message ||
                                    'Invalid username or password. Please try again.');
                            }
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = document.getElementById(rowId);
                    if (row) {
                        row.remove();
                        Swal.fire({
                            title: 'Verified!',
                            text: 'Row has been deleted.',
                            icon: 'success'
                        });
                        updateTotalPrice();
                    }
                }
            }).catch(error => {
                Swal.fire('Verification Failed', error.message, 'error');
            });
        }

        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll('.table tbody tr').forEach(row => {
                const quantity = parseInt(row.querySelector('input[name="quantities[]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[data-price]').getAttribute('data-price')) ||
                    0;
                const discount = parseFloat(row.querySelector('input[name="discounts[]"]').value) || 0;
                total += (price * quantity) * (1 - discount / 100);
            });

            document.querySelector('#totalPrice').textContent = `₱ ${total.toFixed(2)}`;
        }

        function updateTotalPrice() {
            console.log("Called updateTotalPrice");
            var items = document.querySelectorAll('.table tbody tr');
            var total = 0;
            var discountRate = parseFloat(document.querySelector('.discount input').value) / 100 || 0;
            var taxRate = parseFloat(document.getElementById('taxRateInput').value) / 100 ||
                0;

            items.forEach(function(item) {
                var price = parseFloat(item.querySelector('.price').textContent.replace('₱', '')) || 0;
                var quantity = parseInt(item.querySelector('input[type="number"]').value) || 0;
                var itemTotal = price * quantity;
                total += itemTotal;
            });

            var discountAmount = total * discountRate;
            total -= discountAmount;

            var taxAmount = total * taxRate;
            total += taxAmount;

            document.getElementById('totalPrice').textContent = `₱ ${total.toFixed(2)}`;
            document.getElementById('hiddenTotalPrice').value = total.toFixed(2);
        }

        document.getElementById('taxRateInput').addEventListener('change', updateTotalPrice);
        document.querySelector('.discount input').addEventListener('change', updateTotalPrice);

        document.querySelector('.table tbody').addEventListener('click', function(event) {
            if (event.target.closest('.delete-btn')) {
                var button = event.target.closest('.delete-btn');
                var row = button.closest('tr');
                row.parentNode.removeChild(row);
                updateTotalPrice();
            }
        });
        </script>
</body>

</html>