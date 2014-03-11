<?php
// Fara comentarii si spati intre linii, tot codul php are 15 linii. wow.
// Includerea bibliotecii si initierea clientului
include('RatingAPI.php');
$api = new RatingClient('296717969', 'syAVVaCAHcwXNYWuuOmDORbDp403hORKnsGjCiTjuqhioeNT29xR1IvqNvt5kkPI');

// Aplicam runda in API
if (isset($_GET['winner']))
    if ($_GET['winner'] == '0')
        $api->createBattle($_SESSION['id0'], $_SESSION['id1']);
    else
        $api->createBattle($_SESSION['id1'], $_SESSION['id0']);

// Cautam doi oponenti cu in ordinea random
$concurents = $api->listObjects(array('order' => 'random'));

// Pentru securitate, id-urile oponentilor le salvam in sesiune
$_SESSION['id0'] = $concurents[0]->id;
$_SESSION['id1'] = $concurents[1]->id;

// Lista openentilor in descrestere dupa scor. (Pentru sectiunea Top cele mai preferate universitati)
$top = $api->listObjects(array('order' => 'score DESC', 'limit' => 10));

// Ultimele participari al obiectului (Pentru Ultimele participari)
$battles[0] = $concurents[0]->battles(array('limit' => 5, 'order' => 'created_time DESC'));
$battles[1] = $concurents[1]->battles(array('limit' => 5, 'order' => 'created_time DESC'));

// Istoria ultimelor runde. (Pentru sectiunea Ultimele runde)
$history = $api->listBattles(array('limit' => 10, 'order' => 'created_time DESC'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Top Universitati</title>
        <style type="text/css">
            body, html {font-family:Arial, Helvetica, sans-serif;width:100%;margin:0;padding:0;}
            h1 {background-color:#600;color:#fff;padding:20px 0;margin:0;}
            td {font-size:11px;}
            .image {background-color:#eee;border:1px solid #ddd;border-bottom:1px solid #bbb;padding:5px;}
        </style>
        <script>
            if (location.href.match(/\?.*/) && document.referrer) {
                location.href = location.href.replace(/\?.*/, '');
            }
        </script>
    </head>
    <body>
        <center>
            <h1>Top Universitati</h1>
            <h3>Daca ai avea de alege, care ai alege?</h3>
            <h2>Care e mai tare ? Click pentru a vota.</h2>
            <table>
                <tr>
                    <?php foreach ($concurents as $concurent) : ?>
                        <td align="center"><h2><?= $concurent->name ?></h2></td>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach ($concurents as $key => $concurent) : ?>
                        <td valign="top" class="image"><a href="?winner=<?= $key ?>"><img src="<?= $concurent->image_url ?>" style="width:400px; height:300px;" /></a></td>
                        <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach ($concurents as $concurent) : ?>
                        <td>Cistiguri: <?= $concurent->wins ?>, Pierderi: <?= $concurent->losses ?></td>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach ($concurents as $concurent) : ?>
                        <td>Scor: <?= $concurent->score ?></td>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <td>Expected: <?= round(expected($concurents[1]->score, $concurents[0]->score), 4) ?></td>
                    <td>Expected: <?= round(expected($concurents[0]->score, $concurents[1]->score), 4) ?></td>
                </tr>
                <tr>
                    <?php for ($i = 0; $i < 2; $i++) : ?>
                        <td>
                            Ultimele batalii:
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <td>
                                            <b>cistigator</b>
                                        </td>
                                        <td>
                                            <b>ratat</b>
                                        </td>
                                    </tr>
                                </thead>
                                <?php foreach ($battles[$i] as $b) : ?>
                                    <tr>
                                        <td><?= $b->winner->name ?> [<?= $b->winner_score ?>]</td>
                                        <td><?= $b->loser->name ?> [<?= $b->loser_score ?>]</td>
                                    </tr>
                                <?php endforeach ?>
                            </table>
                        </td>
                    <?php endfor ?>
                </tr>
            </table>
            <h2>Top cele mai preferate universitati</h2>
            <table>
                <tr>
                    <?php foreach ($top as $key => $image) : ?>
                        <td valign="top"><img src="<?= $image->image_url ?>" width="70" /></td>
                    <?php endforeach ?>
                </tr>

                <tr>
                    <?php foreach ($top as $key => $image) : ?>
                        <td valign="top">Scor: <?= $image->score ?></td>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach ($top as $key => $image) : ?>
                        <td valign="top">Cistigari: <?= $image->wins ?></td>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach ($top as $key => $image) : ?>
                        <td valign="top">Pierderi: <?= $image->losses ?></td>
                    <?php endforeach ?>
                </tr>
            </table>
            <h2>Ultimele Batalii</h2>
            <table width="40%">
                <thead align="center">
                    <tr>
                        <td><b>winner</b></td>
                        <td><b>loser</b></td>
                    </tr>
                </thead>
                <tbody align="center">
                    <?php foreach ($history as $b) : ?>
                        <tr>
                            <td><img src="<?= $b->winner->image_url ?>" width="70" /></td>
                            <td><img src="<?= $b->loser->image_url ?>" width="70" /></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </center>
    </body>
</html>
