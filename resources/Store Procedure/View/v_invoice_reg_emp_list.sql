CREATE OR REPLACE VIEW v_invoice_reg_emp_list AS

SELECT
	emp_ID1 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount1, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID1 IS NOT NULL AND emp_ID1 != '' AND amount1 IS NOT NULL AND amount1 != ''

UNION 

SELECT
	emp_ID2 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount2, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID2 IS NOT NULL AND emp_ID2 != '' AND amount2 IS NOT NULL AND amount2 != ''

UNION 

SELECT
	emp_ID3 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount3, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID3 IS NOT NULL AND emp_ID3 != '' AND amount3 IS NOT NULL AND amount3 != ''

UNION

SELECT
	emp_ID4 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount4, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID4 IS NOT NULL AND emp_ID4 != '' AND amount4 IS NOT NULL AND amount4 != ''

UNION
 
SELECT
	emp_ID5 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount5, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID5 IS NOT NULL AND emp_ID5 != '' AND amount5 IS NOT NULL AND amount5 != ''

UNION
 
SELECT
	emp_ID6 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount6, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID6 IS NOT NULL AND emp_ID6 != '' AND amount6 IS NOT NULL AND amount6 != ''

UNION
 
SELECT
	emp_ID7 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount7, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID7 IS NOT NULL AND emp_ID7 != '' AND amount7 IS NOT NULL AND amount7 != ''

UNION
 
SELECT
	emp_ID8 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount8, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID8 IS NOT NULL AND emp_ID8 != '' AND amount8 IS NOT NULL AND amount8 != ''

UNION
 
SELECT
	emp_ID9 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount9, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID9 IS NOT NULL AND emp_ID9 != '' AND amount9 IS NOT NULL AND amount9 != ''

UNION
 
SELECT
	emp_ID10 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount10, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID10 IS NOT NULL AND emp_ID10 != '' AND amount10 IS NOT NULL AND amount10 != ''

UNION

SELECT
	emp_ID11 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount11, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID11 IS NOT NULL AND emp_ID11 != '' AND amount11 IS NOT NULL AND amount11 != ''

UNION
 
SELECT
	emp_ID12 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount12, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID12 IS NOT NULL AND emp_ID12 != '' AND amount12 IS NOT NULL AND amount12 != ''

UNION

SELECT
	emp_ID13 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount13, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID13 IS NOT NULL AND emp_ID13 != '' AND amount13 IS NOT NULL AND amount13 != ''

UNION
 
SELECT
	emp_ID14 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount14, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID14 IS NOT NULL AND emp_ID14 != '' AND amount14 IS NOT NULL AND amount14 != ''

UNION
 
SELECT
	emp_ID15 emp_id,
	quot_date,
	FnGetAmountIncTax(quot_date, tax, REPLACE(amount15, ',', '')) amount
FROM dev_invoices_registration 
WHERE 
 del_flg = 0 AND trading_destination_selection IS NOT NULL AND trading_destination_selection != ''
 AND emp_ID15 IS NOT NULL AND emp_ID15 != '' AND amount15 IS NOT NULL AND amount15 != ''
 ;
