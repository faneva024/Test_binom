<?php
include('../inc/connexion.php');
include('../inc/fonction.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $emp_no = isset($_POST['emp_no']) ? mysqli_real_escape_string($bdd, $_POST['emp_no']) : '';
    $new_dept_no = isset($_POST['new_dept_no']) ? mysqli_real_escape_string($bdd, $_POST['new_dept_no']) : '';
    $from_date = isset($_POST['from_date']) ? mysqli_real_escape_string($bdd, $_POST['from_date']) : '';
    $current_dept_from_date = isset($_POST['current_dept_from_date']) ? mysqli_real_escape_string($bdd, $_POST['current_dept_from_date']) : '';
    $current_dept_no = isset($_POST['current_dept_no']) ? mysqli_real_escape_string($bdd, $_POST['current_dept_no']) : '';

    if (empty($emp_no) || !is_numeric($emp_no) || empty($new_dept_no) || empty($from_date)) {
        header("Location: changer_departement.php?emp_no=$emp_no&error=" . urlencode("Veuillez remplir tous les champs requis et fournir un numéro d'employé valide."));
        exit();
    }

    if (!empty($current_dept_from_date) && strtotime($from_date) < strtotime($current_dept_from_date)) {
        header("Location: changer_departement.php?emp_no=$emp_no&error=" . urlencode("La date de début du nouveau département ne peut pas être antérieure à la date de début du département actuel."));
        exit();
    }


    $result = change_employee_department($bdd, $emp_no, $new_dept_no, $from_date, $current_dept_no);

    if ($result['success']) {
        header("Location: fiche_employe.php?emp_no=$emp_no&success=" . urlencode($result['message']));
        exit();
    } else {
        header("Location: changer_departement.php?emp_no=$emp_no&error=" . urlencode($result['message']));
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>