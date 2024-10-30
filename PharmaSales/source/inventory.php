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
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../assets/style/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php
  session_start();
  if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit();
  }
  ?>
  <div class="header">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
      <div class="container header-content">
        <div class="logo">
          <img src="../assets/images/logo.png" class="img-fluid" width="200px" alt="Logo" />
          <small>Inventory</small>
        </div>
        <div class="collapse navbar-collapse mid-content" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item fs-6">
              <a href="#" class="nav-link" onclick="changeIframeSrc('Inventory/mainpage.php')">
                <i class="fa-solid fa-warehouse"></i>
                <div>Inventory</div>
              </a>
            </li>
            <li class="nav-item fs-6">
              <a href="" class="nav-link" onclick="changeIframeSrc('Inventory/performance.php')">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <div>Performance</div>
              </a>
            </li>
            <li class="nav-item fs-6">
              <a href="#" class="nav-link" onclick="changeIframeSrc('Inventory/addProduct.php')">
                <i class="fa-solid fa-capsules"></i>
                <div>Add Product</div>
              </a>
            </li>
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
  <section class="inv-main-con">
    <iframe id="iframeInv" class="page-con" style="display: none"></iframe>
  </section>
  <script>
  function changeIframeSrc(url) {
    const iframe = document.getElementById("iframeInv");
    iframe.src = url;
    iframe.style.display = "block";
    localStorage.setItem("iframeSrc", url);
  }

  window.onload = function() {
    const iframe = document.getElementById("iframeInv");
    const storedSrc = localStorage.getItem("iframeSrc");
    if (storedSrc) {
      iframe.src = storedSrc;
      iframe.style.display = "block";
    }
  };
  </script>
</body>

<style>
body {
  overflow: hidden;
}

.page-con {
  height: 100vh;
  width: 100%;
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
  height: 10vh;
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