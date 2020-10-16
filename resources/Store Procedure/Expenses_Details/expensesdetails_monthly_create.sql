DROP PROCEDURE IF EXISTS expensesdetails_monthly_create;
DELIMITER $$
CREATE PROCEDURE expensesdetails_monthly_create()
BEGIN

	DECLARE ERROR_FLG INT DEFAULT 0;

	DECLARE CONTINUE HANDLER FOR 1064 SET ERROR_FLG = '1';
	DECLARE CONTINUE HANDLER FOR 1065 SET ERROR_FLG = '1';

  	
	SELECT
	  GROUP_CONCAT(DISTINCT
		    CONCAT(
		      'sum(case when SUBSTRING(date,6,2) = ''',
		      SUBSTRING(date,6,2),
		      ''' then totamt else 0 end) AS `',
		      SUBSTRING(date,6,2), '`'
		    )
		  ) INTO @sql
		FROM  temp1; 

	SET @sql = CONCAT('SELECT Period, ', @sql, ' 
							                  FROM temp1
							                  GROUP BY Period ORDER BY date DESC');
	   
	PREPARE stmt FROM @sql;
	
	IF ERROR_FLG > 0 THEN
        SELECT ERROR_FLG;
        SET ERROR_FLG = 0;
    ELSE
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
    END IF;

END;
$$
DELIMITER ;
