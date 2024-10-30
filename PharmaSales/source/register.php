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
    <script src="../assets/scripts/register.js"></script>
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
                    <img src="../assets/images/Logo.png" class="img-fluid" width="200px" alt="Logo" />
                    <small>Register</small>
                </div>
                <div class="collapse navbar-collapse mid-content" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item fs-6">
                            <a href="" onclick="changeIframeSrc('register/general.php')">
                                <i class="fa-solid fa-user-tie"></i>
                                <div>General Manager</div>
                            </a>
                        </li>
                        <li class="nav-item fs-6">
                            <a href="" onclick="changeIframeSrc('register/manager.php')">
                                <i class="fa-solid fa-people-roof"></i>
                                <div>Manager</div>
                            </a>
                        </li>
                        <li class="nav-item fs-6">
                            <a href="#" onclick="changeIframeSrc('register/salesperson.php')">
                                <i class="fa-solid fa-universal-access"></i>
                                <div>Sales Person</div>
                            </a>
                        </li>
                        <li class="nav-item fs-6">
                            <a href="homePage.php">
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
        <iframe id="iframe" class="page-con" style="display: none;"></iframe>
    </section>
    <script>
    function changeIframeSrc(url) {
        const iframe = document.getElementById("iframe");
        iframe.src = url;
        iframe.style.display = "block";
        localStorage.setItem('iframeSrcInv', url);
    }

    window.onload = function() {
        const iframe = document.getElementById("iframe");
        const storedSrc = localStorage.getItem('iframeSrcInv');
        if (storedSrc) {
            iframe.src = storedSrc;
            iframe.style.display = "block";
        }
    }
    </script>
</body>
<style>
.page-con {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 90vh;
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