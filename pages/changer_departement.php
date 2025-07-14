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

$current_department_info = get_current_departement($bdd, $emp_no);
$departements_disponibles = get_departements_except_current($bdd, $current_department_info['dept_no'] ?? null);
?>

<main class="container">

    <header>
        <h2 class="text-danger text-center my-4">
            Changer de département pour <?= htmlspecialchars($employe['first_name']) ?> <?= htmlspecialchars($employe['last_name']) ?>
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

    <section aria-label="Département actuel">
        <article class="card mb-4">
            <header class="card-header bg-info text-white">
                <h5 class="mb-0">Département actuel</h5>
            </header>
            <div class="card-body">
                <?php if ($current_department_info): ?>
                    <p class="mb-0">
                        <strong>Département :</strong> <?= htmlspecialchars($current_department_info['dept_name']) ?><br>
                        <strong>Date de début :</strong> <?= htmlspecialchars($current_department_info['from_date']) ?>
                    </p>
                <?php else: ?>
                    <p class="mb-0">L'employé n'est actuellement affecté à aucun département.</p>
                <?php endif; ?>
            </div>
        </article>
    </section>

    <section aria-label="Formulaire de changement">
        <form action="process_change_dept.php" method="post">
            <input type="hidden" name="emp_no" value="<?= htmlspecialchars($emp_no) ?>">
            <input type="hidden" name="current_dept_from_date" value="<?= htmlspecialchars($current_department_info['from_date'] ?? '') ?>">
            <input type="hidden" name="current_dept_no" value="<?= htmlspecialchars($current_department_info['dept_no'] ?? '') ?>">

            <div class="mb-3">
                <label for="new_dept_no" class="form-label">Nouveau département</label>
                <select class="form-select" id="new_dept_no" name="new_dept_no" required>
                    <option value="">-- Sélectionnez un département --</option>
                    <?php while($dept = mysqli_fetch_assoc($departements_disponibles)): ?>
                        <option value="<?= htmlspecialchars($dept['dept_no']) ?>">
                            <?= htmlspecialchars($dept['dept_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="from_date" class="form-label">Date de début du nouveau département</label>
                <input type="date" class="form-control" id="from_date" name="from_date" required>
            </div>

            <footer class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Changer de département</button>
                <a href="fiche_employe.php?emp_no=<?= htmlspecialchars($emp_no) ?>" class="btn btn-secondary">Annuler</a>
            </footer>
        </form>
    </section>

</main>

<?php include('../inc/footer.php'); ?>
