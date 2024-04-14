<?php

use MongoDB\BSON\ObjectId;

require_once 'class/myDbClass.php';

$mdb = new myDbClass();
$client = $mdb->getClient();
$id = GETPOST('id');

if ($id == '') {
    echo '<div class="dtitle w3-container w3-teal"><h2>Cet élément n\'a pas été trouvé</h2></div>';
} else {
    $obj_id = new ObjectId($id);
    $movies_collection = $mdb->getCollection('movies');
    $document = $movies_collection->findOne(['_id' => $obj_id]);

    $confirm = GETPOST('confirm_envoyer');
    if ($confirm == 'Envoyer') {
        $title = GETPOST('title');
        $year = GETPOST('year');
        $production = array_map('trim', explode(PHP_EOL, GETPOST('production')));
        $actors = array_map('trim', explode(PHP_EOL, GETPOST('actors')));
        $synopsis = GETPOST('synopsis');

        $updateResult = $movies_collection->updateOne(
            ['_id' => $obj_id],
            ['$set' => [
                'title' => $title,
                'year' => (int) $year,
                'production' => $production,
                'actors' => $actors,
                'synopsis' => $synopsis
            ]]
        );

        if ($updateResult->getModifiedCount() == 1) {
            header('Location: index.php?action=list');
            exit;
        } else {
            echo '<p>Erreur lors de la mise à jour de l\'élément.</p>';
        }
    }
?>
    <div class="dtitle w3-container w3-teal">
        <h2>Modification d'un element</h2>
    </div>
    <form class="w3-container" action="index.php?action=edit" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="dcontent">
            <div class="w3-row-padding">
                <div class="w3-half">
                    <label class="w3-text-blue" for="title"><b>Titre</b></label>
                    <input class="w3-input w3-border" type="text" id="title" name="title" value="<?php echo $elt['title']; ?>" />
                </div>
                <div class="w3-half">
                    <label class="w3-text-blue" for="year"><b>Année de sortie</b></label><br />
                    <input type="text" id="year" name="year" value="<?php echo $elt['year']; ?>" />
                </div>
            </div>
            <div class="w3-row-padding">
                <div class="w3-half">
                    <label class="w3-text-blue" for="actors"><b>Acteurs Principaux</b></label>
                    <textarea class="w3-input w3-border" rows=6 id="actors" name="actors"><?php echo implode(PHP_EOL, $elt['actors']); ?></textarea>
                </div>
                <div class="w3-half">
                    <label class="w3-text-blue" for="production"><b>Producteurs</b></label>
                    <textarea class="w3-input w3-border" rows=3 id="production" name="production"><?php echo implode(PHP_EOL, $elt['production']); ?></textarea>
                </div>
            </div>
            <div class="w3-row-padding">
                <div class="w3-full">
                    <label class="w3-text-blue" for="synopsis"><b>Synopsis</b></label>
                    <textarea class="w3-input w3-border" rows=10 id="synopsis" name="synopsis"><?php echo nl2br($elt['synopsis']); ?></textarea>
                </div>
            </div>
            <br />
            <div class="w3-row-padding">
                <div class="w3-half">
                    <input class="w3-btn w3-red" type="submit" name="cancel" value="Annuler" />
                </div>
                <div class="w3-half">
                    <input class="w3-btn w3-blue-grey" type="submit" name="confirm_envoyer" value="Envoyer" />
                </div>
            </div>
            <br /><br />
    </form>
    </div>
    <div class="dfooter">
    </div>
<?php
}
