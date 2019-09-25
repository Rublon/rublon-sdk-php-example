<?php
if (!empty($_SESSION['flashMsg']) && !empty($_SESSION['flashMsg']['text'])) {
    $flashMsg = $_SESSION['flashMsg'];
    ?>
    <div class="alert <?php echo $flashMsg['type'] ?> mt-3 flashMsg" role="alert">
        <?php
        echo $flashMsg['text'];
        ?>
    </div>
    <?php
    unset($_SESSION['flashMsg']);
}