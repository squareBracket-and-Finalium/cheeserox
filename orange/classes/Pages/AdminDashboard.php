<?php

namespace Orange\Pages;

use Orange\MiscFunctions;
use Orange\User;
use Orange\OrangeException;
use Orange\Database;

/**
 * Backend code for the admin dashboard.
 *
 * @since 0.1.0
 */
class AdminDashboard
{
    private \Orange\Database $database;
    private $data;

    public function __construct(\Orange\Orange $betty)
    {
        global $auth;

        $this->database = $betty->getBettyDatabase();
        if (!$auth->isUserAdmin()) {
            $betty->Notification("You do not have permission to access this page", "/");
        }

        $thingsToCount = ['comments', 'channel_comments', 'users', 'videos', 'views', 'favorites', 'bans', 'journals'];
        $query = "SELECT ";
        foreach ($thingsToCount as $thing) {
            if ($query != "SELECT ") $query .= ", ";
            $query .= sprintf("(SELECT COUNT(*) FROM %s) %s", $thing, $thing);
        }

        $this->data = [
            "numbers" => $this->database->fetch($query),
            "system" => [
                "uname" => php_uname(),
            ],
            "graph_data" => [
                "users" => $this->getUserGraph(),
                "submissions" => $this->getVideoGraph(),
                "comments" => $this->getCommentGraph(),
                "shouts" => $this->getShoutsGraph(),
                "journals" => $this->getJournalGraph(),
            ],
        ];
    }

    public function getData()
    {
        return $this->data;
    }

    private function getVideoGraph(): array
    {
        $this->database->query("SET @runningTotal = 0;");
        $videoData = $this->database->query("
SELECT 
    time,
    num_interactions,
    @runningTotal := @runningTotal + totals.num_interactions AS runningTotal
FROM
(SELECT 
    FROM_UNIXTIME(time) AS time,
    COUNT(*) AS num_interactions
FROM videos AS e
GROUP BY DATE(FROM_UNIXTIME(e.time))) totals
ORDER BY time;");
        $videos = $this->database->fetchArray($videoData);
        return $videos;
    }

    private function getCommentGraph(): array
    {
        $this->database->query("SET @runningTotal = 0;");
        $videoData = $this->database->query("
SELECT 
    date,
    num_interactions,
    @runningTotal := @runningTotal + totals.num_interactions AS runningTotal
FROM
(SELECT 
    FROM_UNIXTIME(date) AS date,
    COUNT(*) AS num_interactions
FROM comments AS e
GROUP BY DATE(FROM_UNIXTIME(e.date))) totals
ORDER BY date;");
        $videos = $this->database->fetchArray($videoData);
        return $videos;
    }

    private function getShoutsGraph(): array
    {
        $this->database->query("SET @runningTotal = 0;");
        $videoData = $this->database->query("
SELECT 
    date,
    num_interactions,
    @runningTotal := @runningTotal + totals.num_interactions AS runningTotal
FROM
(SELECT 
    FROM_UNIXTIME(date) AS date,
    COUNT(*) AS num_interactions
FROM channel_comments AS e
GROUP BY DATE(FROM_UNIXTIME(e.date))) totals
ORDER BY date;");
        $videos = $this->database->fetchArray($videoData);
        return $videos;
    }

    private function getUserGraph(): array
    {
        $this->database->query("SET @runningTotal = 0;");
        $userData = $this->database->query("
SELECT 
    joined,
    num_interactions,
    @runningTotal := @runningTotal + totals.num_interactions AS runningTotal
FROM
(SELECT 
    FROM_UNIXTIME(joined) AS joined,
    COUNT(*) AS num_interactions
FROM users AS e
GROUP BY DATE(FROM_UNIXTIME(e.joined))) totals
ORDER BY joined;");
        $users = $this->database->fetchArray($userData);
        return $users;
    }

    private function getJournalGraph(): array
    {
        $this->database->query("SET @runningTotal = 0;");
        $videoData = $this->database->query("
SELECT 
    date,
    num_interactions,
    @runningTotal := @runningTotal + totals.num_interactions AS runningTotal
FROM
(SELECT 
    FROM_UNIXTIME(date) AS date,
    COUNT(*) AS num_interactions
FROM journals AS e
GROUP BY DATE(FROM_UNIXTIME(e.date))) totals
ORDER BY date;");
        $videos = $this->database->fetchArray($videoData);
        return $videos;
    }

}