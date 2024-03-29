<?php

	class Books extends Controller {

        /**
         * Number of items per page
         */
	    const PAGE_LIMIT = 12;

        public function __construct(){
			$this->bookModel = $this->model('Book');
		}

		public function index(){
			$this->page();
		}

		public function page($page=1){
			// Check if search query
			if(isset($_GET['novel-search']) && isset($_GET['novel-genre'])){
				// Check if search query is just empty or filled
				if(!empty(trim($_GET['novel-search'])) || $_GET['novel-genre'] != '0'){
					// Run paginate with query
					$this->paginate($page, $_GET['novel-search'], $_GET['novel-genre']);
				} else {
					// Empty search query -> load default
					$this->paginate($page);
				}
			} else {
				$this->paginate($page);				
			}
		}

		private function paginate($page, $search="", $genre=""){
      		// How may adjacent page links should be shown on each side of the current page link.
      		$adjacents = 2;

            // Compute for the offset
            $offset = Books::PAGE_LIMIT * ($page-1);

			// Check if search AND genre is empty to just load default
			if($search=="" && $genre==""){
				// Get total rows
				$this->bookModel->getBooks();
	  			$totalRows = $this->bookModel->getRowCount();
	      		// Compute total pages rounded up

		        // Get the books and display
				$books = $this->bookModel->getBooksByPagination($offset, Books::PAGE_LIMIT);
			} else {
				// With search query
				// Get total rows
				$this->bookModel->getBooksBySearch($search, $genre);
	  			$totalRows = $this->bookModel->getRowCount();

		        // Get the books and display
				$books = $this->bookModel->getBooksByPaginationSearch($search, $genre, $offset, Books::PAGE_LIMIT);
			}

            foreach ($books as $book) {
                $bookGenres = $this->bookModel->getBookGenresById($book->id);
                $book->category = [];
                foreach ($bookGenres as $bookGenre) {
                    $book->category[$bookGenre->genre_id] = $bookGenre->genre;
                }
            }

			$genresList = $this->bookModel->getGenresList();

            $totalPages = ceil($totalRows / Books::PAGE_LIMIT);

            //Here we generates the range of the page numbers which will display.
            if($totalPages <= (1+($adjacents * 2))) {
                $start = 1;
                $end   = $totalPages;
            } else {
                if(($page - $adjacents) > 1) {
                    if(($page + $adjacents) < $totalPages) {
                        $start = ($page - $adjacents);
                        $end   = ($page + $adjacents);
                    } else {
                        $start = ($totalPages - (1+($adjacents*2)));
                        $end   = $totalPages;
                    }
                } else {
                    $start = 1;
                    $end   = (1+($adjacents * 2));
                }
            }
            //If you want to display all page links in the pagination then
            //uncomment the following two lines
            //and comment out the whole if condition just above it.
            // $start = 1;
            // $end = $totalPages;


            $data = [
				'books' => $books,
				'genresList' => $genresList,
				'page' => $page,
				'start' => $start,
				'end' => $end,
				'totalPages' => $totalPages
			];

			$this->view('books/index', $data);
		}

		public function add(){
			// Check if admin
			if(isset($_SESSION['admin_mode'])){
				$genresList = $this->bookModel->getGenresList();
				if($_SERVER['REQUEST_METHOD'] == 'POST'){
					// Sanitize POST array
					$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

					// Set checkboxes state
					$genresChecked = [];
					foreach ($genresList as $genre) {
						if(isset($_POST[$genre->id])){
							$genresChecked[$genre->id] = "checked";
						} else {
							$genresChecked[$genre->id] = "";
						}
					}				

					$data = [
						'image' => $_FILES['image']['name'],
						'image_dir' => $_FILES['image']['tmp_name'],
						'image_size' => $_FILES['image']['size'],
						'name' => trim($_POST['name']),
						'description' => trim($_POST['description']),
						'price' => $_POST['price'],
						'image_err' => '',
						'name_err' => '',
						'description_err' => '',
						'price_err' => '',
						'genres' => $genresList,
						'genresChecked' => $genresChecked
					];

					// Upload directory
					$upload_dir = PUBLICROOT . "/img/";
					// For checking if image already exists 
					$target_file = $upload_dir . basename($data['image']);
					// Get file extension
					$imgExt = strtolower(pathinfo($data['image'],PATHINFO_EXTENSION));
					// Valid extensions
					$valid_extensions = array('jpeg', 'jpg', 'png', 'gif');

					// Validate data
					if(empty($data['image'])){
						$data['image_err'] = "Please upload image";
					} elseif (!in_array($imgExt, $valid_extensions)) {
						$data['image_err'] = "Please upload valid image (jpeg, jpg, png, gif)";
					} elseif (file_exists($target_file)){
						$data['image_err'] = "Image already exists";
					} elseif($_FILES['image']['error'] == 2){
						// UPLOAD_ERR_FORM_SIZE 
						// Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
						// <input type="hidden" name="MAX_FILE_SIZE" value="300000">
						$data['image_err'] = "Image file too large";
					} elseif ($data['image_size'] > 300000){
						// In case user is geeky enough to bypass max image file size in client side
						// Limit image size to 300KB
						$data['image_err'] = "Image file too large";
					}
					if(empty($data['name'])){
						$data['name_err'] = "Please enter name";
					}
					if(empty($data['description'])){
						$data['description_err'] = "Please enter description";
					}
					if(empty($data['price'])){
						$data['price_err'] = "Please enter price";
					}

					// Make sure no errors
					if(empty($data['image_err']) && empty($data['name_err']) && empty($data['name_err']) && empty($data['description_err']) && empty($data['price_err'])){
						// Validated

						// Add book to database
						if($this->bookModel->addBook($data)){
							// Copy upload file to system
							move_uploaded_file($data['image_dir'], $upload_dir . $data['image']);

							flash('book_message', 'Book Added');
							redirect('books');
						} else {
							die("Something went wrong");
						}
					} else {
						// Load view with error
						$this->view('books/add', $data);
					}
				} else {
					$genresChecked = [];
					foreach ($genresList as $genre) {
						$genresChecked[$genre->id] = "";
					}
					$data = [
						'name' => '',
						'description' => '',
						'price' => '' ,
						'genres' => $genresList,
						'genresChecked' => $genresChecked
					];
					$this->view('books/add', $data);	
				}
			} else {
				redirect('');
			}
		}

		public function edit($id){
			// Check if admin
			if(isset($_SESSION['admin_mode'])){
				// Get existing book from model
				$book = $this->bookModel->getBookById($id);
				$bookGenres = $this->bookModel->getBookGenresById($book->id);
				$genresList = $this->bookModel->getGenresList();
				if($_SERVER['REQUEST_METHOD'] == 'POST'){
					// Sanitize POST array
					$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

					// Set checkboxes state
					$genresChecked = [];
					foreach ($genresList as $genre) {
						if(isset($_POST[$genre->id])){
							$genresChecked[$genre->id] = "checked";
						} else {
							$genresChecked[$genre->id] = "";
						}
					}

                    $data = [
                        'id' => $id,
                        'image' => $book->image,
                        'name' => trim($_POST['name']),
                        'description' => trim($_POST['description']),
                        'price' => $_POST['price'],
                        'image_err' => '',
                        'name_err' => '',
                        'description_err' => '',
                        'price_err' => '',
                        'genres' => $genresList,
                        'genresChecked' => $genresChecked
                    ];
					// Upload directory
					$upload_dir = PUBLICROOT . "/img/";

					// Check if there is uploaded file
					$isThereUploadedFile = false;
					if(is_uploaded_file($_FILES['image']['tmp_name'])){
						$isThereUploadedFile = true;
					}

					if($isThereUploadedFile){
                        $data['image'] = $_FILES['image']['name'];
						// For checking if image already exists
						$target_file = $upload_dir . basename($data['image']);
						// Get file extension
						$imgExt = strtolower(pathinfo($data['image'],PATHINFO_EXTENSION));
						// Valid extensions
						$valid_extensions = array('jpeg', 'jpg', 'png', 'gif');
						// Check image uploaded
						if (!in_array($imgExt, $valid_extensions)) {
							$data['image_err'] = "Please upload valid image (jpeg, jpg, png, gif)";
						} elseif (file_exists($target_file)){
							$data['image_err'] = "Image already exists";
						} elseif($_FILES['image']['error'] == 2){
							// UPLOAD_ERR_FORM_SIZE 
							// Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
							// <input type="hidden" name="MAX_FILE_SIZE" value="300000">
							$data['image_err'] = "Image file too large";
						} elseif ($_FILES['image']['size'] > 300000){
							// In case user is geeky enough to bypass max image file size in client side
							// Limit image size to 300KB
							$data['image_err'] = "Image file too large";
						}
					}

					// Validate data
					if(empty($data['name'])){
						$data['name_err'] = "Please enter name";
					}
					if(empty($data['description'])){
						$data['description_err'] = "Please enter description";
					}
					if(empty($data['price'])){
						$data['price_err'] = "Please enter price";
					}

					// Make sure no errors
					if(empty($data['image_err']) && empty($data['name_err']) && empty($data['name_err']) && empty($data['description_err']) && empty($data['price_err'])){
						// Validated
						// Update book in database
						if($this->bookModel->updateBook($data)){
							// Copy upload file to system

							// Check if there is uploaded file
							if($isThereUploadedFile){
								// Delete old image
								unlink($upload_dir . $data['image']);
								// Copy new image
								move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $data['image']);
							}

							flash('book_message', 'Book Updated');
							redirect('books');
						} else {
							die("Something went wrong");
						}
					} else {
						// Load view with error
						$this->view('books/edit', $data);
					}
				} else {
					// Set checkboxes state
					$genresChecked = [];
					foreach($bookGenres as $genre){
						$genresChecked[$genre->genre_id] = "checked";
					}
					foreach ($genresList as $genre) {
						if(!isset($genresChecked[$genre->id])){
							$genresChecked[$genre->id] = "";
						}
					}

					$data = [
						'id' => $id,
						'image' => $book->image,
						'name' => $book->name,
						'description' => $book->description,
						'price' => $book->price ,
						'genres' => $genresList,
						'genresChecked' => $genresChecked
					];
					$this->view('books/edit', $data);	
				}
			} else {
				redirect('');
			}
		}

		public function show($id){
			$book = $this->bookModel->getBookById($id);
			$bookGenres = $this->bookModel->getBookGenresById($book->id);
			$book->category = [];
			foreach ($bookGenres as $bookGenre) {
				$book->category[$bookGenre->genre_id] = $bookGenre->genre;
			}
			$data = [
				'book' => $book,
			];
			$this->view('books/show', $data);
		}

		public function delete($id){
			// Check if admin
			if(isset($_SESSION['admin_mode'])){
				if($_SERVER["REQUEST_METHOD"] == 'POST'){
					// Get existing book from model
					$book = $this->bookModel->getBookById($id);
					// Check if book is not already ordered
					if($this->bookModel->checkBookIfOrdered($id)){
						// Give warning that you can't delete book
						flash('book_message', 'Cannot delete book, book is already ordered!', 'alert alert-danger alert-dismissible fade show');
						redirect('books');
					} else {
						if($this->bookModel->deleteBook($id)){
							// Delete local image
							unlink(PUBLICROOT . "/img/" . $book->image);
							flash('book_message', 'Book Removed');
							redirect('books');
						} else {
							die('Something went wrong');
						}
					}
				} else {
					redirect('books');
				}
			} else {
				redirect('');
			}
		}

	}
 ?>