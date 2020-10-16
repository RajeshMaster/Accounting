CREATE OR REPLACE VIEW v_engg_plus_list AS
SELECT
	ap_ym,
	account_period,
	quot_date,
	emp_id,
	SUM(amount) amount
FROM v_invoice_reg_emp_list irep
LEFT JOIN v_kessan_account_period_list kapl ON kapl.ap_ym = DATE_FORMAT(irep.quot_date,'%Y/%m')
GROUP BY emp_id, ap_ym