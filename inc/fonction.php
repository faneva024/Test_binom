<?php

function liste_dept($bdd){
    return mysqli_query($bdd, "SELECT * FROM vue_departements_managers");
}

function get_employees_by_dept($bdd, $dept_no) {
    $dept_no = mysqli_real_escape_string($bdd, $dept_no);
    return mysqli_query($bdd, "SELECT * FROM vue_employes_par_departement WHERE dept_no = '$dept_no'");
}

function get_employee_details($bdd, $emp_no) {
    $emp_no = mysqli_real_escape_string($bdd, $emp_no);
    return [
        'info' => mysqli_fetch_assoc(mysqli_query($bdd, "SELECT * FROM employees WHERE emp_no = '$emp_no'")),
        'current_salary' => mysqli_fetch_assoc(mysqli_query($bdd, 
            "SELECT salary FROM salaries WHERE emp_no = '$emp_no' ORDER BY to_date DESC LIMIT 1")),
        'current_title' => mysqli_fetch_assoc(mysqli_query($bdd, 
            "SELECT title FROM titles WHERE emp_no = '$emp_no' ORDER BY to_date DESC LIMIT 1")),
        'salaries' => mysqli_query($bdd, "SELECT * FROM salaries WHERE emp_no = '$emp_no' ORDER BY from_date DESC"),
        'titles' => mysqli_query($bdd, "SELECT * FROM titles WHERE emp_no = '$emp_no' ORDER BY from_date DESC")
    ];
}

function get_poste_plus_long($bdd, $emp_no) {
    $emp_no = mysqli_real_escape_string($bdd, $emp_no);
    $res = mysqli_query($bdd, "SELECT title, duree FROM vue_poste_plus_long WHERE emp_no = '$emp_no' ORDER BY duree DESC LIMIT 1");
    return mysqli_fetch_assoc($res);
}

function get_statistiques_emplois($bdd) {
    return mysqli_query($bdd, "SELECT * FROM vue_statistiques_emplois");
}

function search_employees($bdd, $params) {
    $defaults = [
        'dept' => '',
        'nom' => '',
        'age_min' => null,
        'age_max' => null,
        'page' => 1,
        'limit' => 20
    ];
    $params = array_merge($defaults, $params);

    foreach ($params as $key => $value) {
        if (is_string($value)) {
            $params[$key] = mysqli_real_escape_string($bdd, $value);
        }
    }

    $offset = ($params['page'] - 1) * $params['limit'];
    $conditions = "WHERE 1=1";

    if (!empty($params['dept'])) {
        $conditions .= " AND dept_no = '{$params['dept']}'";
    }
    if (!empty($params['nom'])) {
        $conditions .= " AND (first_name LIKE '%{$params['nom']}%' OR last_name LIKE '%{$params['nom']}%')";
    }
    if (!is_null($params['age_min']) && $params['age_min'] !== '') {
        $conditions .= " AND age >= {$params['age_min']}";
    }
    if (!is_null($params['age_max']) && $params['age_max'] !== '') {
        $conditions .= " AND age <= {$params['age_max']}";
    }

    $sql = "SELECT * FROM vue_employes_departements_ages $conditions LIMIT {$params['limit']} OFFSET $offset";
    $res = mysqli_query($bdd, $sql);

    $sql_total = "SELECT COUNT(*) as total FROM vue_employes_departements_ages $conditions";
    $res_total = mysqli_query($bdd, $sql_total);
    $total = mysqli_fetch_assoc($res_total)['total'] ?? 0;

    return [
        'result' => $res,
        'total' => $total
    ];
}

function get_departements($bdd) {
    return mysqli_query($bdd, "SELECT dept_no, dept_name FROM departments");
}

function get_current_departement($bdd, $emp_no) {
    $emp_no = mysqli_real_escape_string($bdd, $emp_no);
    $sql = "SELECT d.dept_name, de.dept_no, de.from_date
            FROM dept_emp de
            JOIN departments d ON d.dept_no = de.dept_no
            WHERE de.emp_no = '$emp_no' AND de.to_date > NOW()";
    return mysqli_fetch_assoc(mysqli_query($bdd, $sql));
}

function get_current_manager($bdd, $dept_no) {
    $dept_no = mysqli_real_escape_string($bdd, $dept_no);
    $sql = "SELECT dm.emp_no, e.first_name, e.last_name, dm.from_date
            FROM dept_manager dm
            JOIN employees e ON e.emp_no = dm.emp_no
            WHERE dm.dept_no = '$dept_no' AND dm.to_date > NOW()";
    return mysqli_fetch_assoc(mysqli_query($bdd, $sql));
}

function get_departements_except_current($bdd, $current_dept_no = null) {
    $sql = "SELECT dept_no, dept_name FROM departments";
    if (!empty($current_dept_no)) {
        $current_dept_no_escaped = mysqli_real_escape_string($bdd, $current_dept_no);
        $sql .= " WHERE dept_no != '$current_dept_no_escaped'";
    }
    return mysqli_query($bdd, $sql);
}

function change_employee_department($bdd, $emp_no, $new_dept_no, $from_date, $old_dept_no = null) {
    // Utilisation de requêtes préparées pour la sécurité
    mysqli_begin_transaction($bdd); // Démarre une transaction

    try {
        // 1. Mettre fin à l'enregistrement du département actuel (si l'employé en a un et qu'il est encore actif)
        if (!empty($old_dept_no)) {
            // La date de fin sera la veille de la nouvelle date de début
            $end_current_dept_date = date('Y-m-d', strtotime($from_date . ' -1 day'));

            $stmt_update = mysqli_prepare($bdd, "UPDATE dept_emp SET to_date = ? WHERE emp_no = ? AND dept_no = ? AND to_date > CURDATE()");
            if ($stmt_update === false) {
                throw new Exception("Erreur de préparation (UPDATE dept_emp): " . mysqli_error($bdd));
            }
            mysqli_stmt_bind_param($stmt_update, "sis", $end_current_dept_date, $emp_no, $old_dept_no);
            mysqli_stmt_execute($stmt_update);
            
            if (mysqli_stmt_errno($stmt_update)) {
                throw new Exception("Erreur d'exécution (UPDATE dept_emp): " . mysqli_stmt_error($stmt_update));
            }
            mysqli_stmt_close($stmt_update);
        }

        // 2. Insérer le nouvel enregistrement pour le département
        $stmt_insert = mysqli_prepare($bdd, "INSERT INTO dept_emp (emp_no, dept_no, from_date, to_date) VALUES (?, ?, ?, '9999-01-01')");
        if ($stmt_insert === false) {
            throw new Exception("Erreur de préparation (INSERT dept_emp): " . mysqli_error($bdd));
        }
        mysqli_stmt_bind_param($stmt_insert, "iss", $emp_no, $new_dept_no, $from_date);
        mysqli_stmt_execute($stmt_insert);

        if (mysqli_stmt_errno($stmt_insert)) {
            throw new Exception("Erreur d'exécution (INSERT dept_emp): " . mysqli_stmt_error($stmt_insert));
        }
        mysqli_stmt_close($stmt_insert);

        mysqli_commit($bdd); // Valide la transaction
        return ['success' => true, 'message' => 'Département changé avec succès.'];

    } catch (Exception $e) {
        mysqli_rollback($bdd); // Annule la transaction en cas d'erreur
        return ['success' => false, 'message' => 'Erreur lors du changement de département : ' . $e->getMessage()];
    }
}

function set_employee_as_manager($bdd, $emp_no, $dept_no, $from_date) {
    // Utilisation de requêtes préparées pour la sécurité
    mysqli_begin_transaction($bdd); // Démarre une transaction

    try {
        // 1. Mettre fin au rôle de manager actuel pour ce département (s'il y en a un et qu'il est actif)
        // La date de fin sera la veille de la nouvelle date de début du manager
        $end_current_manager_date = date('Y-m-d', strtotime($from_date . ' -1 day'));
        
        $stmt_update = mysqli_prepare($bdd, "UPDATE dept_manager SET to_date = ? WHERE dept_no = ? AND to_date > CURDATE()");
        if ($stmt_update === false) {
            throw new Exception("Erreur de préparation (UPDATE dept_manager): " . mysqli_error($bdd));
        }
        mysqli_stmt_bind_param($stmt_update, "ss", $end_current_manager_date, $dept_no);
        mysqli_stmt_execute($stmt_update);

        if (mysqli_stmt_errno($stmt_update)) {
            throw new Exception("Erreur d'exécution (UPDATE dept_manager): " . mysqli_stmt_error($stmt_update));
        }
        mysqli_stmt_close($stmt_update);

        // 2. Insérer le nouvel enregistrement pour le rôle de manager
        $stmt_insert = mysqli_prepare($bdd, "INSERT INTO dept_manager (emp_no, dept_no, from_date, to_date) VALUES (?, ?, ?, '9999-01-01')");
        if ($stmt_insert === false) {
            throw new Exception("Erreur de préparation (INSERT dept_manager): " . mysqli_error($bdd));
        }
        mysqli_stmt_bind_param($stmt_insert, "iss", $emp_no, $dept_no, $from_date);
        mysqli_stmt_execute($stmt_insert);

        if (mysqli_stmt_errno($stmt_insert)) {
            throw new Exception("Erreur d'exécution (INSERT dept_manager): " . mysqli_stmt_error($stmt_insert));
        }
        mysqli_stmt_close($stmt_insert);

        mysqli_commit($bdd); // Valide la transaction
        return ['success' => true, 'message' => 'L\'employé est maintenant manager de ce département.'];

    } catch (Exception $e) {
        mysqli_rollback($bdd); // Annule la transaction en cas d'erreur
        return ['success' => false, 'message' => 'Erreur lors de la désignation du manager : ' . $e->getMessage()];
    }
}

?>