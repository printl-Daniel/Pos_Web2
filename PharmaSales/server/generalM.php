<?php
session_start();
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["fullN"])) {
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
    $fullN = filter_var($_POST["fullN"], FILTER_SANITIZE_STRING);
    $role = "generalManager";
    $manager = new Manager();
    $manager->addManager($username, $password, $email, $fullN, $role);
}

if (isset($_POST['personID'])) {
    $id = $_POST['personID'];
    $manager = new Manager();
    $manager->deleteManager($id);
}

if (isset($_POST['upName']) && isset($_POST['upEmail']) && isset($_POST['upUName'])) {
    $id = $_POST['upId'];
    $name = $_POST['upName'];
    $uName = $_POST['upUName'];
    $email = $_POST['upEmail'];
    $manager = new Manager();
    $manager->updateManager($id, $name, $email, $uName);
}

class Manager
{

    public function deleteManager($id)
    {
        include 'connection.php';
        try {
            $queryDel = "DELETE FROM user WHERE userID = ?";
            $stmtDel = $conn->prepare($queryDel);
            $stmtDel->execute([$id]);

            $_SESSION['success'] = "The manager was deleted successfully.";
            header("Location: ../source/register/general.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: ../source/register/general.php");
            exit();
        }
    }

    public function updateManager($id, $name, $email, $username)
    {
        include 'connection.php';
        try {
            $queryUp = "UPDATE user SET fullName = ?, email = ?, username = ? WHERE userID = ?";
            $stmtUp = $conn->prepare($queryUp);
            $stmtUp->execute([$name, $email, $username, $id]);

            $_SESSION['success'] = "Manager information updated successfully.";
            header("Location: ../source/register/general.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: ../source/register/general.php");
            exit();
        }
    }

    public function addManager($username, $password, $email, $fullN, $role)
    {
        include 'connection.php';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $checkSql = "SELECT COUNT(*) FROM user WHERE username = ?";
            $stmtCheck = $conn->prepare($checkSql);
            $stmtCheck->execute([$username]);
            $userExists = $stmtCheck->fetchColumn();

            if ($userExists > 0) {
                $_SESSION['error'] = "Username already exists. Please choose a different username.";
                header("Location: ../source/register/general.php");
                exit();
            }

            $sql = "INSERT INTO user (username, password, email, fullName, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $hashedPassword, $email, $fullN, $role]);
            $stmt = $conn->lastInsertId();

            $_SESSION['success'] = "Registered Successfully.";
            header("Location: ../source/register/general.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to insert the product. Please try again. Error: " . $e->getMessage();
            header("Location: ../source/register/general.php");
            exit();
        }
    }
}
