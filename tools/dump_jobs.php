<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$rows=DB::table('jobs')->orderBy('id','desc')->limit(5)->get();
foreach($rows as $row){
    echo "id={$row->id} attempts={$row->attempts} reserved_at={$row->reserved_at} available_at={$row->available_at} payload=";
    echo substr($row->payload,0,200) . "...\n";
}
