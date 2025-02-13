<!DOCTYPE html>
<html lang="en">
<head>
<?php 
 $cssStack =[];
 $jsStack =[];
 $selectedContent = isset($_GET['content']) ? $_GET['content'] : 'index';
 $contentPath = "$selectedContent";
include("layouts/header.php"); 

?>
</head>
<body class="animsition">
	
	<!-- Nav -->
	<?php include("layouts/navbar.php") ?>

	
    <!-- Cart -->
    <?php include("layouts/viewcart.php") ?>

	<!-- Product -->
    <?php 
        if (file_exists($contentPath)) {
            include($contentPath);
        } else {
            include("layouts/showproduct.php");
        }
        ?>

	<!-- Footer -->
	<?php include("layouts/footer.php") ?>


	<!-- Back to top -->
	<?php include("layouts/backtotop.php") ?>

	<!-- Quick View -->
	<?php include("layouts/quickview.php") ?>

<!--===============================================================================================-->	
    <?php include("layouts/js.php") ?>

</body>
</html>