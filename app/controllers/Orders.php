<?php
class Orders extends Controller
{
    /**
     * Number of items per page
     */
    const PAGE_LIMIT = 5;

    public function __construct()
    {
        $this->bookModel = $this->model('Book');
        $this->orderModel = $this->model('Order');
    }

    public function index(){
		$this->page();
	}

	public function page($page=1){
		// Check if there is logged-in user
		if(isset($_SESSION['user_id'])){
			// How may adjacent page links should be shown on each side of the current page link.
	  		$adjacents = 2;	
			// Get total rows
			// Check if admin
			if(isset($_SESSION['admin_mode'])){
				$this->orderModel->getOrders();
			} else {
				$this->orderModel->getOrdersById(intval($_SESSION['user_id']));
			}
			$totalRows = $this->orderModel->getRowCount();
	  		// Compute total pages rounded up
	  		$totalPages = ceil($totalRows / ORDERS::PAGE_LIMIT);
	  		// Compute for the offset
	        $offset = ORDERS::PAGE_LIMIT * ($page-1);

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

		    // Get orders of customer
		    // Check if admin
		    if(isset($_SESSION['admin_mode'])){
		    	$orders = $this->orderModel->getOrdersByPagination($offset, ORDERS::PAGE_LIMIT);
		    } else {
		    	$orders = $this->orderModel->getOrdersByIdPagination($offset, ORDERS::PAGE_LIMIT, $_SESSION['user_id']);
		    }
			$data = [
				'orders' => $orders,
				'page' => $page,
				'start' => $start,
				'end' => $end,
				'totalPages' => $totalPages
			];

			$this->view('orders/index', $data);
		} else {
			redirect('');
		}
	}

	public function show($id){
		// Check if there is logged-in user
		if(isset($_SESSION['user_id'])){
			$order = $this->orderModel->getOrderById($id);

			// Check if admin
			if(!isset($_SESSION['admin_mode'])){
				$customerId = $this->orderModel->getCustomerId($_SESSION['user_id']);
			}
			// Check if admin or correct customer
			if(isset($_SESSION['admin_mode']) || $customerId == $order->customer_id){
				// TODO 4- Sipariş detaylarını DB'den çekip ayarlayan kod parçasını yazınız.
                $orderDetails = $this->orderModel->getOrderDetailsById($id);
				$data = [
					'totalPrice' => $order->total_price,
					'orderDate' => $order->order_date,
					'books' => $orderDetails
				];
				$this->view('orders/show', $data);
			} else {
				redirect('orders');
			}
		} else {
			redirect('');
		}
	}
}
