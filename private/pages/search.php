<?php

namespace Orange;

global $orange;

use Orange\OrangeException;
use Orange\Templating;

require_once dirname(__DIR__) . '/class/common.php';

require_once dirname(__DIR__) . '/class/Pages/SubmissionSearch.php';

$query = $_GET['query'] ?? null;
$type = ($_GET['type'] ?? 'recent');
$page_number = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1);

try {
    $page = new \Orange\Pages\SubmissionSearch($orange, $type, $page_number, $query);
    $data = $page->getData();
} catch (OrangeException $e) {
    $e->page();
}

$twig = new Templating($orange);

echo $twig->render('browse.twig', [
    'data' => $data,
    'page' => $page_number,
]);