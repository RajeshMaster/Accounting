<?php

namespace App\Http\Common;

	class settingscommon {

		public static function getDbFieldsforProcess() {

			return array('mstbanks'=>array('labels'=>

												array('heading'=>trans('messages.lbl_bank_det_ind'),

												 	'field1lbl'=>trans('messages.lbl_bank_name')),

			    	  							 	'selectfields'=>array('id','BankName','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','BankName','Ins_DT',

		 										 							'delflg'),

		 										 	'insertfields'=>array('location','BankName',

		 										 						'delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('BankName','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('BankName')),

						// Bank in jp

						'mstbanks2'=>array('labels'=>

												array('heading'=>trans('messages.lbl_bank_det_jp'),

												 	'field1lbl'=>trans('messages.lbl_bank_name')),

			    	  							 	'selectfields'=>array('id','BankName','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','BankName','Ins_DT',

		 										 							'delflg'),

		 										 	'insertfields'=>array('location','BankName',

		 										 						'delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('BankName','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('BankName')),

						// Project Type

						'dev_estimatesetting'=>array('labels'=>

												array('heading'=>trans('messages.lbl_projecttype'),

												 	'field1lbl'=>trans('messages.lbl_projecttype')),

			    	  							 	'selectfields'=>array('id','ProjectType','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','ProjectType',

		 										 							'delflg'),

		 										 	'insertfields'=>array('ProjectType',

		 										 						'delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('ProjectType','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('ProjectType')),

						// Others

						'dev_estimate_others'=>array('labels'=>

												array('heading'=>trans('messages.lbl_oters'),

												 	'field1lbl'=>trans('messages.lbl_subject')),

			    	  							 	'selectfields'=>array('id','content','created_datetime','delflg'),

		 										 	'displayfields'=>array('id','content',

		 										 							'delflg'),

		 										 	'insertfields'=>array('content',

		 										 						'delflg','created_datetime','update_datetime','created_by','updated_by'),

		 										 	'updatefields'=>array('content','update_datetime','updated_by'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('content')),

						// Allowance

						'dev_allowancesetting'=>array('labels'=>

												array('heading'=>trans('messages.lbl_allowance'),

												 	'field1lbl'=>trans('messages.lbl_allowance')),

			    	  							 	'selectfields'=>array('id','Allowance','Ins_DT',

			    	  							 		'delflg'),

		 										 	'displayfields'=>array('id','Allowance',

		 										 							'delflg'),

		 										 	'insertfields'=>array('Allowance',

		 										 						'delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('Allowance','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('Allowance')),

						// Taxfree

						'dev_taxfreesetting'=>array('labels'=>

												array('heading'=>trans('messages.lbl_taxfree'),

												 	'field1lbl'=>trans('messages.lbl_taxfree')),

			    	  							 	'selectfields'=>array('id','taxfree','Ins_DT',

			    	  							 		'delflg'),

		 										 	'displayfields'=>array('id','taxfree',

		 										 							'delflg'),

		 										 	'insertfields'=>array('taxfree',

		 										 						'delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('taxfree','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('taxfree')),

						// Deduction

						'dev_deductionsetting'=>array('labels'=>

												array('heading'=>trans('messages.lbl_deduction'),

												 	'field1lbl'=>trans('messages.lbl_deduction')),

			    	  							 	'selectfields'=>array('id','deduction','Ins_DT',

			    	  							 		'delflg'),

		 										 	'displayfields'=>array('id','deduction',

		 										 							'delflg'),

		 										 	'insertfields'=>array('deduction',

		 										 						'delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('deduction','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('deduction')),

						// By Company 1

						'dev_bycompany1setting'=>array('labels'=>

												array('heading'=>trans('messages.lbl_bycompany1'),

												 	'field1lbl'=>trans('messages.lbl_bycompany1')),

			    	  								'selectfields'=>array('id','bycompany1','Ins_DT','delflg'),

		 											'displayfields'=>array('id','bycompany1','delflg'),

		 											'insertfields'=>array('bycompany1','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

													'updatefields'=>array('bycompany1','Up_DT','UpdatedBy'),

									 				'usenotusefields'=>array('delflg'),

													'commitfields'=>array('bycompany1')),

						// By Company 2

						'dev_bycompany2setting'=>array('labels'=>

												array('heading'=>trans('messages.lbl_bycompany2'),

												 	'field1lbl'=>trans('messages.lbl_bycompany2')),

			    	  							 	'selectfields'=>array('id','bycompany2','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','bycompany2','delflg'),

		 										 	'insertfields'=>array('bycompany2','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('bycompany2','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('bycompany2')),

						// End Single Field Popop

						// Start Two Field Popup

						// Other Status

						'inv_estimate_others'=>array('labels'=>

											   array('heading'=>trans('messages.lbl_Othersstatus'),

												 	'field1lbl'=>trans('messages.lbl_subjectinjapanese'),

													'field2lbl'=>trans('messages.lbl_subjectinenglish')),

			    	  							 	'selectfields'=>array('id','content_japanese',

			    	  							 		'content_english','created_datetime','delflg'),

		 										 	'displayfields'=>array('id','content_japanese',

		 										 		'content_english','delflg'),

		 										 	'insertfields'=>array('content_japanese',

		 										 		'content_english','delflg','created_datetime','update_datetime','created_by','updated_by'),

		 										 	'updatefields'=>array('content_japanese',

		 										 		'content_english','update_datetime','updated_by'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('content_japanese')),

						// Main Subject expense 

						'dev_expensesetting'=>array('labels'=>

											   array('heading'=>trans('messages.lbl_expensemainsubject'),

												 	'field1lbl'=>trans('messages.lbl_subjectinenglish'),

													'field2lbl'=>trans('messages.lbl_subjectinjapanese')),

			    	  							 	'selectfields'=>array('id','Subject',

			    	  							 		'Subject_jp','Ins_DT','delflg'),

			    	  							 	'selectboxfields'=>array('id','Subject',

			    	  							 		'Subject_jp','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','Subject',

		 										 		'Subject_jp','delflg'),

		 										 	'insertfields'=>array('Subject',

		 										 		'Subject_jp','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('Subject',

		 										 		'Subject_jp','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('Subject')),

						// Loan Type

						'inv_set_loantype'=>array('labels'=>

											   array('heading'=>trans('messages.lbl_loantype'),

												 	'field1lbl'=>trans('messages.lbl_loaninenglish'),

													'field2lbl'=>trans('messages.lbl_loaninjapanese')),

			    	  							 	'selectfields'=>array('id','loanEng',

			    	  							 		'loanJap','InsDT','delflg'),

		 										 	'displayfields'=>array('id','loanEng',

		 										 		'loanJap','delflg'),

		 										 	'insertfields'=>array('loanEng',

		 										 		'loanJap','delflg','InsDT','UpDT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('loanEng',

		 										 		'loanJap','UpDT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('loanEng')),

						// MainSubject Transfer

						'inv_set_transfermain'=>array('labels'=>

										 		array('heading'=>trans('messages.lbl_pettycashmainsubject'),

													'field1lbl'=>trans('messages.lbl_mainpettycashinenglish'),

													'field2lbl'=>trans('messages.lbl_mainpettycashinjapanese')),

			    	  							 	'selectfields'=>array('id','main_eng',

			    	  							 		'main_jap','Ins_DT','delflg'),

			    	  							 	'selectboxfields'=>array('id','main_eng',

			    	  							 		'main_jap','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','main_eng',

		 										 		'main_jap','delflg'),

		 										 	'insertfields'=>array('main_eng',

		 										 		'main_jap','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('main_eng',

		 										 		'main_jap','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('main_eng')),

						// Allowance

						'inv_set_contractallowance'=>array('labels'=>

											   array('heading'=>trans('messages.lbl_allowanceselection'),

												 	'field1lbl'=>trans('messages.lbl_allowanceinenglish'),

													'field2lbl'=>trans('messages.lbl_allowanceinjapanese')),

			    	  							 	'selectfields'=>array('id','allowance_eng',

			    	  							 		'allowance_jap','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','allowance_eng',

		 										 		'allowance_jap','delflg'),

		 										 	'insertfields'=>array('allowance_eng',

		 										 		'allowance_jap','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('allowance_eng',

		 										 		'allowance_jap','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('allowance_eng')),

						// Main Catagories

						'inv_set_salarymain'=>array('labels'=>

											   array('heading'=>trans('messages.lbl_maincatagories'),

													'field1lbl'=>trans('messages.lbl_maincategoriesinenglish'),

													'field2lbl'=>trans('messages.lbl_maincategoriesinjapanese')),

			    	  							 	'selectfields'=>array('id','main_eng',

			    	  							 		'main_jap','Ins_DT','delflg'),

			    	  							 	'selectboxfields'=>array('id','main_eng',

			    	  							 		'main_jap','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','main_eng',

		 										 		'main_jap','delflg'),

		 										 	'insertfields'=>array('main_eng',

		 										 		'main_jap','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('main_eng',

		 										 		'main_jap','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('main_eng')),

						// End Two Field Popop

						// Start Three Field Popup

						// bankbranch in india

						'mstbankbranch'=>array('labels'=>

												array('heading'=>trans('messages.lbl_branch_name_ind'),

			    	 								'field1lbl'=>trans('messages.lbl_bank_name'),

													'field2lbl'=>trans('messages.lbl_branch_name'),

													'field3lbl'=>trans('messages.lbl_branch_number')),

			    	  							 	'selectfields'=>array('id',

			    	  							 	'BankId','BranchName','BranchNo','delflg',

			    	  							 	'Ins_DT'),

		 										 	'displayfields'=>array('id','BankId','BranchName','BranchNo','delflg','Ins_DT'),

		 										 	'insertfields'=>array('BankId','BranchName',

		 										 		'BranchNo','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('BankId','BranchName','BranchNo','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('BranchName'),

		 										 	'selectboxfields'=>array('id',

		 										 	'BankName')),

						// bankbranch in jp

						'mstbankbranch2'=>array('labels'=>

												array('heading'=>trans('messages.lbl_branch_name_jp'),

			    	 								'field1lbl'=>trans('messages.lbl_bank_name'),

													'field2lbl'=>trans('messages.lbl_branch_name'),

													'field3lbl'=>trans('messages.lbl_branch_number')),

			    	  							 	'selectfields'=>array('id',

			    	  							 	'BankId','BranchName','BranchNo','delflg',

			    	  							 	'Ins_DT'),

		 										 	'displayfields'=>array('id','BankId','BranchName','BranchNo','delflg','Ins_DT'),

		 										 	'insertfields'=>array('BankId','BranchName',

		 										 		'BranchNo','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('BankId','BranchName','BranchNo','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('BranchName'),

		 										 	'selectboxfields'=>array('id',

		 										 	'BankName')),

						// Expense Sub

						'inv_set_expensesub'=>array('labels'=>

													array('heading'=>trans('messages.lbl_expensesubsubject'),

			    	 									'field1lbl'=>trans('messages.lbl_mainsubject'),

														'field2lbl'=>trans('messages.lbl_subjectinjapanese'),

														'field3lbl'=>trans('messages.lbl_subjectinenglish')),

			    	  							 		'selectfields'=>array('id',

			    	  							 			'mainid','sub_eng','sub_jap','delflg',

			    	  							 			'Ins_DT'),

		 										 		'displayfields'=>array('id','mainid','sub_eng', 'sub_jap','delflg','Ins_DT'),

		 										 		'insertfields'=>array('mainid','sub_eng',

		 										 			'sub_jap','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 		'updatefields'=>array('mainid','sub_eng','sub_jap','Up_DT','UpdatedBy'),

												 		'usenotusefields'=>array('delflg'),

		 										 		'commitfields'=>array('sub_eng'),

		 										 		'selectboxfields'=>array('id','mainid')),

						// Pettycash Sub

						'inv_set_transfersub'=>array('labels'=>

													array('heading'=>trans('messages.lbl_pettycashsubsubject'),

			    	 									'field1lbl'=>trans('messages.lbl_mainpettycash'),

														'field2lbl'=>trans('messages.lbl_subpettycashinenglish'),

														'field3lbl'=>trans('messages.lbl_subpettycashinjapanese')),

			    	  							 		'selectfields'=>array('id','mainid','sub_eng','sub_jap','delflg', 'Ins_DT'),

		 										 		'displayfields'=>array('id','mainid','sub_eng', 'sub_jap','delflg','Ins_DT'),

		 										 		'insertfields'=>array('mainid','sub_eng',

		 										 			'sub_jap','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 		'updatefields'=>array('mainid','sub_eng','sub_jap','Up_DT','UpdatedBy'),

												 		'usenotusefields'=>array('delflg'),

		 										 		'commitfields'=>array('sub_eng'),

		 										 		'selectboxfields'=>array('id','mainid')),

						// Categories Sub

						'inv_set_salarysub'=>array('labels'=>

													array('heading'=>trans('messages.lbl_subcategories'),

			    	 									'field1lbl'=>trans('messages.lbl_maincategories'),

														'field2lbl'=>trans('messages.lbl_subcategoriesinenglish'),

														'field3lbl'=>trans('messages.lbl_subcategoriesinjapanese')),

			    	  							 		'selectfields'=>array('id','mainid','sub_eng','sub_jap','delflg','Ins_DT'),

		 										 		'displayfields'=>array('id','mainid','sub_eng','sub_jap','delflg','Ins_DT'),

		 										 		'insertfields'=>array('mainid','sub_eng',

		 										 			'sub_jap','delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy'),

		 										 		'updatefields'=>array('mainid','sub_eng','sub_jap','Up_DT','UpdatedBy'),

												 		'usenotusefields'=>array('delflg'),

		 										 		'commitfields'=>array('sub_eng'),

		 										 		'selectboxfields'=>array('id','mainid')),

						// User Designation

						'sysdesignationtypes'=>array('labels'=>

											   array('heading'=>trans('messages.lbl_userdesignation'),

												 	'field1lbl'=>trans('messages.lbl_userdesignationinenglish'),

													'field2lbl'=>trans('messages.lbl_userdesignationinjapanese')),

			    	  							 	'selectfields'=>array('id','DesignationNM',

			    	  							 		'DesignationNMJP','Ins_DT','DelFlg'),

		 										 	'displayfields'=>array('id','DesignationNM',

		 										 		'DesignationNMJP','DelFlg'),

		 										 	'insertfields'=>array('DesignationNM',

		 										 		'DesignationNMJP','DelFlg','Ins_DT','Upd_DT','CreatedBy','UpdatedBy'),

		 										 	'updatefields'=>array('DesignationNM',

		 										 		'DesignationNMJP','Upd_DT','UpdatedBy'),

												 	'usenotusefields'=>array('DelFlg'),

		 										 	'commitfields'=>array('DesignationNM')),
						// Bank in jp

						'mstsalary'=>array('labels'=>

												array('heading'=>trans('messages.lbl_salary_det'),

												 	'field1lbl'=>trans('messages.lbl_salary_det')),

			    	  							 	'selectfields'=>array('id','Name','Ins_DT','delflg'),

		 										 	'displayfields'=>array('id','Name','Ins_DT',

		 										 							'delflg'),

		 										 	'insertfields'=>array('location','Name',

		 										 						'delflg','Ins_DT','Up_DT','CreatedBy','UpdatedBy','Salarayid'),

		 										 	'updatefields'=>array('Name','Up_DT','UpdatedBy'),

												 	'usenotusefields'=>array('delflg'),

		 										 	'commitfields'=>array('Name')),

						

			    );

		}

	}

?>