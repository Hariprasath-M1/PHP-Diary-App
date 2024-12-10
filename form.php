<?php

require __DIR__ . '/inc/functions.inc.php';
require __DIR__ . '/inc/db-connect.inc.php';

//   var_dump($_POST);

if (!empty($_POST)) {
    // var_dump($_POST);
    $title = (string) ($_POST['title'] ?? '');
    $date = (string) ($_POST['date'] ?? '');
    $message = (string) ($_POST['message'] ?? '');
    $imageName = null;


    if (!empty($_FILES) && !empty($_FILES['image'])) {
        if ($_FILES['image']['error'] === 0 && $_FILES['image']['size'] !== 0) {
            $namewithoutextension = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
            $name = preg_replace('/[^a-zA-Z0-9]/', '', $namewithoutextension);

            $originalImage = $_FILES['image']['tmp_name'];
            $imageName = $name . '-' . time() . '.jpg';
            $destImage = __DIR__ . '/uploads/' . $imageName;

            $imageSize = getimagesize($originalImage);
            if (!empty($imageSize)) {
                [$width, $height] = $imageSize;

                $maxDim = 400;
                $scaleFactor = $maxDim / max($width, $height);

                $newWidth = intval($width * $scaleFactor);
                $newHeight = intval($height * $scaleFactor);


                $im = imagecreatefromjpeg($originalImage);
                if (!empty($im)) {
                    $newImg = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($newImg, $im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    imagejpeg($newImg, $destImage);
                }
            }
        }
    }

    $stmt = $pdo->prepare('INSERT INTO `entries` (`title`,`date`,`message`,`image`) VALUES (:title,:date,:message, :image)');
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':date', $date);
    $stmt->bindValue(':message', $message);
    $stmt->bindValue(':image', $imageName);
    $stmt->execute();

    echo '<a href="index.php">Continue to the diary</a>';
    die();
}

?>




<?php require __DIR__ . '/views/header.view.php'; ?>

<h1 class="main-heading">new entry</h1>

<form method="POST" action="form.php" enctype="multipart/form-data">
    <div class="form-group">
        <label class="form-group__label" for="title">Title:</label>
        <input class="form-group__input" type="text" id="title" name="title" required />
    </div>
    <div class="form-group">
        <label class="form-group__label" for="date">Date:</label>
        <input class="form-group__input" type="date" id="date" name="date" required />
    </div>
    <div class="form-group">
        <label class="form-group__label" for="image">Image:</label>
        <input class="form-group__input" type="file" id="image" name="image" />
    </div>
    <div class="form-group">
        <label class="form-group__label" for="message">Message:</label>
        <textarea class="form-group__input" name="message" id="message" rows="6" required></textarea>
    </div>
    <button class="button-end">
        <img src="./images/send-mail.png" class="button-end__img">
        Save !
    </button>
</form>
<?php require __DIR__ . '/views/footer.view.php'; ?>