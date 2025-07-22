<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            // Drop all old columns except id
            foreach ([
                'name', 'description', 'username', 'password', 'api_key', 'api_secret', 'account_sid', 'auth_token', 'from', 'url', 'extra', 'created_at', 'updated_at', 'deleted_at'
            ] as $col) {
                if (Schema::hasColumn('sms_gateways', $col)) {
                    $table->dropColumn($col);
                }
            }
            // Add new columns if not exist
            if (!Schema::hasColumn('sms_gateways', 'key')) {
                $table->string('key')->nullable();
            }
            if (!Schema::hasColumn('sms_gateways', 'sender')) {
                $table->string('sender')->nullable();
            }
            if (!Schema::hasColumn('sms_gateways', 'active')) {
                $table->boolean('active')->default(true);
            }
            if (!Schema::hasColumn('sms_gateways', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('sms_gateways', function (Blueprint $table) {
            foreach (['key', 'sender', 'active'] as $col) {
                if (Schema::hasColumn('sms_gateways', $col)) {
                    $table->dropColumn($col);
                }
            }
            if (Schema::hasColumn('sms_gateways', 'created_at') && Schema::hasColumn('sms_gateways', 'updated_at')) {
                $table->dropTimestamps();
            } else {
                if (Schema::hasColumn('sms_gateways', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('sms_gateways', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            }
        });
    }
};
