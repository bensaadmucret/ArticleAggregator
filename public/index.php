<?php

use Alltricks\ArticleAggregator;

require __DIR__ . '/../vendor/autoload.php';



// Initialisation de la classe ArticleAggregato
$aggregator = new ArticleAggregator();


// Ajout des sources RSS à l'agrégateur
$aggregator->appendRss('Le Monde', 'https://www.lemonde.fr/rss/une.xml');
$aggregator->appendRss('Le Figaro', 'http://www.lefigaro.fr/rss/figaro_actualites.xml');

// récupération des articles
$aggregator->getAllActicles();


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- style -->
    <link rel="stylesheet" href="ressources/style.css">
    <title>ArticleAggregator</title>
</head>

<body>
    <main>
        <section>
        <h1 class="mt-5 p-1">Derniers articles</h1>
           <div class="container">
            <?php foreach ($aggregator as $article): ?>
            <div class="card">
                <div class="card-header"> <?php echo '<h2>' . $article->name . '</h2>'; ?></div>
                <div class="card-body">
                    <?php echo '<p>' . $article->content . '</p>'; ?>
                </div>
                <div class="card-footer"><?php echo '<p><em>Source: ' . ($article->source_id ??  'N/A') . '</em></p>'; ?></div>
            </div>
            <?php endforeach ?>
            </div> 
        </section>
    </main>

    <footer>
    
    </footer>
</body>

</html>