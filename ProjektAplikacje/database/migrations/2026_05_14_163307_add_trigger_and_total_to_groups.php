<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // 1. TRIGgery AUTOMATYZACJI: Aktualizacja salda grupy (Dodanie, Edycja, Usunięcie)
        DB::unprepared("
            CREATE TRIGGER update_group_total_after_bill_insert AFTER INSERT ON bills
            FOR EACH ROW
            BEGIN
                UPDATE groups SET total_amount = total_amount + NEW.amount WHERE id = NEW.group_id;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER update_group_total_after_bill_update AFTER UPDATE ON bills
            FOR EACH ROW
            BEGIN
                UPDATE groups SET total_amount = total_amount - OLD.amount + NEW.amount WHERE id = NEW.group_id;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER update_group_total_after_bill_delete AFTER DELETE ON bills
            FOR EACH ROW
            BEGIN
                UPDATE groups SET total_amount = total_amount - OLD.amount WHERE id = OLD.group_id;
            END
        ");

        // 2. TRIGGER WALIDACYJNY
        DB::unprepared("
            CREATE TRIGGER validate_user_in_group_before_item_assign BEFORE INSERT ON bill_item_user
            FOR EACH ROW
            BEGIN
                DECLARE v_group_id INT;
                DECLARE v_is_member INT DEFAULT 0;

                SELECT b.group_id INTO v_group_id
                FROM bill_items bi
                JOIN bills b ON bi.bill_id = b.id
                WHERE bi.id = NEW.bill_item_id;

                SELECT COUNT(*) INTO v_is_member
                FROM group_user
                WHERE group_id = v_group_id AND user_id = NEW.user_id;

                IF v_is_member = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Blad Walidacji DB: Uzytkownik nie nalezy do tej grupy!';
                END IF;
            END
        ");

        // 3. FUNKCJA SKŁADOWA
        DB::unprepared("
            CREATE FUNCTION get_user_net_balance(p_user_id INT, p_group_id INT)
            RETURNS DECIMAL(10,2)
            DETERMINISTIC
            BEGIN
                DECLARE v_total_paid DECIMAL(10,2) DEFAULT 0.00;
                DECLARE v_total_owed DECIMAL(10,2) DEFAULT 0.00;

                SELECT IFNULL(SUM(amount), 0.00) INTO v_total_paid
                FROM bills
                WHERE payer_id = p_user_id AND group_id = p_group_id;

                SELECT IFNULL(SUM(bs.amount), 0.00) INTO v_total_owed
                FROM bill_splits bs
                JOIN bills b ON bs.bill_id = b.id
                WHERE bs.user_id = p_user_id AND b.group_id = p_group_id;

                RETURN v_total_paid - v_total_owed;
            END
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared("DROP TRIGGER IF EXISTS update_group_total_after_bill_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS update_group_total_after_bill_update");
        DB::unprepared("DROP TRIGGER IF EXISTS update_group_total_after_bill_delete");
        DB::unprepared("DROP TRIGGER IF EXISTS validate_user_in_group_before_item_assign");
        DB::unprepared("DROP FUNCTION IF EXISTS get_user_net_balance");
    }
};
