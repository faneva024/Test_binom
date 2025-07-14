<?php
include('../inc/connexion.php');
include('../inc/fonction.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $emp_no = isset($_POST['emp_no']) ? mysqli_real_escape_string($bdd, $_POST['emp_no']) : '';
    $dept_no = isset($_POST['dept_no']) ? mysqli_real_escape_string($bdd, $_POST['dept_no']) : '';
    $from_date = isset($_POST['from_date']) ? mysqli_real_escape_string($bdd, $_POST['from_date']) : '';
    $current_manager_from_date = isset($_POST['current_manager_from_date']) ? mysqli_real_escape_string($bdd, $_POST['current_manager_from_date']) : '';

  
    if (empty($emp_no) || !is_numeric($emp_no) || empty($dept_no) || empty($from_date)) {
        header("Location: devenir_manager.php?emp_no=$emp_no&error=" . urlencode("Veuillez remplir tous les champs requis et fournir un numéro d'employé valide."));
        exit();
    }

  
    if (!empty($current_manager_from_date) && strtotime($from_date) < strtotime($current_manager_from_date)) {
        header("Location: devenir_manager.php?emp_no=$emp_no&error=" . urlencode("La date de début du nouveau manager ne peut pas être antérieure à la date de début du manager actuel."));
        exit();
    }


    $stmt_check = mysqli_prepare($bdd, "SELECT COUNT(*) FROM dept_manager WHERE emp_no = ? AND dept_no = ? AND from_date = ?");
    if ($stmt_check === false) {
        header("Location: devenir_manager.php?emp_no=$emp_no&error=" . urlencode("Erreur de préparation de la vérification du manager existant."));
        exit();
    }
    mysqli_stmt_bind_param($stmt_check, "iss", $emp_no, $dept_no, $from_date);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $count);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($count > 0) {
        header("Location: devenir_manager.php?emp_no=$emp_no&error=" . urlencode("Cet employé est déjà manager de ce département à partir de cette date."));
        exit();
    }


    $result = set_employee_as_manager($bdd, $emp_no, $dept_no, $from_date);

    if ($result['success']) {
        header("Location: fiche_employe.php?emp_no=$emp_no&success=" . urlencode($result['message']));
        exit();
    } else {
        header("Location: devenir_manager.php?emp_no=$emp_no&error=" . urlencode($result['message']));
        exit();
    }
} else {

    header("Location: index.php");
    exit();
}
?>