<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddFulltextIndexToProductsTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE products ADD FULLTEXT fulltext_index_description (description)');
    }

    public function down()
    {
        DB::statement('ALTER TABLE products DROP INDEX fulltext_index_description');
    }
}