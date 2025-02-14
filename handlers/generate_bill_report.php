<?php
require_once '../config/database.php';
require_once '../classes/Billing.php';

class BillReportGenerator {
    private $billing;
    private $db;

    public function __construct() {
        $this->db = new DatabaseConnection();
        $this->billing = new Billing($this->db);
    }

    public function generateReport($startDate, $endDate, $format = 'html') {
        try {
            $bills = $this->billing->getBillsByDateRange($startDate, $endDate);
            
            switch($format) {
                case 'pdf':
                    return $this->generatePDFReport($bills);
                case 'csv':
                    return $this->generateCSVReport($bills);
                case 'html':
                default:
                    return $this->generateHTMLReport($bills);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to generate report: " . $e->getMessage());
        }
    }

    private function generateHTMLReport($bills) {
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;

        $html = '<div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Billing Report</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Bill Number</th>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>';

        foreach($bills as $bill) {
            $totalAmount += $bill['total_amount'];
            $totalPaid += $bill['paid_amount'];
            $totalPending += ($bill['total_amount'] - $bill['paid_amount']);

            $html .= "<tr>
                        <td>{$bill['bill_number']}</td>
                        <td>{$bill['patient_name']}</td>
                        <td>{$bill['bill_date']}</td>
                        <td>" . number_format($bill['total_amount'], 2) . "</td>
                        <td>" . number_format($bill['paid_amount'], 2) . "</td>
                        <td><span class='badge bg-" . $this->getStatusColor($bill['status']) . "'>{$bill['status']}</span></td>
                    </tr>";
        }

        $html .= '</tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Totals:</strong></td>
                        <td><strong>' . number_format($totalAmount, 2) . '</strong></td>
                        <td><strong>' . number_format($totalPaid, 2) . '</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="mt-4">
                <h4>Summary</h4>
                <p>Total Bills: ' . count($bills) . '</p>
                <p>Total Amount: ' . number_format($totalAmount, 2) . '</p>
                <p>Total Paid: ' . number_format($totalPaid, 2) . '</p>
                <p>Total Pending: ' . number_format($totalPending, 2) . '</p>
            </div>
        </div>
    </div>';

        return $html;
    }

    private function generateCSVReport($bills) {
        $csv = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($csv, ['Bill Number', 'Patient', 'Date', 'Total Amount', 'Paid Amount', 'Status']);
        
        // Add data
        foreach($bills as $bill) {
            fputcsv($csv, [
                $bill['bill_number'],
                $bill['patient_name'],
                $bill['bill_date'],
                $bill['total_amount'],
                $bill['paid_amount'],
                $bill['status']
            ]);
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return $content;
    }

    private function generatePDFReport($bills) {
        // Implement PDF generation using a library like TCPDF or FPDF
        // This is a placeholder - you'll need to implement the actual PDF generation
        throw new Exception("PDF generation not implemented yet");
    }

    private function getStatusColor($status) {
        switch($status) {
            case 'paid':
                return 'success';
            case 'partially_paid':
                return 'warning';
            case 'pending':
                return 'info';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}

// Handle report generation request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
        $endDate = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
        $format = filter_input(INPUT_POST, 'format', FILTER_SANITIZE_STRING) ?? 'html';

        $generator = new BillReportGenerator();
        $report = $generator->generateReport($startDate, $endDate, $format);

        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="billing_report.csv"');
            echo $report;
        } else {
            echo $report;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
