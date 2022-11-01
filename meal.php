<?php
const GET_PARAM_MIN_STARS = 'search_min_stars';
const GET_PARAM_SEARCH_TEXT = 'search_text';
const SHOW_DESCRIPTION = 'show_description';
const LANGUAGE = 'sprache';

$translation = [
    'Bewertung' => 'Rating',
    'Name' => 'Name',
    'Begründung' => 'Reason',
    'Senden' => 'send'
];

/**
 * List of all allergens.
 */
$allergens = [
    11 => 'Gluten',
    12 => 'Krebstiere',
    13 => 'Eier',
    14 => 'Fisch',
    17 => 'Milch'
];

$meal = [
    'name' => 'Süßkartoffeltaschen mit Frischkäse und Kräutern gefüllt',
    'description' => 'Die Süßkartoffeln werden vorsichtig aufgeschnitten und der Frischkäse eingefüllt.',
    'price_intern' => 2.90,
    'price_extern' => 3.90,
    'allergens' => [11, 13],
    'amount' => 42             // Number of available meals
];

$ratings = [
    ['text' => 'Die Kartoffel ist einfach klasse. Nur die Fischstäbchen schmecken nach Käse. ',
        'author' => 'Ute U.',
        'stars' => 2],
    ['text' => 'Sehr gut. Immer wieder gerne',
        'author' => 'Gustav G.',
        'stars' => 4],
    ['text' => 'Der Klassiker für den Wochenstart. Frisch wie immer',
        'author' => 'Renate R.',
        'stars' => 4],
    ['text' => 'Kartoffel ist gut. Das Grüne ist mir suspekt.',
        'author' => 'Marta M.',
        'stars' => 3]
];

$showRatings = [];
if (!empty($_GET[GET_PARAM_SEARCH_TEXT])) {
    $searchTerm = $_GET[GET_PARAM_SEARCH_TEXT];
    foreach ($ratings as $rating) {
        if (strripos($rating['text'], $searchTerm) !== false) { //strpos wird zu strripos. Findet letztes statt erstes Vorkommen von ZK aber klappt trotzdem
            $showRatings[] = $rating;
        }
    }
} else if (!empty($_GET[GET_PARAM_MIN_STARS])) {
    $minStars = $_GET[GET_PARAM_MIN_STARS];
    foreach ($ratings as $rating) {
        if ($rating['stars'] >= $minStars) {
            $showRatings[] = $rating;
        }
    }
} else {
    $showRatings = $ratings;
}

function calcMeanStars(array $ratings): float
{
    $sum = 0; // Startet bei 0 nicht bei 1
    foreach ($ratings as $rating) {
        $sum += $rating['stars'];
    }

    return $sum / count($ratings); // Division kommt erst zum Schluss
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <title>Gericht: <?php echo $meal['name']; ?></title>
    <style>
        * {
            font-family: Arial, serif;
        }

        .rating {
            color: darkgray;
        }

        #allergene li {
            font-size: 12px;
        }


    </style>
</head>
<body>
<form method="get">
    <label>Sprache:
        <select name="sprache" >
            <option value="Deutsch">Deutsch</option>
            <option value="English">English</option>
        </select>
        <input type="submit" name="input_language">
    </label>
</form>
<h1>Gericht: <?php echo $meal['name']; ?></h1>

<form method="get">
    <label class="switch"> Beschreibung
        <input type="submit" name="show_description" value="1">
        <input type="submit" name="show_description" value="0">
    </label>
</form>

<?php
//$_GET[SHOW_DESCRIPTION] = 1; //Hier 1 zu 0 machen, um gleichen Effekt wie unten zu erzielen
if ($_GET[SHOW_DESCRIPTION] == 1)
    echo '<p>' . $meal['description'] . '</p>';
?><!-- http://localhost:9000/beispiele/meal.php?show_description=1 -->
<p>
<table class="price">
    <thead>
    <tr>
        <td>Preis Intern</td>
        <td>Preis Extern</td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <!--number_format(float,dezimalstellen,dezimalsperator(string),tausendseperator(string))-->
        <td><?php echo number_format($meal['price_intern'], 2, ',') . '€' ?></td>
        <td><?php echo number_format($meal['price_extern'], 2, ',') . '€' ?></td>
    </tr>
    </tbody>
</table>
</p>
<h1><?php if ($_GET[LANGUAGE] == "English")
        echo $translation['Bewertung'];
    else
        echo 'Bewertung' ?> (Insgesamt: <?php echo calcMeanStars($ratings); ?>)
    <form method="get">
        <label class="TOP FLOPP">
            <input type="submit" name="TOP" value="TOP">
            <input type="submit" name="FLOPP" value="FLOPP">    <!--WIP-->
        </label>
    </form>
</h1>
<form method="get">
    <label for="search_text">Filter:</label>
    <input id="search_text" type="text" name="search_text" value="<?php echo $_GET['search_text'] ?? '' ?>">
    <!--?? wie isset. Gibt hier search_text zurück, falls dieser gesetzt ist, sonst ''-->
    <input type="submit" value="Suchen">
</form>
<table class="rating">
    <thead>
    <tr>
        <td>Text</td>
        <td>Sterne</td>
        <td>Autoren</td>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($showRatings as $rating) {
        echo "<tr><td class='rating_text'>{$rating['text']}</td>
                      <td class='rating_stars'>{$rating['stars']}</td>
                      <td class='autor_text'>{$rating['author']}</td>
                  </tr>";
    }
    ?>
    </tbody>
</table>
<div id="allergene"><p>Allergene</p>
    <ul>
        <?php

        foreach ($allergens as $row => $value) {
            foreach ($meal['allergens'] as $allergenArr => $allergenNum) {
                if ($row == $allergenNum)
                    echo '<li>' . $row . ', ' . $value . '</li>';
            }
        }
        ?>
    </ul>
</div>


</body>
</html>