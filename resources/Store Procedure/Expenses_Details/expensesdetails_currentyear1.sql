DROP PROCEDURE IF EXISTS dev_bank;
DELIMITER $$
CREATE PROCEDURE dev_bank(
IN st_date varchar(7), 
IN end_date varchar(7),
IN cur_value varchar(7)
)
BEGIN

	DECLARE ERROR_FLG INT DEFAULT 0;

	DECLARE CONTINUE HANDLER FOR 1064 SET ERROR_FLG = '1';
	DECLARE CONTINUE HANDLER FOR 1065 SET ERROR_FLG = '1';

  	SET @sql1 = NULL;
  	
  	SET SESSION group_concat_max_len = 1000000;
  	
	call dev_bank_create (st_date, end_date, cur_value);

	SELECT
	  GROUP_CONCAT( 
	    DISTINCT CONCAT( 
	      'sum(case when date = '''
	      , date
	      , ''' then totamt else 0 end) AS `'
	      , date
	      , '`'
	    )
	  ) INTO @sql1 
	FROM
	  temp1; 

	SET @sql1 = CONCAT( 
	    'SELECT id,subid,date,Subject,sub_eng,Subject_jp,sub_jap, '
	    , @sql1
	    , ' 
		from temp1
		GROUP BY sub_eng ORDER BY Subject,sub_eng'
	  );
	   
	PREPARE stmt FROM @sql1;
	
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
