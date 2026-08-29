<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Models\StokBarang;
use Illuminate\Console\Command;

class GenerateStokAwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-stok-awal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Barang::all() as $barang){
        StokBarang::firstOrCreate(

            ['barang_id'=>$barang->id],

            ['stok' => 0]
        );

        }
        $this->info('stok awal berhasil dibuat');
    }
}
