<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'new') {
    // Get products for sale
    $products_sql = "SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.stock > 0 
                     ORDER BY p.name";
    $products = $conn->query($products_sql);
    
    // Get customers
    $customers_sql = "SELECT * FROM customers ORDER BY name";
    $customers = $conn->query($customers_sql);
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-shopping-cart"></i> New Sale</h2>
        </div>
    </div>
    
    <form method="POST" id="saleForm">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Sale Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="saleItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="saleItemsBody">
                                    <!-- Items will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Product</label>
                                <select class="form-select" id="productSelect">
                                    <option value="">Choose a product...</option>
                                    <?php while ($product = $products->fetch_assoc()): ?>
                                        <option value="<?php echo $product['id']; ?>" 
                                                data-price="<?php echo $product['price']; ?>"
                                                data-stock="<?php echo $product['stock']; ?>">
                                            <?php echo htmlspecialchars($product['name']); ?> 
                                            (Stock: <?php echo $product['stock']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantityInput" min="1" value="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-success w-100" id="addItemBtn">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Sale Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Customer (Optional)</label>
                            <select class="form-select" name="customer_id">
                                <option value="">Walk-in Customer</option>
                                <?php while ($customer = $customers->fetch_assoc()): ?>
                                    <option value="<?php echo $customer['id']; ?>">
                                        <?php echo htmlspecialchars($customer['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method" id="paymentMethodSelect">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                        
                        <!-- Beautiful CVV Section (hidden by default, shown if Card is selected) -->
                        <div class="mb-3" id="cvvSection" style="display:none;">
                            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #ff6bd8 100%); color: #fff;">
                                <div class="card-header border-0" style="background: transparent;">
                                    <h6 class="mb-0" style="font-weight:700; letter-spacing:1px;">
                                        <i class="fas fa-credit-card"></i> Card Security (CVV)
                                    </h6>
                                </div>
                                <div class="card-body pb-2 pt-3">
                                    <label for="cvvInput" class="form-label" style="color:#fff; font-weight:600;">
                                        CVV
                                        <span tabindex="0" data-bs-toggle="tooltip" title="3 or 4 digit code on the back of your card" style="color:#ffd700; cursor:pointer;">
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                    </label>
                                    <div class="input-group input-group-lg position-relative">
                                        <input type="password" maxlength="4" class="form-control cvv-input" id="cvvInput" name="cvv" placeholder="CVV" autocomplete="off" style="border-radius: 10px; border: none; box-shadow: 0 2px 8px rgba(255,255,255,0.15); background: rgba(255,255,255,0.15); color: #fff; font-size:1.2rem; font-weight:600; letter-spacing:2px;">
                                        <span class="input-group-text bg-transparent border-0" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#ffd700; font-size:1.3rem;">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-2">
                            <div class="col-6">Subtotal:</div>
                            <div class="col-6 text-end" id="subtotal">$0.00</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6">Discount:</div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" name="discount" 
                                       id="discountInput" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-3">
                            <div class="col-6"><strong>Total:</strong></div>
                            <div class="col-6 text-end"><strong id="totalAmount">$0.00</strong></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100" id="completeSaleBtn" disabled>
                            <i class="fas fa-check"></i> Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let saleItems = [];
        let subtotal = 0;
        
        $('#addItemBtn').click(function() {
            const productId = $('#productSelect').val();
            const quantity = parseInt($('#quantityInput').val());
            const option = $('#productSelect option:selected');
            const price = parseFloat(option.data('price'));
            const stock = parseInt(option.data('stock'));
            const productName = option.text();
            
            if (!productId || quantity <= 0) {
                Swal.fire({
                    title: '⚠️ Missing Information',
                    text: 'Please select a product and enter a valid quantity.',
                    icon: 'warning',
                    confirmButtonText: 'Got it!',
                    confirmButtonColor: '#ffc107',
                    background: 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'animated fadeInUp',
                        title: 'text-white',
                        content: 'text-white'
                    }
                });
                return;
            }
            
            if (quantity > stock) {
                Swal.fire({
                    title: '📦 Stock Limit Exceeded',
                    text: `Quantity (${quantity}) exceeds available stock (${stock}).`,
                    icon: 'error',
                    confirmButtonText: 'Got it!',
                    confirmButtonColor: '#dc3545',
                    background: 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'animated fadeInUp',
                        title: 'text-white',
                        content: 'text-white'
                    }
                });
                return;
            }
            
            const total = price * quantity;
            
            saleItems.push({
                product_id: productId,
                name: productName,
                price: price,
                quantity: quantity,
                total: total
            });
            
            updateSaleTable();
            updateTotals();
            
            // Reset inputs
            $('#productSelect').val('');
            $('#quantityInput').val(1);
        });
        
        function updateSaleTable() {
            const tbody = $('#saleItemsBody');
            tbody.empty();
            
            saleItems.forEach((item, index) => {
                const row = `
                    <tr>
                        <td>${item.name}</td>
                        <td>$${item.price.toFixed(2)}</td>
                        <td>${item.quantity}</td>
                        <td>$${item.total.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        function removeItem(index) {
            saleItems.splice(index, 1);
            updateSaleTable();
            updateTotals();
        }
        
        function updateTotals() {
            subtotal = saleItems.reduce((sum, item) => sum + item.total, 0);
            const discount = parseFloat($('#discountInput').val()) || 0;
            const total = subtotal - discount;
            
            $('#subtotal').text('$' + subtotal.toFixed(2));
            $('#totalAmount').text('$' + total.toFixed(2));
            
            $('#completeSaleBtn').prop('disabled', saleItems.length === 0);
        }
        
        $('#discountInput').on('input', updateTotals);
        
        $('#saleForm').submit(function(e) {
            if (saleItems.length === 0) {
                e.preventDefault();
                Swal.fire({
                    title: '🛒 Empty Sale',
                    text: 'Please add at least one item to the sale.',
                    icon: 'warning',
                    confirmButtonText: 'Got it!',
                    confirmButtonColor: '#ffc107',
                    background: 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'animated fadeInUp',
                        title: 'text-white',
                        content: 'text-white'
                    }
                });
                return;
            }
            
            // Add hidden inputs for items
            saleItems.forEach((item, index) => {
                $(this).append(`<input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">`);
                $(this).append(`<input type="hidden" name="items[${index}][price]" value="${item.price}">`);
                $(this).append(`<input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">`);
            });
        });
        
        // Show/hide CVV section based on payment method
        $('#paymentMethodSelect').on('change', function() {
            if ($(this).val() === 'card') {
                $('#cvvSection').slideDown(250);
            } else {
                $('#cvvSection').slideUp(200);
            }
        });
        // Initialize tooltip for CVV info
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
    
    <?php
} elseif ($action === 'view' && isset($_GET['id'])) {
    $sale_id = $_GET['id'];
    $sale_sql = "SELECT s.*, u.full_name as cashier_name, c.name as customer_name 
                  FROM sales s 
                  LEFT JOIN users u ON s.user_id = u.id 
                  LEFT JOIN customers c ON s.customer_id = c.id 
                  WHERE s.id = ?";
    $stmt = $conn->prepare($sale_sql);
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    $sale = $stmt->get_result()->fetch_assoc();
    
    if (!$sale) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "❌ Sale Not Found",
                    text: "The requested sale could not be found.",
                    icon: "error",
                    confirmButtonText: "Go Back",
                    confirmButtonColor: "#dc3545",
                    background: "linear-gradient(135deg, #dc3545 0%, #c82333 100%)",
                    backdrop: "rgba(0, 0, 0, 0.4)",
                    customClass: {
                        popup: "animated fadeInUp",
                        title: "text-white",
                        content: "text-white"
                    }
                }).then(() => {
                    window.location.href = "index.php?page=sales";
                });
            });
        </script>';
        return;
    }
    
    // Get sale items
    $items_sql = "SELECT si.*, p.name as product_name 
                   FROM sale_items si 
                   JOIN products p ON si.product_id = p.id 
                   WHERE si.sale_id = ?";
    $stmt = $conn->prepare($items_sql);
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    $items = $stmt->get_result();
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-receipt"></i> Sale Details</h2>
            <!-- SweetAlert2 CDN -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            
            <?php if (isset($_GET['success'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '✅ Sale Completed!',
                        text: 'Sale completed successfully!',
                        icon: 'success',
                        confirmButtonText: 'Great!',
                        confirmButtonColor: '#28a745',
                        background: 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
                        backdrop: 'rgba(0, 0, 0, 0.4)',
                        customClass: {
                            popup: 'animated fadeInUp',
                            title: 'text-white',
                            content: 'text-white'
                        }
                    });
                });
            </script>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Invoice #<?php echo htmlspecialchars($sale['invoice_number']); ?></h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($sale['created_at'])); ?><br>
                    <strong>Cashier:</strong> <?php echo htmlspecialchars($sale['cashier_name']); ?><br>
                    <strong>Customer:</strong> <?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer'); ?>
                </div>
                <div class="col-md-6 text-end">
                    <strong>Payment Method:</strong> <?php echo ucfirst($sale['payment_method']); ?><br>
                    <strong>Subtotal:</strong> <?php echo formatCurrency($sale['total_amount']); ?><br>
                    <strong>Discount:</strong> <?php echo formatCurrency($sale['discount']); ?><br>
                    <strong>Total:</strong> <?php echo formatCurrency($sale['final_amount']); ?>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo formatCurrency($item['price']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo formatCurrency($item['total']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-3">
                <a href="index.php?page=sales" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Sales
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
    
    <?php
} else {
    // List all sales
    $sales_sql = "SELECT s.*, u.full_name as cashier_name, c.name as customer_name 
                   FROM sales s 
                   LEFT JOIN users u ON s.user_id = u.id 
                   LEFT JOIN customers c ON s.customer_id = c.id 
                   ORDER BY s.created_at DESC";
    $sales = $conn->query($sales_sql);
    ?>
    
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-shopping-cart"></i> Sales</h2>
            <a href="index.php?page=sales&action=new" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Sale
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Sales History</h5>
        </div>
        <div class="card-body">
            <?php if ($sales->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Cashier</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sale = $sales->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($sale['invoice_number']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                                    <td><?php echo htmlspecialchars($sale['cashier_name']); ?></td>
                                    <td><?php echo formatCurrency($sale['final_amount']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $sale['payment_method'] === 'cash' ? 'success' : 'info'; ?>">
                                            <?php echo ucfirst($sale['payment_method']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($sale['created_at'])); ?></td>
                                    <td>
                                        <a href="index.php?page=sales&action=view&id=<?php echo $sale['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">No sales found.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}
?> 