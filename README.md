# Application de collecte d'articles

Cette application permet de collecter des articles à partir de deux sources différentes : une base de données MySQL et des flux RSS. Les articles collectés sont stockés dans un tableau et peuvent être parcourus à l'aide d'une boucle `foreach`.

## Installation

1.  Assurez-vous que PHP 7.4 ou supérieur est installé sur votre système.
2.  Clonez ce dépôt ou téléchargez-le sous forme d'archive.
3.  Copiez le fichier `.env.example` en `.env` et modifiez les valeurs des variables en fonction de votre configuration.
4.  Exécutez `composer install` pour installer les dépendances.

## Utilisation

1.  Créez une instance de la classe `ArticleAggregator` : `$aggregator = new ArticleAggregator();`.
2.  Ajoutez des articles à partir de la base de données : `$aggregator->appendDatabase($sourceId);`.
3.  Ajoutez des articles à partir de flux RSS : `$aggregator->appendRss($sourceName, $feedUrl);`.
4.  Parcourez les articles avec une boucle `foreach` :

phpCopy code

`foreach ($aggregator as $article) {
    echo $article->name . "\n";
    echo $article->content . "\n";
    echo $article->sourceName . "\n";
}` 

## Configuration

Les variables de configuration sont stockées dans le fichier `.env`. Voici les variables disponibles :

-   `DB_HOST` : l'hôte de la base de données MySQL (par défaut : `localhost`).
-   `DB_NAME` : le nom de la base de données MySQL.
-   `DB_USER` : le nom d'utilisateur de la base de données MySQL.
-   `DB_PASSWORD` : le mot de passe de la base de données MySQL.
-   `RSS_FEEDS` : une liste de flux RSS sous forme de chaînes de caractères séparées par des virgules. Chaque chaîne doit être de la forme `nom-de-la-source=url-du-flux`.