<?php

require __DIR__ . '/inc/functions.inc.php';
require __DIR__ . '/inc/db-connect.inc.php';

date_default_timezone_set('Asia/Kolkata');


$perPage = 2;
$page = (int) ($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

//$page = 1, $offset => 0
//$page = 2, $offset => $perPage
//$page = 3, $offset => $perpage * 2
$offset = ($page - 1) * $perPage;


$stmtCount = $pdo->prepare('SELECT COUNT(*) AS `count` FROM `entries`');
$stmtCount->execute();
$count = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];
// var_dump($count);

$numPages = ceil($count / $perPage);


$stmt = $pdo->prepare('SELECT * FROM `entries` ORDER BY `date` DESC, `id` DESC LIMIT :perPage OFFSET :offset');
$stmt->bindValue('perPage', (int) $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', (int) $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php require __DIR__ . '/views/header.view.php'; ?>

<h1 class="main-heading">Entries</h1>
<?php
foreach ($results as $result):
    $date = new DateTime($result['date'], new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
?>
    <div class="card">
        <?php if (!empty($result['image'])): ?>
            <div class="card__image-container">
                <img class="card__image" src="uploads/<?php echo e($result['image']); ?>" alt="">
            </div>
        <?php endif; ?>
        <div class="card__desc-container">
            <div class="card__desc-time"><?php echo htmlspecialchars($date->format('d-m-Y H:i:s')); ?></div>
            <h2 class="card__heading"><?php echo htmlspecialchars($result['title']); ?></h2>
            <p class="card__paragraph">
                <?php echo nl2br(htmlspecialchars($result['message'])); ?>
            </p>
        </div>
    </div>
<?php endforeach; ?>


<ul class="pagination">
    <?php if ($page > 1): ?>
        <li class="pagination__li">
            <a
                class="pagination__link"
                href="index.php?<?php echo http_build_query(['page' => $page - 1]); ?>">◄</a>
        </li>
    <?php endif; ?>
    <?php for ($x = 1; $x <= $numPages; $x++): ?>
        <li class="pagination__li">
            <a class="pagination__link" href="index.php?<?php echo http_build_query(['page' => $x]); ?>">
                <?php echo htmlspecialchars($x); ?>
            </a>
        </li>
    <?php endfor; ?>

    <li class="pagination__li">
        <?php if ($page < $numPages): ?>
            <a class="pagination__link" href="index.php?<?php echo http_build_query(['page' => $page + 1]); ?>">►</a>
        <?php endif; ?>
    </li>
</ul>
<?php require __DIR__ . '/views/footer.view.php'; ?>