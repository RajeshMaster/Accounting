DROP PROCEDURE IF EXISTS dev_bank_create;
DELIMITER $$
CREATE PROCEDURE dev_bank_create(
IN st_date varchar(7) , 
IN end_date varchar(7),
IN cur_value varchar(7)
)
BEGIN

  	
	DROP TABLE IF EXISTS `temp1`;
	
	SET @create_sql = "
		CREATE TEMPORARY TABLE temp1 ENGINE = MEMORY 
			SELECT
			  a.*
			  , SUM(a.amount) AS totamt 
			FROM
			  ( 
			    SELECT
			      main.id
			      , sub.id AS subid
			      , SUBSTRING(t.bankdate, 1, 7) AS date
			      , case 
			        when t.salaryFlg != 1 
			        then IFNULL(main.Subject, 'Loan Payment') 
			        else IFNULL(main.Subject, 'Paid Salary') 
			        end AS Subject
			      , case 
			        when t.salaryFlg != 1 
			        then IFNULL(main.Subject_jp, 'Loan Payment') 
			        else IFNULL(main.Subject_jp, 'Paid Salary') 
			        end AS Subject_jp
			      , case 
			        when t.salaryFlg != 1 
			        then IFNULL(sub.sub_eng, 'Loan Payment') 
			        else IFNULL(sub.sub_eng, 'Paid Salary') 
			        end AS sub_eng
			      , case 
			        when t.salaryFlg != 1 
			        then IFNULL(sub.sub_jap, 'Loan Payment') 
			        else IFNULL(sub.sub_jap, 'Paid Salary') 
			        end AS sub_jap
			      , (t.amount + t.fee) AS amount 
			    FROM
			      dev_banktransfer t 
			      LEFT JOIN dev_expensesetting main 
			        ON t.subject = main.id 
			      LEFT JOIN inv_set_expensesub sub 
			        ON t.details = sub.id 
			    WHERE
			      t.del_flg = 0 /*AND t.salaryFlg!=1*/
			      
			      UNION ALL 
			      
			    SELECT
			      main.id
			      , sub.id AS subid
			      , SUBSTRING(e.date, 1, 7) AS date
			      , case 
			        when e.salaryFlg != 1 
			        then IFNULL(main.Subject, 'Petty') 
			        else IFNULL(main.Subject, 'Paid Salary') 
			        end AS Subject
			      , case 
			        when e.salaryFlg != 1 
			        then IFNULL(main.Subject_jp, 'Petty') 
			        else IFNULL(main.Subject_jp, 'Paid Salary') 
			        end AS Subject_jp
			      , case 
			        when e.salaryFlg != 1 
			        then IFNULL(sub.sub_eng, 'Petty') 
			        else IFNULL(sub.sub_eng, 'Paid Salary') 
			        end AS sub_eng
			      , case 
			        when e.salaryFlg != 1 
			        then IFNULL(sub.sub_jap, 'Petty') 
			        else IFNULL(sub.sub_jap, 'Paid Salary') 
			        end AS sub_jap
			      , e.amount 
			    FROM
			      dev_expenses e 
			      LEFT JOIN dev_expensesetting main 
			        ON e.subject = main.id 
			      LEFT JOIN inv_set_expensesub sub 
			        ON e.details = sub.id 
			    WHERE
			      ( 
			        e.transaction_flg IS NULL 
			        OR e.transaction_flg = 0
			      ) 
			      AND e.del_flg = '1'
			  ) AS a 
			WHERE ";
			
	  IF ((st_date IS NOT NULL AND st_date <> '') AND (end_date IS NOT NULL AND end_date <> '') AND (cur_value IS NOT NULL AND cur_value <> '')) THEN
	  		set @create_where = CONCAT("a.date > '", st_date, "' AND a.date < '",end_date, " ' AND a.date<=  '",cur_value," ' ");
	  ELSEIF ((st_date IS NOT NULL AND st_date <> '') AND (end_date IS NULL OR end_date = '') AND (cur_value IS NULL OR cur_value = '')) THEN
	  		set @create_where = CONCAT("a.date <= '", st_date, "' ");
	  ELSEIF ((st_date IS NULL OR st_date = '') AND (end_date IS NOT NULL AND end_date <> '') AND (cur_value IS NULL OR cur_value = '')) THEN	
	  		set @create_where = concat("a.date >= '", end_date, "' ");
	  ELSE
	  		set @create_where = CONCAT("a.date > '",st_date,"' AND a.date < '" ,end_date, "' ");
	  END IF;


	SET @create_group = "
		GROUP BY
		  a.Subject
		  , a.sub_eng
		  , a.date 
		ORDER BY
		  a.date DESC"; 
	SET @sql2 = CONCAT(@create_sql,@create_where,@create_group);
		  
	PREPARE stmt FROM @sql2;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;

END;
$$
DELIMITER ;