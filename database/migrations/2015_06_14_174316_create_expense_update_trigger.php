<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseUpdateTrigger extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {

        DB::unprepared(
            'CREATE TRIGGER expense_update_category_total
            AFTER UPDATE ON `expenses` FOR EACH ROW
            BEGIN
                IF NEW.category_id <> OLD.category_id THEN
                    UPDATE categories set total = total - OLD.value
                    WHERE id = OLD.category_id;
                    UPDATE categories set total = total + NEW.value
                    WHERE id = NEW.category_id;
                ELSEIF NEW.deleted_at <> OLD.deleted_at THEN
                    UPDATE categories set total = total - OLD.value
                    WHERE id = OLD.category_id;
                ELSEIF NEW.value <> OLD.value THEN
                    UPDATE categories set total = total + NEW.value - OLD.value
                    WHERE id = NEW.category_id;
                END IF;
            END'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `expense_update_category_total`');
    }

}
