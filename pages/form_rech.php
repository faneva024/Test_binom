<?php
include('../inc/connexion.php');
include('../inc/fonction.php');
include('../inc/header.php');

$departements = get_departements($bdd);
?>

<main class="container">
    <h2 class="text-danger text-center my-4">Recherche avancée d'employés</h2>
    
    <form action="result_rech.php" method="get" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="dept" class="form-label">Département</label>
                <select class="form-select" id="dept" name="dept">
                    <option value="">Tous</option>
                    <?php while($dept = mysqli_fetch_assoc($departements)): ?>
                        <option value="<?= $dept['dept_no'] ?>">
                            <?= htmlspecialchars($dept['dept_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="nom" class="form-label">Nom employé</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom ou prénom">
            </div>
            
            <div class="col-md-2">
                <label for="age_min" class="form-label">Âge minimum</label>
                <input type="number" class="form-control" id="age_min" name="age_min" min="18" max="100">
            </div>
            
            <div class="col-md-2">
                <label for="age_max" class="form-label">Âge maximum</label>
                <input type="number" class="form-control" id="age_max" name="age_max" min="18" max="100">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Rechercher</button>
            </div>
        </div>
    </form>
</main>

<?php include('../inc/footer.php'); ?>
