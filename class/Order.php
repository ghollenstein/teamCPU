<?php
class Order
{

    protected $conn;
    protected $userModel;
    protected $addressModel;
    protected $productModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $paymentModel;
    protected $userId;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->userModel = new Users($this->conn);
        $this->addressModel = new Addresses($this->conn);
        $this->orderModel = new Orders($this->conn);
        $this->orderItemModel = new Order_items($this->conn);
        $this->productModel = new Products($this->conn);
        $this->paymentModel = new Payments($this->conn);
        $this->userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    }



    public function saveOrder($params)
    {
        $cartDataJson = $params['POST']['cartData'] ?? '{}';
        $cartData = json_decode(html_entity_decode($cartDataJson), true);
        $deliveryAddressId = $params['POST']['delivery'] ?? null;
        $billingAddressId = $params['POST']['billing'] ?? null;
        $paymentStatus = $params['POST']['stripeToken'] ?? null;

        // Start the transaction
        $this->conn->begin_transaction();
        try {
            //order
            $this->orderModel->address_id_delivery = $deliveryAddressId;
            $this->orderModel->address_id_billing = $billingAddressId;
            $this->orderModel->user_id = $this->userId;
            $this->orderModel->order_date = date('Y-m-d H:i:s');  // Current date and time
            $this->orderModel->total_price = 0;  // This will be calculated based on cart items
            $this->orderModel->status = 1;  // Example status

            if (!$this->orderModel->create()) {
                throw new Exception("Failed to create order");
            }

            //payment
            $this->paymentModel->order_id = $this->orderModel->order_id;
            $this->paymentModel->payment_date = date('Y-m-d H:i:s');
            $this->paymentModel->payment_type = 'CC';
            $this->paymentModel->payment_status = $paymentStatus;
            $this->paymentModel->create();

            if (!$this->paymentModel->create()) {
                throw new Exception("Failed to create payment");
            }

            //products
            $totalPrice = 0;
            foreach ($cartData as $productId => $details) {
                //get the product object
                $this->productModel->get($productId);


                if (!$this->productModel->product_id) {
                    throw new Exception("Product not found with ID: $productId");
                }

                $this->orderItemModel->order_id = $this->orderModel->order_id;  // Assuming Order class sets this on successful creation
                $this->orderItemModel->product_id = $productId;
                $this->orderItemModel->quantity = $details['quantity'];
                $this->orderItemModel->price = $this->productModel->price;
                $this->orderItemModel->tax = $this->productModel->tax;

                if (!$this->orderItemModel->create()) {
                    throw new Exception("Failed to add order items");
                }

                //reduce stock
                $this->productModel->stock = $this->productModel->stock - $this->orderItemModel->quantity;
                if ($this->productModel->update() === 0) {  // Update method must handle total price updates
                    throw new Exception("Failed to update the stock quantity");
                }

                $totalPrice += $this->orderItemModel->price * $this->orderItemModel->quantity;
            }

            // Update the total price in the order after all items are added
            $this->orderModel->total_price = $totalPrice;
            if ($this->orderModel->update() === 0) {  // Update method must handle total price updates
                throw new Exception("Failed to update total price in the order");
            }

            $this->conn->commit();
            echo "Order processed successfully.";
        } catch (Exception $e) {
            $this->conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
}
