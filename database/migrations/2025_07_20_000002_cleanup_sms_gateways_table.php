<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            $columns = [
                'created_by_id', 'to_name', 'text', 'msg_name', 'is_current', 'http_api', 'class_name',
                'key_one', 'key_one_description', 'key_two', 'key_two_description', 'key_three', 'key_three_description',
                'key_four', 'key_four_description', 'notes'
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('sms_gateways', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down()
    {
        // No-op: columns are not restored
    }
};
