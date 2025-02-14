<?php
$pageTitle = isset($_GET['id']) ? "Edit Item" : "Add New Item";
$itemId = $_GET['id'] ?? null;

include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';

require_once '../../config/database.php';

$db = new DatabaseConnection(); 

$item = null;
if ($itemId) {
    require_once '../../classes/Item.php';
    $itemObj = new Item($db);
    $item = $itemObj->getItem($itemId);
}
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $pageTitle; ?></h3>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo path('handlers'); ?>save_item.php" method="POST">
                                <div class="row mb-3">
                                    <?php if ($itemId): ?>
                                    <input type="hidden" name="id" value="<?php echo $itemId; ?>">
                                    <?php endif; ?>

                                    <div class="col-sm-6">
                                        <label class="form-label required">Item Code</label>
                                        <input type="text" class="form-control" name="item_code"
                                            value="<?php echo $item['item_code'] ?? ''; ?>" required>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label required">Name</label>
                                        <input type="text" class="form-control" name="name"
                                            value="<?php echo $item['name'] ?? ''; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Category</label>
                                        <select class="form-select" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php
                                        try {
                                            require_once '../../classes/ItemCategory.php';
                                            $category = new ItemCategory($db);
                                            $categories = $category->getAllCategories();

                                            foreach($categories as $cat) {
                                                $selected = ($item['category_id'] ?? '') == $cat['id'] ? 'selected' : '';
                                                echo "<option value='{$cat['id']}' {$selected}>{$cat['name']}</option>";
                                            } 
                                        } catch (Exception $e) {
                                            echo "<!-- Error: ". htmlspecialchars($e->getMessage()) ."-->";
                                        } 
                                        ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label required">Unit</label>
                                        <input type="text" class="form-control" name="unit"
                                            value="<?php echo $item['unit'] ?? ''; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Price</label>
                                        <input type="number" class="form-control" name="current_price" step="0.01"
                                            value="<?php echo $item['current_price'] ?? ''; ?>" required>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label required">Reorder Level</label>
                                        <input type="number" class="form-control" name="reorder_level"
                                            value="<?php echo $item['reorder_level'] ?? '0'; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-12">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description"
                                            rows="3"><?php echo $item['description'] ?? ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label required">Status</label>
                                        <select class="form-select" name="status" required>
                                            <option value="active"
                                                <?php echo ($item['status'] ?? '') == 'active' ? 'selected' : ''; ?>>
                                                Active</option>
                                            <option value="inactive"
                                                <?php echo ($item['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>
                                                Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn button-custom">Save Item</button>
                                    <a href="items.php" class="btn btn-link">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../../includes/main_footer.php'; ?>
</div>

<?php include '../../includes/footer_scripts.php'; ?>