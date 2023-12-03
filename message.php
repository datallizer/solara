<?php
    if(isset($_SESSION['message'])) :
?>
    <div style="width: 100%;" class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['message']; ?> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php 
    unset($_SESSION['message']);
    endif;
?>