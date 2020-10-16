-- ----------------------------
-- View structure for `v_seq_ones`
-- ----------------------------
DROP VIEW IF EXISTS `v_seq_ones`;
CREATE VIEW `v_seq_ones` AS select 0 AS `seq` union all select 1 AS `seq` union all select 2 AS `seq` union all select 3 AS `seq` union all select 4 AS `seq` union all select 5 AS `seq` union all select 6 AS `seq` union all select 7 AS `seq` union all select 8 AS `seq` union all select 9 AS `seq`;

-- ----------------------------
-- View structure for `v_seq_tens`
-- ----------------------------
DROP VIEW IF EXISTS `v_seq_tens`;
CREATE VIEW `v_seq_tens` AS select 0 AS `seq` union all select 10 AS `seq` union all select 20 AS `seq` union all select 30 AS `seq` union all select 40 AS `seq` union all select 50 AS `seq` union all select 60 AS `seq` union all select 70 AS `seq` union all select 80 AS `seq` union all select 90 AS `seq`;

-- ----------------------------
-- View structure for `v_seq_hundreds`
-- ----------------------------
DROP VIEW IF EXISTS `v_seq_hundreds`;
CREATE VIEW `v_seq_hundreds` AS select 0 AS `seq` union all select 100 AS `seq` union all select 200 AS `seq` union all select 300 AS `seq` union all select 400 AS `seq` union all select 500 AS `seq` union all select 600 AS `seq` union all select 700 AS `seq` union all select 800 AS `seq` union all select 900 AS `seq`;

-- ----------------------------
-- View structure for `v_seq_0_to_1000`
-- ----------------------------
DROP VIEW IF EXISTS `v_seq_0_to_1000`;
CREATE VIEW `v_seq_0_to_1000` AS select ((`vsh`.`seq` + `vst`.`seq`) + `vso`.`seq`) AS `seq` from ((`v_seq_ones` `vso` join `v_seq_tens` `vst`) join `v_seq_hundreds` `vsh`);
