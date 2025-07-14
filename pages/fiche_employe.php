<?php
include('../inc/connexion.php');
include('../inc/fonction.php');
include('../inc/header.php');

if (isset($_GET['emp_no'])) {
    $emp_no = $_GET['emp_no'];
    $employee_data = get_employee_details($bdd, $emp_no);
    $employe = $employee_data['info'];
}
?>

<main class="container">
    <?php if (isset($employe)): ?>

        <header>
            <h2 class="text-danger text-center my-4">
                Fiche de <?= htmlspecialchars($employe['first_name']) ?> <?= htmlspecialchars($employe['last_name']) ?>
            </h2>
        </header>

        <section class="row">
            <article class="col-md-6">
                <div class="card border-warning mb-4">
                    <header class="card-header bg-warning">
                        <h4>Informations personnelles</h4>
                    </header>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>ID :</strong> <?= $employe['emp_no'] ?></li>
                            <li class="list-group-item"><strong>Date de naissance :</strong> <?= $employe['birth_date'] ?></li>
                            <li class="list-group-item"><strong>Genre :</strong> <?= $employe['gender'] ?></li>
                            <li class="list-group-item"><strong>Date d'embauche :</strong> <?= $employe['hire_date'] ?></li>
                            <li class="list-group-item"><strong>Salaire actuel :</strong>
                                <?= isset($employee_data['current_salary']['salary']) ?
                                    number_format($employee_data['current_salary']['salary'], 2, ',', ' ') . ' €' : 'N/A' ?>
                            </li>
                            <li class="list-group-item"><strong>Poste actuel :</strong>
                                <?= isset($employee_data['current_title']['title']) ?
                                    htmlspecialchars($employee_data['current_title']['title']) : 'N/A' ?>
                            </li>
                            <?php
                            $poste_plus_long = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT title, duree FROM vue_poste_plus_long WHERE emp_no = '$emp_no' ORDER BY duree DESC LIMIT 1"));
                            ?>
                            <li class="list-group-item"><strong>Poste le plus long :</strong>
                                <?= $poste_plus_long['title'] ?? 'N/A' ?>
                            </li>
                        </ul>
                        <div class="mt-3 d-flex gap-2">
                            <a href="changer_departement.php?emp_no=<?= $emp_no ?>" class="btn btn-outline-primary">Changer de département</a>
                            <a href="devenir_manager.php?emp_no=<?= $emp_no ?>" class="btn btn-outline-success">Devenir Manager</a>
                        </div>
                    </div>
                </div>
            </article>

            <article class="col-md-6">
                <section class="card border-danger mb-4">
                    <header class="card-header bg-danger text-white">
                        <h4>Historique des salaires</h4>
                    </header>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Période</th><th>Salaire</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($salaire = mysqli_fetch_assoc($employee_data['salaries'])): ?>
                                    <tr>
                                        <td>
                                            <?= date('Y', strtotime($salaire['from_date'])) ?> –
                                            <?= date('Y', strtotime($salaire['to_date'])) ?>
                                        </td>
                                        <td><?= number_format($salaire['salary'], 2, ',', ' ') ?> €</td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card border-primary">
                    <header class="card-header bg-primary text-white">
                        <h4>Historique des postes</h4>
                    </header>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Période</th><th>Poste</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($poste = mysqli_fetch_assoc($employee_data['titles'])): ?>
                                    <tr>
                                        <td>
                                            <?= date('Y', strtotime($poste['from_date'])) ?> –
                                            <?= date('Y', strtotime($poste['to_date'])) ?>
                                        </td>
                                        <td><?= htmlspecialchars($poste['title']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </article>
        </section>

    <?php else: ?>
        <section class="alert alert-danger">Employé non trouvé</section>
    <?php endif; ?>

    <footer class="text-center mt-3">
        <a href="javascript:history.back()" class="btn btn-dark">Retour</a>
    </footer>
</main>

<?php include('../inc/footer.php'); ?>
