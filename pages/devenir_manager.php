<?php
include('../inc/connexion.php');
include('../inc/fonction.php');
include('../inc/header.php');

if (!isset($_GET['emp_no']) || !is_numeric($_GET['emp_no'])) {
    header("Location: index.php?error=" . urlencode("Employé non spécifié ou invalide."));
    exit();
}

$emp_no = $_GET['emp_no'];
$employee_data = get_employee_details($bdd, $emp_no);
$employe = $employee_data['info'];

if (!$employe) {
    header("Location: index.php?error=" . urlencode("Employé introuvable."));
    exit();
}

$current_dept_info = get_current_departement($bdd, $emp_no);
if (!$current_dept_info) {
    header("Location: fiche_employe.php?emp_no=$emp_no&error=" . urlencode("L'employé doit être affecté à un département pour devenir manager."));
    exit();
}

$dept_no = $current_dept_info['dept_no'];
$dept_name = $current_dept_info['dept_name'];

$current_manager_info = get_current_manager($bdd, $dept_no);
?>

<main class="container">

    <header>
        <h2 class="text-danger text-center my-4">
            Désigner <?= htmlspecialchars($employe['first_name']) ?> <?= htmlspecialchars($employe['last_name']) ?> comme manager
        </h2>
    </header>

    <section aria-label="Messages d'état">
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
    </section>

    <section aria-label="Département et manager actuel">
        <article class="card mb-4">
            <header class="card-header bg-info text-white">
                <h5 class="mb-0">Département et Manager actuel</h5>
            </header>
            <div class="card-body">
                <p><strong>Département concerné :</strong> <?= htmlspecialchars($dept_name) ?></p>
                <?php if ($current_manager_info): ?>
                    <p>
                        <strong>Manager actuel :</strong> <?= htmlspecialchars($current_manager_info['first_name']) ?> <?= htmlspecialchars($current_manager_info['last_name']) ?><br>
                        <strong>Date de début du manager actuel :</strong> <?= htmlspecialchars($current_manager_info['from_date']) ?>
                    </p>
                <?php else: ?>
                    <p>Ce département n'a pas de manager actuel.</p>
                <?php endif; ?>
            </div>
        </article>
    </section>

    <section aria-label="Formulaire de désignation">
        <form action="process_become_manager.php" method="post">
            <input type="hidden" name="emp_no" value="<?= htmlspecialchars($emp_no) ?>">
            <input type="hidden" name="dept_no" value="<?= htmlspecialchars($dept_no) ?>">
            <input type="hidden" name="current_manager_from_date" value="<?= htmlspecialchars($current_manager_info['from_date'] ?? '') ?>">

            <div class="mb-3">
                <label for="from_date" class="form-label">Date de début du rôle de manager</label>
                <input type="date" class="form-control" id="from_date" name="from_date" required>
            </div>

            <footer class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Désigner comme Manager</button>
                <a href="fiche_employe.php?emp_no=<?= htmlspecialchars($emp_no) ?>" class="btn btn-secondary">Annuler</a>
            </footer>
        </form>
    </section>

</main>

<?php include('../inc/footer.php'); ?>
