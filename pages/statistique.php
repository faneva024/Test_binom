<?php
include('../inc/connexion.php');
include('../inc/fonction.php');
include('../inc/header.php');

$stats = get_statistiques_emplois($bdd);
?>

<main class="container">

    <header>
        <h2 class="text-center text-danger my-4">Statistiques des emplois</h2>
    </header>

    <?php if(mysqli_num_rows($stats) > 0): ?>
    <section class="table-responsive" aria-label="Tableau des statistiques">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Emploi</th>
                    <th>Hommes</th>
                    <th>Femmes</th>
                    <th>Salaire moyen (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($stats)): ?>
                <article>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= $row['hommes'] ?></td>
                        <td><?= $row['femmes'] ?></td>
                        <td><?= number_format($row['salaire_moyen'], 2, ',', ' ') ?></td>
                    </tr>
                </article>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <?php else: ?>
    <section aria-label="Message d’alerte">
        <div class="alert alert-warning text-center">Aucune statistique trouvée.</div>
    </section>
    <?php endif; ?>

    <footer class="text-center mt-4">
        <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
    </footer>

</main>

<?php include('../inc/footer.php'); ?>
