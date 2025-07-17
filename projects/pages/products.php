<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'add' || $action === 'edit') {
    $product_id = isset($_GET['id']) ? $_GET['id'] : null;
    $product = null;
    
    if ($action === 'edit' && $product_id) {
        $product = getProduct($conn, $product_id);
        if (!$product) {
            echo '<div class="alert alert-danger">Product not found.</div>';
            return;
        }
    }
    
    // Get categories
    $categories_sql = "SELECT * FROM categories ORDER BY name";
    $categories = $conn->query($categories_sql);
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-box"></i> <?php echo $action === 'add' ? 'Add Product' : 'Edit Product'; ?></h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Product Information</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id">
                                <option value="">Select Category</option>
                                <?php while ($category = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo ($product['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <?php if (!empty($product['image'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Current product image" 
                                 class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            <br><small class="text-muted">Current image</small>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <small class="text-muted">Supported formats: JPG, PNG, GIF. Max size: 5MB</small>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="price" 
                                       value="<?php echo $product['price'] ?? ''; ?>" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock" 
                                   value="<?php echo $product['stock'] ?? '0'; ?>" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" class="form-control" name="low_stock_threshold" 
                                   value="<?php echo $product['low_stock_threshold'] ?? '10'; ?>" min="0">
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="index.php?page=products" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php
} else {
    // List all products
    $products_sql = "SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.name";
    $products = $conn->query($products_sql);
    ?>
    
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-box"></i> Products</h2>
            <a href="index.php?page=products&action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Product saved successfully!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> 
            <?php 
            switch ($_GET['error']) {
                case 'delete_failed':
                    echo 'Error deleting product.';
                    break;
                case 'in_use':
                    echo 'Cannot delete this product as it has been used in sales transactions.';
                    break;
                case 'not_found':
                    echo 'Product not found.';
                    break;
                default:
                    echo 'An error occurred.';
            }
            ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Product Inventory</h5>
        </div>
        <div class="card-body">
            <?php if ($products->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo getProductImageUrl($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                        <?php if ($product['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($product['description']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo formatCurrency($product['price']); ?></td>
                                    <td>
                                        <?php 
                                        $stock_class = $product['stock'] <= $product['low_stock_threshold'] ? 'text-danger' : 'text-success';
                                        echo "<span class='$stock_class'>" . $product['stock'] . "</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($product['stock'] <= 0): ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php elseif ($product['stock'] <= $product['low_stock_threshold']): ?>
                                            <span class="badge bg-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?page=products&action=delete&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No products found</h5>
                    <p class="text-muted">Start by adding your first product.</p>
                    <a href="index.php?page=products&action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?> 