CREATE OR REPLACE VIEW v_kessan_account_period_temp_list AS
SELECT
	DATE_ADD(CONCAT(Startingyear, '-', Startingmonth, '-01'), INTERVAL seq month) seq_act_period,
	Accountperiod,
	CONCAT(Startingyear, '-', Startingmonth, '-01') StartingAccountYmd,
	LAST_DAY(CURDATE()) ClosingAccountYmd,
	Closingyear,
	Closingmonth
FROM seq_0_to_1000 ts
LEFT JOIN dev_kessandetails ON TRUE