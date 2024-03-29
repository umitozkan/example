<?php require APPROOT .'/views/inc/header.php'; ?>
<!-- Jumbotron -->
<div class="container">    
    <div id="books-index-jumbotron" class="jumbotron text-center py-4 mb-3">
        <h1 class="text-white jumbotron-text-shadow">Light Novels</h1>
    </div>
</div>
<div class="container">
	<?php flash('book_message') ?>
    <?php if(isset($_SESSION['admin_mode'])): ?>
	<div class="row mb-3">
		<div class="col-md-6 mt-2">
			<h1 class="mb-0 text-dark"><i class="fa fa-book"></i>Novels</h1>
		</div>
		<div class="col-md-6">
			<a href="<?php echo URLROOT; ?>/books/add" class="btn btn-dark pull-right"><i class="fa fa-pencil"></i> Add Book</a>
		</div>
	</div>
    <?php endif; ?>
    <?php require APPROOT .'/views/inc/search.php'; ?>
	<div class="row">
        <div class="col-md-12">
            <div class="container">
                <div class="row">
                    <?php foreach ($data['books'] as $book): ?>
                    <div class="col-lg-3 col-md-6 col-12 mt-3 mb-3 d-flex">
                        <div class="card border-secondary mb-xs-5 mt-xs-5">
                            <a href="<?php echo URLROOT ?>/books/show/<?php echo $book->id ?>"><img class="card-img-top" src="<?php echo IMGSRC . $book->image; ?>"></a>
                            <div class="card-body">
                                <h4 class="card-title"><strong><a href="<?php echo URLROOT ?>/books/show/<?php echo $book->id ?>"><?php echo $book->name; ?></a></strong></h4>
                                <h6 class="card-subtitle mb-3 text-muted">
                                	<?php foreach($book->category as $category): ?>
                                		<?php echo $category . " " ?>
	                                <?php endforeach; ?>
                            	</h6>
                            </div>
                            <div class="card-footer text-dark">
								Price: $<?php echo $book->price ?>
                            </div>
                        </div>
                    </div>
               <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT .'/views/inc/pagination.php'; ?>
<?php require APPROOT .'/views/inc/footer.php'; ?>