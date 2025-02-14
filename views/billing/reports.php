<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../includes/navbar.php';
?>

<div class="page-wrapper">
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Billing Reports</h3>
                        </div>
                        <div class="card-body">
                            <form id="reportForm" action="../../handlers/generate_bill_report.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="form-label required">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label required">End Date</label>
                                        <input type="date" class="form-control" name="end_date" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label required">Report Format</label>
                                        <select class="form-select" name="format" required>
                                            <option value="html">HTML</option>
                                            <option value="csv">CSV</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Generate Report</button>
                                    </div>
                                </div>
                            </form>

                            <div id="reportContent" class="mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const format = formData.get('format');
    
    if (format === 'csv') {
        this.submit(); // Let the form submit normally for CSV download
        return;
    }
    
    // For HTML format, use AJAX
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('reportContent').innerHTML = html;
    })
    .catch(error => {
        alert('Error generating report: ' + error.message);
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
