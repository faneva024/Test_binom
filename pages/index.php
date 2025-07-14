<?php
include('../inc/connexion.php');
include('../inc/fonction.php');
include('../inc/header.php');

$departements = liste_dept($bdd);
?>

<main class="container">
    <header>
        <h2 class="text-danger text-center my-4">Liste des départements</h2>
    </header>

    <section aria-label="Recherche des employés">
        <form action="result_rech.php" method="get" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="dept" class="form-label">Département</label>
                    <select class="form-select" id="dept" name="dept">
                        <option value="">Tous</option>
                        <?php
                        $res_depts = mysqli_query($bdd, "SELECT dept_no, dept_name FROM departments");
                        while($dept = mysqli_fetch_assoc($res_depts)):
                        ?>
                            <option value="<?= $dept['dept_no'] ?>"><?= htmlspecialchars($dept['dept_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control">
                </div>

                <div class="col-md-2">
                    <label for="age_min" class="form-label">Âge min</label>
                    <input type="number" name="age_min" class="form-control" min="18" max="100">
                </div>

                <div class="col-md-2">
                    <label for="age_max" class="form-label">Âge max</label>
                    <input type="number" name="age_max" class="form-control" min="18" max="100">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                </div>
            </div>
        </form>
    </section>

    <?php if(isset($_GET['error'])): ?>
    <section>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    </section>
    <?php endif; ?>

    <section aria-label="Liste des départements">
        <table class="table table-striped" border="2">
            <thead>
                <tr>
                    <th class="bg-warning">Nom du département</th>
                    <th class="bg-warning">Manager</th>
                    <th class="bg-warning">Nombre d’employés</th>
                    <th class="bg-warning">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($dept = mysqli_fetch_assoc($departements)): ?>
                <article>
                    <tr>
                        <td><?= htmlspecialchars($dept['dept_name']) ?></td>
                        <td><?= htmlspecialchars($dept['manager_name']) ?></td>
                        <td><?= $dept['nb_employes'] ?></td>
                        <td>
                            <form action="traitement.php" method="post">
                                <input type="hidden" name="dept_no" value="<?= $dept['dept_no'] ?>">
                                <button type="submit" class="btn btn-dark btn-sm">Voir employés</button>
                            </form>
                        </td>
                    </tr>
                </article>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section aria-label="Statistiques globales">
        <div class="text-end mb-3">
            <a href="statistique.php" class="btn btn-info">Voir les statistiques globales</a>
        </div>
    </section>
</main>

<?php include('../inc/footer.php'); ?>
