var URLROOT = location.origin + "/" + location.pathname.substr(location.pathname.indexOf('/') == 0 ? 1 : 0).split('/')[0];

$(function () {
    // Add to Cart on Shop Page
    // TODO 2- Sepete Ekleme düğmesine ajax kodunu yazınız (JQuery kullanabilirsiniz)
    $('#cart-button').click(function (e) {
        addCart(e);
    });
    // Update cart via ajax
    $('#update-cart-button').click(function (e) {
        updateCart(e);
    });

    // Disable and enable cart
    $.blockUI.defaults.css = {};

    // Check for any input changes and enable update cart button 
    $("#cart-form").change(function () {
        $("#update-cart-button").prop('disabled', false);
    });

    // Hide alert message instead of removing in DOM
    $('#cart-alert-close').click(function () {
        $('#cart-alert').fadeOut('fast');
    });

    // Delete book row
    $('.cart-delete-button').click(function (e) {
        deleteCart.call(this, e);
    });

    // Go back on last page of hostname on books/show.php
    $('a.back').click(function () {
        if (document.referrer.indexOf(window.location.hostname) != -1) {
            // if from books/show/x page go back to books/page/x
            parent.history.back();
            return false;
        } else {
            // if from direct url input, go to shop page
            window.location.href = URLROOT + "/books";
            return false;
        }
    });
});

function addCart(e) {
    e.preventDefault();
    startBlockingCart();
    bookId = e.target.dataset.index;
    console.log(bookId.toString());
    $.ajax({
        url: URLROOT + '/users/ajaxaddcart',
        type: 'POST',
        data: {index: bookId},
        beforeSend: function () {
            startBlockingCart();
        },
        success: function (data) {
            var addCart = JSON.parse(data);
            console.log(addCart);
            $('#cartItems').html(addCart);
        }
    });
}

function updateCart(e) {
    e.preventDefault();
    startBlockingCart();
    $.ajax({
        url: URLROOT + '/users/ajaxupdatecart',
        type: 'POST',
        data: $('#cart-form').serializeArray(),
        beforeSend: function () {
            startBlockingCart();
        },
        complete: function () {
            var timer;
            clearTimeout(timer);
            timer = setTimeout(function () {
                stopBlockingCart();
                $("#update-cart-button").prop('disabled', true);
                $('#cart-alert').fadeIn('fast');
                $('#cart-message').html("Cart updated");
            }, 800);
        },
        success: function (data) {
            var updatedCart = JSON.parse(data);
            $('#cart-total-cost').html('P' + updatedCart.totalPrice);
            updatedCart.books.forEach(function (book) {
                $('#bookLinePrice_' + book.id).html('P' + book.linePrice);
            });
            $('#cartItems').html(" " + updatedCart.totalItems);
        }
    });
}

function deleteCart(e) {
    var bookRowId = $(this).data('index');
    e.preventDefault();
    startBlockingCart();
    var data = {'bookRowId': bookRowId};
    $.ajax({
        url: URLROOT + '/users/ajaxdeletecart',
        type: 'POST',
        data: $('#cart-form').serialize() + '&' + $.param(data),
        beforeSend: function () {
            startBlockingCart();
        },
        complete: function () {
            var timer;
            clearTimeout(timer);
            timer = setTimeout(function () {
                stopBlockingCart();
                $('#bookRowId_' + bookRowId).hide('fast', function () {
                    $('#bookRowId_' + bookRowId).remove();
                });
                $('#cart-alert').fadeIn('fast');
                $('#cart-message').html("Book removed from cart.");
            }, 800);
        },
        success: function (data) {
            console.log(data);
            var updatedCart = JSON.parse(data);
            if (updatedCart.cartEmpty == false) {
                $('#cart-total-cost').html('P' + updatedCart.totalPrice);
                $('#cartItems').html(" " + updatedCart.totalItems);
            } else {
                $('#cart-total-cost').html('P0');
                $('#cartItems').html(" 0");
                $('#checkout-button').prop('disabled', true);
                $('#cart-update-footer').fadeOut(800);
            }
        }
    });
}

function startBlockingCart() {
    $('#cart-section').block({
        message: $('#throbber')
    });
}

function stopBlockingCart() {
    $('#cart-section').unblock();
}

function startLoadingButton() {
    $('#cart-loader').show();
    $('#cart-icon').hide();
    $('#cart-button').prop('disabled', true);
}

function stopLoadingButton() {
    $('#cart-loader').hide();
    $('#cart-icon').show();
    $('#cart-button').prop('disabled', false);
}
