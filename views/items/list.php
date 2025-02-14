<?php
$pageTitle = "Manage Items";

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../config/database.php';

$db = new DatabaseConnection();
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Items List</h3>
                            <div class="card-actions">
                                <a href="add_item.php" class="btn button-custom">
                                    Add New Item
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <select class="form-select" id="category-filter">
                                        <option value="">All Categories</option>
                                        <?php
                                        try {
                                            require_once '../../classes/ItemCategory.php';
                                            $category = new ItemCategory($db);
                                            $categories = $category->getAllCategories();
                                            foreach($categories as $cat) {
                                                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                            }                      
                                        } catch (Exception $e) {
                                            echo "<!-- Error: " . htmlspecialchars($e->getMessage()) . " -->";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="search-items" placeholder="Search items...">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Unit</th>
                                            <th>Current Price</th>
                                            <th>Last Updated</th>
                                            <th>Status</th>
                                            <th class="w-1">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-table-body">
                                        <?php
                                        try {
                                            require_once '../../classes/Item.php';
                                            $item = new Item($db);
                                            $items = $item->getAllItems();

                                            foreach($items as $item) {
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['item_code']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                                    <td>
                                                        <span class="text-primary font-weight-bold">
                                                            <?php echo number_format($item['current_price'], 2); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($item['updated_at'])); ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $item['status'] === 'active' ? 'button-custom-black-sm' : 'button-custom-sm'; ?>">
                                                            <?php echo ucfirst($item['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-list flex-nowrap">
                                                            <button class="btn btn-sm button-custom-white-sm update-price-btn"
                                                                    data-item-id="<?php echo $item['id']; ?>"
                                                                    data-current-price="<?php echo $item['current_price']; ?>">
                                                                Update Price
                                                            </button>
                                                            <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm button-custom-white-sm">
                                                                Edit
                                                            </a>
                                                            <button class="btn btn-sm button-custom-white-sm view-history-btn"
                                                                    data-item-id="<?php echo $item['id']; ?>">
                                                                History    
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } catch (Exception $e) {
                                            echo "<tr><td colspan='8' class='text-center text-danger'>Error loading items</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Price Update Modal -->
<div class="modal modal-blur fade" id="updatePriceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Item Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updatePriceForm">
                <div class="modal-body">
                    <input type="hidden" id="update-item-id" name="item_id">
                    <div class="mb-3">
                        <label class="form-label">Current Price</label>
                        <input type="text" class="form-control" id="current-price" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">New Price</label>
                        <input type="number" class="form-control" name="new_price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div> 

<!-- Price History Modal -->
<div class="modal modal-blur fade" id="priceHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Price History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Old Price</th>
                                <th>New Price</th>
                                <th>Changed By</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="price-history-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 

<?php include '../../includes/footer_scripts.php'; ?>