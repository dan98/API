<?php
include('RatingAPI.php');
$api = new RatingClient('296717969', 'syAVVaCAHcwXNYWuuOmDORbDp403hORKnsGjCiTjuqhioeNT29xR1IvqNvt5kkPI');
if (isset($_POST['winner'])) {
    switch ($_POST['winner']) {
        case '0': $api->createBattle($_SESSION['id0'], $_SESSION['id1']);
            break;
        case '1': $api->createBattle($_SESSION['id1'], $_SESSION['id0']);
            break;
    }
}
$concurents = $api->listObjects(array('order' => 'random'));

$_SESSION['id0'] = $concurents[0]->id;
$_SESSION['id1'] = $concurents[1]->id;

$top = $api->listObjects(array('order' => 'score DESC', 'limit' => 10));

$battles = array();
$i = 0;
foreach ($concurents as $concurent) {
    $battles[$i] = $concurent->battles(array('limit' => 5, 'order' => 'created_time DESC'));
    $i++;
}

$history = $api->listBattles(array('limit' => 10, 'order' => 'created_time DESC'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Top Universitati</title>
        <style type="text/css">
            body, html {font-family:Arial, Helvetica, sans-serif;width:100%;margin:0;padding:0;text-align:center;}
            h1 {background-color:#600;color:#fff;padding:20px 0;margin:0;}
            a img {border:0;}
            td {font-size:11px;}
            .image {background-color:#eee;border:1px solid #ddd;border-bottom:1px solid #bbb;padding:5px;}
        </style>
        <script>
            document.onkeydown = function(evt) {
                evt = evt || window.event;
                if (evt.keyCode == 37) {
                    document.getElementById('winner0').submit();
                }
                if (evt.keyCode == 39) {
                    document.getElementById('winner1').submit();
                }
            };
        </script>
    </head>

    <body>
        <h1>Top Universitati</h1>
        <h3>Daca ai avea de alege, care ai alege?</h3>
        <h2>Care e mai tare ? Click pentru a vota.</h2>
        <form id="winner0" method="post"><input name="winner" value="0" type="hidden" /></form>
        <form id="winner1" method="post"><input name="winner" value="1" type="hidden" /></form>
        <center>
            <table>
                <tr>
                    <?php foreach ($concurents as $concurent) { ?>
                        <td align="center"><h2><?= $concurent->name ?></h2></td>
                    <?php } ?>
                </tr>
                <tr>
                    <?php foreach ($concurents as $key => $concurent) { ?>
                        <td valign="top" class="image"><a onclick="document.getElementById('winner<?= $key ?>').submit();"><img src="<?= $concurent->image_url ?>" style="width:400px; height:300px;" /></a></td>
                        <?php } ?>
                </tr>
                <tr>
                    <?php foreach ($concurents as $concurent) { ?>
                        <td>Cistiguri: <?= $concurent->wins ?>, Pierderi: <?= $concurent->losses ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <?php foreach ($concurents as $concurent) { ?>
                        <td>Scor: <?= $concurent->score ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td>Expected: <?= round(expected($concurents[1]->score, $concurents[0]->score), 4) ?></td>
                    <td>Expected: <?= round(expected($concurents[0]->score, $concurents[1]->score), 4) ?></td>
                </tr>
                <tr>
                    <?php for ($i = 0; $i < 2; $i++) { ?>
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
                                <?php
                                foreach ($battles[$i] as $b) {
                                    echo '<tr>';
                                    echo '<td>' . $b->winner->name . ' [' . $b->winner_score . ']</td><td>' . $b->loser->name . ' [' . $b->loser_score . ']</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </table>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </center>

        <h2>Top cele mai preferate universitati</h2>
        <center>
            <table>
                <tr>
                    <?php foreach($top as $key => $image) : ?>
                    <td valign="top"><img src="<?= $image->image_url ?>" width="70" /></td>
                    <?php endforeach ?>
                </tr>

                <tr>
                    <?php foreach($top as $key => $image) : ?>
                    <td valign="top">Scor: <?= $image->score ?></td>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach($top as $key => $image) : ?>
                    <td valign="top">Cistigari: <?= $image->wins ?></td>
                    <?php endforeach ?>
                </tr>
                <tr>
                    <?php foreach($top as $key => $image) : ?>
                    <td valign="top">Pierderi: <?= $image->losses ?></td>
                    <?php endforeach ?>
                </tr>
            </table>
        </center>
        <h2>Ultimele Batalii</h2>
        <center>
            <table width="40%">
                <thead align="center">
                    <tr>
                        <td>
                            <b></b>
                        </td>
                        <td>
                            <b>-----</b>
                        </td>
                    </tr>
                </thead>
                <tbody align="center">
                    <?php
                    foreach ($history as $b) {
                        echo '<tr>';
                        echo '<td valign="middle"><img src="' . $b->winner->image_url . '" width="70" /></td><td><img src="' . $b->loser->image_url . '" width="70" /></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </center>
    </body>
</html>
