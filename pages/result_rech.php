<?php
include('../inc/connexion.php');
include('../inc/fonction.php');
include('../inc/header.php');

$params = [
    'dept' => $_GET['dept'] ?? '',
    'nom' => $_GET['nom'] ?? '',
    'age_min' => $_GET['age_min'] ?? '',
    'age_max' => $_GET['age_max'] ?? '',
    'page' => $_GET['page'] ?? 1,
    'limit' => 20
];

$data = search_employees($bdd, $params);
$result = $data['result'];
$total = $data['total'];

$page = max(1, intval($params['page']));
$limit = $params['limit'];
$offset = ($page - 1) * $limit;
?>

<main class="container">

    <header>
        <h2 class="text-danger text-center my-4">Résultats de recherche</h2>
    </header>

    <section class="mb-3">
        <a href="form_rech.php" class="btn btn-secondary">Nouvelle recherche</a>
    </section>

    <?php if(mysqli_num_rows($result) > 0): ?>
    <section class="table-responsive" aria-label="Résultats de la recherche">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Âge</th>
                    <th>Département</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <article>
                    <tr>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= $row['age'] ?></td>
                        <td><?= htmlspecialchars($row['dept_name'] ?? 'Non affecté') ?></td>
                    </tr>
                </article>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <nav aria-label="Pagination des résultats">
        <ul class="pagination justify-content-center">
            <?php if($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="result_rech.php?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">← Précédent</a>
            </li>
            <?php endif; ?>

            <li class="page-item disabled">
                <span class="page-link">
                    Page <?= $page ?> (<?= $offset + 1 ?>–<?= min($offset + $limit, $total) ?> sur <?= $total ?>)
                </span>
            </li>

            <?php if($offset + $limit < $total): ?>
            <li class="page-item">
                <a class="page-link" href="result_rech.php?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Suivant →</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php else: ?>
    <section>
        <div class="alert alert-warning">Aucun résultat trouvé.</div>
    </section>
    <?php endif; ?>

</main>

<?php include('../inc/footer.php'); ?>
