<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $cssStack = [];
    $jsStack = [];
    $selectedContent = isset($_GET['content']) ? $_GET['content'] : 'index';
    $contentPath = "$selectedContent";
    require_once __DIR__ . "/../api/config/constants.php";
    include("layouts/header.php");

    ?>
</head>
<script>
    // Gán BASE_API_URL cho biến JS từ PHP
    const BASE_API_URL = "<?php echo BASE_API_URL; ?>";
</script>

<body class="animsition">

    <!-- Nav -->
    <?php include("layouts/navbar.php") ?>


    <!-- Cart -->
    <?php include("layouts/viewcart.php") ?>

    <!-- Product -->
    <main>
        <?php
        if (file_exists($contentPath)) {
            echo $contentPath;
            include($contentPath);
        } else {
            include("layouts/showproduct.php");
        }
        ?>
    </main>


    <!-- Footer -->
    <?php include("layouts/footer.php") ?>


    <!-- Back to top -->
    <?php include("layouts/backtotop.php") ?>

    <!-- Quick View -->
    <?php include("layouts/quickview.php") ?>

    <!--===============================================================================================-->
    <?php include("layouts/js.php") ?>

</body>
<script src="../client/js/logout.js"></script>

</html>