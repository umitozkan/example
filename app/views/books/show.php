<?php require APPROOT .'/views/inc/header.php'; ?>
<!-- Jumbotron -->
<div class="container">    
    <div id="books-show-jumbotron" class="jumbotron text-center py-4 mb-3">
        <h1 class="text-white jumbotron-text-shadow">Light Novel Details</h1>
    </div>
</div>
<div class="container pb-5 mb-5">
    <a href="" class="btn btn-ligth m-1 back"><i class="fa fa-backward"></i> Back</a>
    <br>
    <div class="card border-secondary mb-5">
        <div class="row ">
            <div class="col-md-4">
                <img src="<?php echo IMGSRC . $data['book']->image; ?>" class="w-100">
            </div>
            <div class="col-md-5 p-3">
                <div class="card-body p-3">
                    <h3 class="card-title text-primary"><?php echo $data['book']->name ?></h3>
                    <h6 class="card-subtitle mb-3 text-muted">
                      <?php foreach($data['book']->category as $category): ?>
                        <?php echo $category . " " ?>
                      <?php endforeach; ?>
                    </h6>
                    <p class="card-text text-dark">
                        <?php echo $data['book']->description ?>
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-secondary text-center m-4">
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                        <h4 class="card-title mb-0 text-dark">Price: $<?php echo $data['book']->price ?></h4>
                        <?php if(isset($_SESSION['admin_mode'])): ?>
                        <?php else: ?>
                            <button data-index="<?php echo $data['book']->id ?>" id="cart-button" class="btn btn-sm btn-dark mt-2">
                                <img id="cart-loader" style="display: none;" src="<?php echo IMGSRC ?>ajax-loader.gif"/>
                                <i id="cart-icon" class="fa fa-shopping-cart fa-lg"></i> 
                            Add to Cart
                            </button>   
                        <?php endif ?>
                    </div>
                    <div class="card-footer text-muted">

                    </div>
                </div>
                <?php if(isset($_SESSION['admin_mode'])): ?>
                <div class="card border-secondary text-center m-4">
                    <div class="card-header">
                        <h5 class="mb-0">ADMIN PANEL</h5>
                    </div>
                    <div class="card-body">
                        <a href="<?php echo URLROOT ?>/books/edit/<?php echo $data['book']->id; ?>" class="btn btn-sm btn-info btn-block"><i class="fa fa-pencil-square-o"></i>Edit</a>
                        <hr>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-sm btn-danger btn-block" data-toggle="modal" data-target="#deleteModal"><i class="fa fa-trash"></i>
                            Delete
                        </button>
                    </div>
                    <div class="card-footer text-muted">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if(isset($_SESSION['admin_mode'])): ?>
    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Book</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this book?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <form class="pull-right" action="<?php echo URLROOT ?>/books/delete/<?php echo $data['book']->id ?>" method="POST">
                        <input type="submit" value="Delete" class="btn btn-danger">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php require APPROOT .'/views/inc/footer.php'; ?>
