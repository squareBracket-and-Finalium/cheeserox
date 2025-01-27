<?php

namespace OpenSB;

global $auth, $database;

if (!isset($_POST['vidid'])) {
    die("No POST data.");
} else if (!isset($_POST['rating']) or $_POST['rating'] == '') {
    die(); //don't output anything if there is no data.
}
if ($database->result("SELECT COUNT(rating) FROM upload_ratings WHERE video=? AND user=?", [$database->result("SELECT id FROM uploads WHERE video_id=?", [$_POST['vidid']]), $auth->getUserID()]) != 0) {
    $database->query("DELETE FROM upload_ratings WHERE user=? AND video=?",
        [$auth->getUserID(), $database->result("SELECT id FROM uploads WHERE video_id=?", [$_POST['vidid']])]);
    echo 0;
} else {
    $database->query("INSERT INTO upload_ratings (user, video, rating) VALUES (?,?,?)",
        [$auth->getUserID(), $database->result("SELECT id FROM uploads WHERE video_id=?", [$_POST['vidid']]), $_POST['rating']]);
    echo 1;
}
