DROP PROCEDURE IF EXISTS expensesdetails_customer_create;
DELIMITER $$
CREATE PROCEDURE expensesdetails_customer_create()
BEGIN

	DECLARE ERROR_FLG INT DEFAULT 0;

	DECLARE CONTINUE HANDLER FOR 1064 SET ERROR_FLG = '1';
	DECLARE CONTINUE HANDLER FOR 1065 SET ERROR_FLG = '1';

  	SET @sql1 = NULL;
  	
  	SET SESSION group_concat_max_len = 1000000;

  	
	SELECT
	  GROUP_CONCAT(DISTINCT
		    CONCAT(
		      'sum(case when Period = ''',
		      Period,
		      ''' then totamt else 0 end) AS `',
		      Period, '`'
		    )
		  ) INTO @sql
		FROM  temp1; 

	SET @sql = CONCAT( 
	    'SELECT id,subid,date,Subject,sub_eng,Subject_jp,sub_jap, '
	    , @sql
	    , ' 
		from temp1
		GROUP BY sub_eng ORDER BY Subject,sub_eng'
	  );
	   
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
