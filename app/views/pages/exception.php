<?php require APPROOT .'/views/inc/header.php'; ?>
<!-- Jumbotron -->
<div class="container">    
    <div id="pages-about-jumbotron" class="jumbotron text-center py-4 mb-3">
        <h1 class="text-white jumbotron-text-shadow">Oooooo!</h1>
    </div>
</div>
<div class="container pt-2 pb-5 mb-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-body" style="text-align: center;">
                    <h2 class="card-title text-dark">Unexpected Exception</h2>
                    <div class="card-text text-dark">
                        <p><strong><?php echo $data['ex']->getMessage() ?></strong></p>
                        <p style="font-size: small;">

                            <?php // echo $data['ex']->getOrginalMessage() ?><br/>
                            <?php // echo $data['ex']->getTraceMessage() ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT .'/views/inc/footer.php'; ?>