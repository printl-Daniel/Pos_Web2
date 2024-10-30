<?php

session_start();
include 'connection.php';

if (isset($_POST['productId'])) {
    $productId = $_POST['productId'];
    try {
        $conn->beginTransaction();

        $queryDel = "DELETE FROM sales WHERE productID = ?";
        $stmtDel = $conn->prepare($queryDel);
        $stmtDel->execute([$productId]);

        $queryInv = "DELETE FROM inventory WHERE productID = ?";
        $stmtInv = $conn->prepare($queryInv);
        $stmtInv->execute([$productId]);

        $queryProd = "DELETE FROM product WHERE productID = ?";
        $stmtProd = $conn->prepare($queryProd);
        $stmtProd->execute([$productId]);

        $conn->commit();

        $_SESSION['success'] = "The product was deleted successfully.";
        header("Location: ../source/Inventory/mainpage.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../source/Inventory/mainpage.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Product ID not provided.";
    header("Location: ../source/Inventory/mainpage.php");
    exit();
}
