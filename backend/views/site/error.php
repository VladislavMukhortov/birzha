<?php
$this->title = h($name);
?>
<section class="py-5 bg-light error">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 m-auto">

                <h1><?php echo h($this->title) ?></h1>

                <div class="alert alert-danger">
                    <?php echo nl2br(h($message)) ?>
                </div>

                <p>The above error occurred while the Web server was processing your request.</p>
                <p>Please contact us if you think this is a server error. Thank you.</p>

            </div>
        </div>
    </div>
</section>
