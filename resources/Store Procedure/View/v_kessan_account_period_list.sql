CREATE OR REPLACE VIEW v_kessan_account_period_list AS
SELECT
        *,
        DATE_FORMAT(seq_act_period,'%Y/%m') ap_ym,
        CASE
                WHEN MONTH(seq_act_period) <= Closingmonth
                THEN (YEAR(seq_act_period) - Closingyear) + Accountperiod
                ELSE (YEAR(seq_act_period) - Closingyear) + Accountperiod + 1
        END account_period
FROM v_kessan_account_period_temp_list
WHERE seq_act_period <= LAST_DAY(CURDATE());