<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateProductFonts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update-fonts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product fonts and font requirements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update SKU 2001
        $product1 = Product::where('sku', '2001')->first();
        if ($product1) {
            $product1->update([
                'fonts' => ['Arial', 'Times New Roman', 'Helvetica', 'Georgia'],
                'font_requirement' => 2
            ]);
            $this->info('Product 2001 updated successfully!');
            $this->info('Fonts: ' . implode(', ', $product1->fonts));
            $this->info('Font requirement: ' . $product1->font_requirement);
            $this->info('Has fonts: ' . ($product1->hasFonts() ? 'true' : 'false'));
        }

        // Update SKU 2002
        $product2 = Product::where('sku', '2002')->first();
        if ($product2) {
            $product2->update([
                'font_requirement' => 2
            ]);
            $this->info('Product 2002 updated successfully!');
            $this->info('Fonts: ' . implode(', ', $product2->fonts));
            $this->info('Font requirement: ' . $product2->font_requirement);
            $this->info('Has fonts: ' . ($product2->hasFonts() ? 'true' : 'false'));
        }

        return 0;
    }
}
