<?php

namespace App\Http\Controllers;

use App\Services\ImportExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImportExportController extends Controller
{
    protected ImportExportService $importExportService;

    public function __construct(ImportExportService $importExportService)
    {
        $this->importExportService = $importExportService;
    }

    /**
     * Show import/export dashboard
     */
    public function index()
    {
        $stats = $this->importExportService->getImportStats();
        return view('import-export.index', compact('stats'));
    }

    /**
     * Export products to CSV
     */
    public function exportProducts(): Response
    {
        $csv = $this->importExportService->exportProducts();
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="products_' . date('Y-m-d_H-i-s') . '.csv"');
    }

    /**
     * Import products from CSV
     */
    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $results = $this->importExportService->importProducts($request->file('file'));

        if ($request->expectsJson()) {
            return response()->json($results);
        }

        $message = "Import completed: {$results['success']} products imported successfully.";
        if (!empty($results['errors'])) {
            $message .= " " . count($results['errors']) . " errors occurred.";
        }

        return redirect()->back()->with([
            'success' => $message,
            'import_results' => $results
        ]);
    }

    /**
     * Export customers to CSV
     */
    public function exportCustomers(): Response
    {
        $csv = $this->importExportService->exportCustomers();
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="customers_' . date('Y-m-d_H-i-s') . '.csv"');
    }

    /**
     * Import customers from CSV
     */
    public function importCustomers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $results = $this->importExportService->importCustomers($request->file('file'));

        if ($request->expectsJson()) {
            return response()->json($results);
        }

        $message = "Import completed: {$results['success']} customers imported successfully.";
        if (!empty($results['errors'])) {
            $message .= " " . count($results['errors']) . " errors occurred.";
        }

        return redirect()->back()->with([
            'success' => $message,
            'import_results' => $results
        ]);
    }

    /**
     * Export orders to CSV
     */
    public function exportOrders(): Response
    {
        $csv = $this->importExportService->exportOrders();
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="orders_' . date('Y-m-d_H-i-s') . '.csv"');
    }

    /**
     * Download product template
     */
    public function downloadProductTemplate(): Response
    {
        $csv = $this->importExportService->generateProductTemplate();
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="product_template.csv"');
    }

    /**
     * Download customer template
     */
    public function downloadCustomerTemplate(): Response
    {
        $csv = $this->importExportService->generateCustomerTemplate();
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="customer_template.csv"');
    }

    /**
     * Get import statistics via AJAX
     */
    public function getStats()
    {
        $stats = $this->importExportService->getImportStats();
        return response()->json($stats);
    }
} 