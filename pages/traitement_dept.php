<?php
include('../inc/connexion.php');


if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dept_no'])) {
    $dept_no = mysqli_real_escape_string($bdd, $_POST['dept_no']);
    

    if(!preg_match('/^d[0-9]{3}$/', $dept_no)) {

        header("Location: index.php?error=invalid_dept_format");
        exit();
    }
    
    
    $dept_info = mysqli_fetch_assoc(mysqli_query($bdd, 
        "SELECT dept_name FROM departments WHERE dept_no = '$dept_no'"));
    
    if($dept_info) {
        
        header("Location: liste_employes.php?dept_no=$dept_no&dept_name=".urlencode($dept_info['dept_name']));
        exit();
    } else {
        
        header("Location: index.php?error=dept_not_found");
        exit();
    }
}

header("Location: index.php");
exit();
?>