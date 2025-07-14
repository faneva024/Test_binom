<?php
include('../inc/connexion.php');
include('../inc/fonction.php');
include('../inc/header.php');

$dept_no = isset($_GET['dept_no']) ? mysqli_real_escape_string($bdd, $_GET['dept_no']) : '';
$dept_name = isset($_GET['dept_name']) ? htmlspecialchars($_GET['dept_name']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($bdd, $_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

if (empty($dept_no)) {
    header("Location: index.php?error=no_dept_selected");
    exit();
}

$sql = "SELECT e.emp_no, e.first_name, e.last_name
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        WHERE de.dept_no = '$dept_no' AND de.to_date > NOW()";

if (!empty($search)) {
    $sql .= " AND (e.first_name LIKE '%$search%' OR e.last_name LIKE '%$search%')";
}

$sql_count = $sql;
$sql .= " LIMIT $limit OFFSET $offset";

$result_emp = mysqli_query($bdd, $sql);
$total_rows = mysqli_num_rows(mysqli_query($bdd, $sql_count));
?>

<main class="container">

    <header>
        <h2 class="text-danger text-center my-4">
            Employés du département : <?= $dept_name ?>
        </h2>
    </header>

    <section aria-label="Formulaire de recherche">
        <form method="get" class="row g-2 mb-4">
            <input type="hidden" name="dept_no" value="<?= htmlspecialchars($dept_no) ?>">
            <input type="hidden" name="dept_name" value="<?= htmlspecialchars($dept_name) ?>">

            <div class="col-md-4">
                <input type="text" class="form-control" name="search" 
                       value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Rechercher un nom ou prénom">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>
    </section>

    <?php if(mysqli_num_rows($result_emp) > 0): ?>
    <section aria-label="Liste des employés">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Fiche</th>
                </tr>
            </thead>
            <tbody>
                <?php while($emp = mysqli_fetch_assoc($result_emp)): ?>
                <article>
                    <tr>
                        <td><?= htmlspecialchars($emp['last_name']) ?></td>
                        <td><?= htmlspecialchars($emp['first_name']) ?></td>
                        <td>
                            <a href="fiche_employe.php?emp_no=<?= $emp['emp_no'] ?>" 
                               class="btn btn-warning btn-sm">
                                Détails
                            </a>
                        </td>
                    </tr>
                </article>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <nav aria-label="Pagination">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                    ← Précédent
                </a>
            </li>
            <?php endif; ?>

            <li class="page-item disabled">
                <span class="page-link">
                    Page <?= $page ?> (<?= $offset + 1 ?>–<?= min($offset + $limit, $total_rows) ?> sur <?= $total_rows ?>)
                </span>
            </li>

            <?php if ($offset + $limit < $total_rows): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                    Suivant →
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php else: ?>
    <section aria-label="Message d’alerte">
        <div class="alert alert-warning text-center">
            Aucun employé trouvé pour ce département<?= $search ? " avec ce filtre" : "" ?>.
        </div>
    </section>
    <?php endif; ?>
</main>

<?php include('../inc/footer.php'); ?>
