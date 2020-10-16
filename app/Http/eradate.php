<?php
namespace App\Http;
use Carbon\Carbon;
class Eradate{
	
	//明治  　　元号、略称、コード、開始日付、終了日付　　　　　　　　　　　　
	private static $meijiArr=array ("明治","M","1","1868/09/08","1912/07/29","明");
	//大正
	private static $taishoArr=array("大正","T","2","1912/07/30","1926/12/24","大");
	//昭和
	private static $showaArr=array ("昭和","S","3","1926/12/25","1989/01/07","昭");
	//平成
	private static $heiseiArr=array("平成","H","4","1989/01/08","","平");
	//時代範囲
	private static $eraYr=array("1868","1912","1926","1989");
	//現在の時代
	private static $CURRNENDO="H";

	//Created by Hari 2015-12-29
	public static function geteradate($date,$type){
		$res = Eradate::datetonendo($date,$type);
		$data= explode('/', $res);
		$substrvalue = substr($data[0], 0,1);
		if($substrvalue == self::$meijiArr[1]){
			$returnkanji = self::$meijiArr[0]."/".self::$meijiArr[5];
		} elseif($substrvalue == self::$taishoArr[1]){
			$returnkanji = self::$taishoArr[0]."/".self::$taishoArr[5];
		} elseif($substrvalue == self::$showaArr[1]){
			$returnkanji = self::$showaArr[0]."/".self::$showaArr[5];
		} elseif($substrvalue == self::$heiseiArr[1]){
			$returnkanji = self::$heiseiArr[0]."/".self::$heiseiArr[5];
		} else {
			$returnkanji =  " / ";
		}
		return $res."/".$returnkanji;
	}
	
 	/*********************************************************
  	** 関数名:date_to_nendo							      	**
  	** 処　理 :年度取得関数			          		        **
  	** 引  数  :$date=日付                                                                                                   **
  	**      :$type=出力形式　         1:24 2:H24/04  3:424         **
  	** 戻り値:フォーマットされた日付                          					  	**
  	** 履  歴 :2012-05-18 Pravin                              **
  	*********************************************************/
	public static function datetonendo($date,$type){
		$res="";
		if(!empty($date)){
			$len=strlen(trim($date));
			//H24/01/01
			if(preg_match( '/[-\.\/ ]/', $date) && $len>=6){
				if($len==6){
					//H24/04 format
					$date=$date."/01";
				}
				if(Eradate::checkvaliddate($date)){
					if(strlen(trim($date))==9){
						$date=Eradate::warekitoseireki($date);
						if(!empty($date)){
							list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date);
						}
					}else{
						list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date);
					} 
					
					if(!empty($year)){
							$res=Eradate::getnendo($date,$type);
					}
				}
			}
		}
	   return $res;
	}
	
  /*********************************************************
  ** 関数名:getnendo								          **
  ** 処　理 :年度取得処理	          		                  **
  ** 引  数  :$date=日付                                                                                                   **
  **      :$type=出力形式　         1:24 2:H24/04  3:424   5:H24 **
  ** 戻り値:フォーマットされた日付                          					  **
  ** 履  歴 :2012-05-18 Pravin                              **
  *********************************************************/
	private static function getnendo($date,$type){
		//H24/01/01
		list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date);
		if($month<=3){
			$year=$year-1;
		}
		$date=$year."/".$month."/".$day;
		
		switch($type){
		
			case 1:
				$date=substr(Eradate::seirekitowareki($date,""), 1,5);	
				break;
			case 2:
				$date=substr(Eradate::seirekitowareki($date,""), 0,6);
				break;
			case 3:
				$date=substr(Eradate::seirekitowareki($date,"2"), 0,6);
				break;
			case 4:
				$arrDate=explode("/",Eradate::seirekitowareki($date,"3",""));
				$date=$arrDate[0];
				if($date>=400){
					$date=substr($arrDate[0],1,2);	
				}
			case 5:
				$date=substr(Eradate::seirekitowareki($date,""), 0,3);
				break;
			case 6:
				$date=Eradate::seirekitowareki($date,"");	
				break;
			break;
		
		}
		//2012/04/01 format
		return $date;
	}
	
  /*********************************************************
  ** 関数名:checkvaliddate							      **
  ** 処　理 :有効な日付チェック	          		              **
  ** 引  数  :$date=日付                                                                                                   **
  ** 戻り値:フォーマットされた日付                          					  **
  ** 履  歴 :2012-05-18 Pravin                              **
  *********************************************************/
	private static function checkvaliddate($date){
		
		if(strlen($date)==10){
			$yr=substr($date, 0,4);
			$mnth=substr($date, 5,2);
			$day=substr($date, 8,2);
			
			if(is_numeric($yr) && is_numeric($mnth) && is_numeric($day)){
				return true;
			}
		}else {
			$sym=substr($date, 0,1);
			$yr=substr($date, 1,2);
			$mnth=substr($date, 4,2);
			$day=substr($date, 7,2);
			
			if(preg_match('/[a-zA-Z]/', $sym) && is_numeric($yr) && is_numeric($mnth)
				&& is_numeric($day)){
				return true;
			}
		}
		return false;
	}

	
	/*********************************************************
  	** 関数名:isNendo								      	**
  	** 処　理 :有効な年度をチェックする		          		        **
  	** 引  数  :$strNendo=年度  (H24)							**
  	** 戻り値:true/false 										**
  	** 履  歴 :2013/03/12 タミル                             			 			**
  	*********************************************************/
	public function isNendo($strNendo){
		$Nendo="";
		if($strNendo==''){
			return false;
		}
		
		if(ctype_alnum($strNendo)==false){
			return false;
		}
		
		if(strlen($strNendo)==1){
			if(!is_numeric($strNendo)){
				return false;
			}else{
				$Nendo=Eradate::$CURRNENDO.$strNendo;
			}
		}elseif(strlen($strNendo)==2){
			if(is_numeric($strNendo)){
				$Nendo=Eradate::$CURRNENDO.$strNendo;
			}else{
				if(!is_numeric(substr($strNendo,1,1))){
					return false;
				}
				$Nendo=$strNendo;
			}
		}elseif(strlen($strNendo)==3){
			if(!is_numeric(substr($strNendo,1,2))){
				return false;
			}
			$Nendo=$strNendo;
		}else{
			return false;
		}
		
		$resYear=Eradate::getyear($Nendo);
		if($resYear==""){
			return false;
		}
		return true;
	}
	
	/*********************************************************
  	** 関数名:getNendoFormated						      	**
  	** 処　理 :年度の形式を行う。		          		        **
  	** 引  数  :$nendo=年度 ,$formatFlg{1:24,2:424,3:h24,4:H24}	**
  	** 戻り値:24,424,h24,H24									**
  	** 履  歴 :2013/03/12 タミル                             			 			**
  	*********************************************************/
	public function getNendoFormated($nendo,$formatFlg){
		$len=strlen($nendo);
		
		$Ere=Eradate::$CURRNENDO;
		$Eyear=null;
		
		if($len==1){
			$Eyear=$nendo;
		}elseif($len==2){
			if(is_numeric($nendo)){
				$Eyear=$nendo;
			}else{
				$Ere=$nendo[0];
				$Eyear=$nendo[1];
			}
		}elseif($len==3){
			$Ere=$nendo[0];
			if(is_numeric($Ere)){
				if($Ere==Eradate::$meijiArr[2]){
					$Ere=Eradate::$meijiArr[1];
				}elseif($Ere==Eradate::$taishoArr[2]){
					$Ere=Eradate::$taishoArr[1];
				}elseif($Ere==Eradate::$showaArr[2]){
					$Ere=Eradate::$showaArr[1];
				}elseif($Ere==Eradate::$heiseiArr[2]){
					$Ere=Eradate::$heiseiArr[1];
				}
			}
			$Eyear=substr($nendo,1);
		}
		
		if($Eyear!=null){
			$Ere=strtoupper($Ere);
			$Eyear=str_pad($Eyear, 2, "0", STR_PAD_LEFT);
			
			if($formatFlg==1){//24
				if($Ere==Eradate::$CURRNENDO){
					$Ere="";
				}
			}elseif($formatFlg==2){//424
				if($Ere==Eradate::$meijiArr[1]){
					$Ere=Eradate::$meijiArr[2];
				}elseif($Ere==Eradate::$taishoArr[1]){
					$Ere=Eradate::$taishoArr[2];
				}elseif($Ere==Eradate::$showaArr[1]){
					$Ere=Eradate::$showaArr[2];
				}elseif($Ere==Eradate::$heiseiArr[1]){
					$Ere=Eradate::$heiseiArr[2];
				}
			}elseif($formatFlg==3){//h24
				$Ere=strtolower($Ere);
			}
			return $Ere.$Eyear;
		}
		return "";
	}

/*********************西暦→和暦変換関数  開始*********************/

	/*********************************************************
  	** 関数名:seirekitowareki								**
  	** 処　理:西暦→和暦変換関数								**
  	** 引  数 :$date=日付				                      	**
  	**      :$format=日付形式      1:昭和55年1月1日     2:4240101   **
  	**      :    3:24/01/01 現在日付   nullの場合  H24/01/01　　　   **	
  	**      :$separator 日付セパレーター  "-",".","/"            **
  	**      : nullの場合 "/"				                  	**
  	** 戻り値:フォーマットされた日付                          					  	**
  	** 履  歴:2012-05-17　Pravin	                            **
  	*********************************************************/
	public static function seirekitowareki($date,$format,$separator='/'){
		$res="";
		if(!empty($date)){
			list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date );
			if(checkdate($month,$day,$year)){
				$res=Eradate::getformat($format,$date,$separator);
			}
		}
		return $res;
	}
	
	/*********************************************************
	** 関数名:getformat									  	**
	** 処　理:該当する日付形式の関数を呼ぶ						**
	** 引  数 :$format=日付形式	                            **
	**      :$date=日付  									  	**
	**      :$separator 日付セパレーター  "-",".","/"            **
	**      : nullの場合 "/"				                  	**
	** 戻り値:フォーマットされた日付                          					  	**
	** 履  歴 :2012-05-17 Pravin		                        **
	*********************************************************/
	private static	function getformat($format,$date,$separator){
		$res="";
		switch($format){
			case 1:
				$res=Eradate::getera($format,$date,$separator);
				break;
			case 2:
				$res=Eradate::getera($format,$date,$separator);
				break;
			default:
			 	$res=Eradate::getera($format,$date,$separator);
			
		}
		return $res;
	}
	
  	/*********************************************************
  	** 関数名:getera										 	**
  	** 処　理:該当する元号の処理を行う							**
  	** 引  数 :$type=1:平成24年01月01日  2:コード1,2,3,4 H24/01/01**
  	**      :$date=日付  									  	**
  	**      :$separator 日付セパレーター  "-",".","/"            **
  	**      : nullの場合 "/"				                  	**
  	** 戻り値:フォーマットされた日付                          					  	**
  	** 履  歴 :2012-05-17 	Pravin                            	**
  	*********************************************************/
	private static function getera($type,$date,$separator){
		$posh = strpos($date, "-");
    	$poss = strpos($date, "/");
    	$posd = strpos($date, ".");
		$format=array("Y-m-d","Y/m/d","Y.m.d");
    	if($posh>0){
    		$dtType=$format[0];
    	}
    	if($poss>0){
    		$dtType=$format[1];
    	}
		if($posd>0){
    		$dtType=$format[2];
    	}
		
		//1.　開始年月日：1868/09/08　元号名称：明治   
		$era1 = Carbon::createFromFormat('Y/m/d',Eradate::$meijiArr[3]);
		//2.　開始年月日：1912/07/30　元号名称：大正
		$era2 = Carbon::createFromFormat('Y/m/d',Eradate::$taishoArr[3]);
		//3.　開始年月日：1926/12/25　元号名称：昭和    昭和55年1月1日
		$era3 = Carbon::createFromFormat('Y/m/d',Eradate::$showaArr[3]);
		//4.　開始年月日：1989/01/08　元号名称：平成
		$era4 = Carbon::createFromFormat('Y/m/d',Eradate::$heiseiArr[3]);
		
		$curDate=Carbon::createFromFormat($dtType, $date);
		if($type=="1"){
			$val=0;
		}elseif($type=="2"){
			$val=2;
		}else {
			$val=1;
		}
		
		if($curDate >=$era1 && $curDate< $era2){
			$era =Eradate::$meijiArr[$val];
			$date=Eradate::geteraformatted($era,Eradate::$eraYr[0],
				  $date,$type,$separator);
		}else if($curDate>=$era2 && $curDate< $era3){
			$era =Eradate::$taishoArr[$val];
			$date=Eradate::geteraformatted($era,Eradate::$eraYr[1],
				  $date,$type,$separator);
		}else if($curDate>=$era3 && $curDate<$era4){
			$era =Eradate::$showaArr[$val];
			$date=Eradate::geteraformatted($era,Eradate::$eraYr[2],
				  $date,$type,$separator);
		}else if($curDate>=$era4){
			$era =Eradate::$heiseiArr[$val];
			$date=Eradate::geteraformatted($era,Eradate::$eraYr[3],
				  $date,$type,$separator);
		}
		
		return 	$date;
	}
	
  /*********************************************************
  ** 関数名:geteraformatted								  **
  ** 処　理:	元号名称	または略称を付ける				          **
  ** 引  数 :$era=明治  || 大正 || 昭和 || 平成                                            **
  **	  :$eraYr= 元号開始年 1868,1912,1926,1989          **
  **	  :$date=日付	                                  **
  **      :$type=日付形式  								  **
  **      :$separator 日付セパレーター  "-",".","/"            **
  **      : nullの場合 "/"				                  **
  ** 戻り値:フォーマットされた日付                          					  **
  ** 履  歴 :2012-05-17 	Pravin                            **
  *********************************************************/
  private static function geteraformatted($era,$eraYr,$date,$type,$separator){
		list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date );
		$year=($year-$eraYr)+1;
		if($month<4){
			$year=$year-1;
		}
		$res="";
		switch ($type){
			case 1:
				//平成24年01月01日
				$res=$era.$year."年".$month."月".$day."日";
				break;
			case 2:
				//424/1/1
				$res=Eradate::setseparator($era.$year.
					 "/".$month."/".$day,"/",$separator);
				break;
			default:	
				//H24/01/01
				//if($era==Eradate::$heiseiArr[1] && $type=="3"){
					//$era="";
				//}
				$res=Eradate::setseparator($era.$year.
					 "/".$month."/".$day,"/",$separator);
		}
		return $res;
	}
	
  	/*********************************************************
  	** 関数名:setseparator								    **
  	** 処　理:	日付セパレーターを設定する処理			        **
  	** 引  数 :$date日付										**
  	**	  :$curSep=　現在の日付セパレーター                                                      	**
  	**	  :$separator 新しい日付セパレーター	                  	**
  	** 戻り値:フォーマットされた日付                          					  	**
  	** 履  歴 :2012-05-17 	Pravin                            	**
  	*********************************************************/
	private static function setseparator($date,$curSep,$separator){
		if(empty($separator) || ($separator!="/" && $separator!="." && $separator!="-")){
			$separator="/";
		}
		return str_replace($curSep, $separator, $date);
	}
	
/*********************西暦→和暦変換関数  完了*********************/

/*********************和暦→西暦変換関数  開始*********************/
	
	/*********************************************************
  	** 関数名:isWarekiDate							      	**
  	** 処　理 :有効な日付チェック(和暦)	          		        **
  	** 引  数  :$date=日付                                                                                        	**
  	** 戻り値:フォーマットされた日付                          					  	**
  	** 履  歴 :2013/03/12 タミル                             			 			**
  	*********************************************************/
	public static function isWarekiDate($date){
		if(!empty($date)){
			$len=strlen($date);
			$year=null;
			$month=null;
			$day=null;
			if(!preg_match( '/[-\.\/ ]/', $date)){
				if($len==7){
					$year=substr($date,0,3);
					$month=substr($date,3,2);
					$day=substr($date,5,2);
				}else if($len==6){
					$year=substr($date,0,2);
					$month=substr($date,2,2);
					$day=substr($date,4,2);
				}else if($len==5){
					$year=substr($date,0,1);
					$month=substr($date,1,2);
					$day=substr($date,3,2);
				}else{
					return false;
				}
			}elseif(true){
				list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date);
			}

			$year=trim($year);
			$month=trim($month);
			$day=trim($day);
			
			if($year!=null && $month!=null && $day!=null ){
				if(strlen($year)==1 && preg_match('/[1-9]/',$year)){
					$year=str_pad($year, 2, "0", STR_PAD_LEFT);
					$year=Eradate::$CURRNENDO.$year;
				}else if(strlen($year)==2){
					if(preg_match('/[a-zA-Z]/', substr($year, 0,1))){
					 	$year=substr($year, 0,1).str_pad(substr($year, 1), 2, "0", STR_PAD_LEFT);
					}elseif(preg_match('/[1-9]/',substr($year, 1,1))){
						$year=Eradate::$CURRNENDO.$year;
					}else{
						return false;
					}
				}else if(strlen($year)==3){
					if(preg_match('/[a-zA-Z]/', substr($year, 1,2))){
						return false;
					}
				}
					
				$month=str_pad($month, 2, "0", STR_PAD_LEFT);
				$day=str_pad($day, 2, "0", STR_PAD_LEFT);
				$date=$year."/".$month."/".$day;
				$res=Eradate::ryakushotosei($date);
				if($res==""){
					return false;
				}
			}
		}else{
			return false;
		}
		return Eradate::isSeirekiDate($res);
	}
	
	/*********************************************************
  	** 関数名:warekitoseireki								**
  	** 処　理 :和暦→西暦変換				    				**
  	** 引  数  :$date=日付                                                   					**
  	** 戻り値:フォーマットされた日付                          						**
  	** 履  歴 :2013/03/11 タミル                            						**
  	**********************************************************/
	public static function warekitoseireki($date){
		$retDate="";
		if(!empty($date)){
			$len=strlen($date);
			$year=null;
			$month=null;
			$day=null;
			if(!preg_match( '/[-\.\/ ]/', $date)){
				if($len==7){
					$year=substr($date,0,3);
					$month=substr($date,3,2);
					$day=substr($date,5,2);
				}else if($len==6){
					$year=substr($date,0,2);
					$month=substr($date,2,2);
					$day=substr($date,4,2);
				}else if($len==5){
					$year=substr($date,0,1);
					$month=substr($date,1,2);
					$day=substr($date,3,2);
				}else{
					return "";
				}
			}elseif(true){
				list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date);
			}

			$year=trim($year);
			$month=trim($month);
			$day=trim($day);
			
			if($year!=null && $month!=null && $day!=null ){
				if(strlen($year)==1 && preg_match('/[1-9]/',$year)){
					$year=str_pad($year, 2, "0", STR_PAD_LEFT);
					$year=Eradate::$CURRNENDO.$year;
				}else if(strlen($year)==2){
					if(preg_match('/[a-zA-Z]/', substr($year, 0,1))){
					 	$year=substr($year, 0,1).str_pad(substr($year, 1), 2, "0", STR_PAD_LEFT);
					}elseif(preg_match('/[1-9]/',substr($year, 1,1))){
						$year=Eradate::$CURRNENDO.$year;
					}else{
						return "";
					}
				}else if(strlen($year)==3){
					if(preg_match('/[a-zA-Z]/', substr($year, 1,2))){
						return "";
					}
				}
					
				$month=str_pad($month, 2, "0", STR_PAD_LEFT);
				$day=str_pad($day, 2, "0", STR_PAD_LEFT);
				$date=$year."/".$month."/".$day;
				$retDate="";
				$SeirekiDate=Eradate::ryakushotosei($date);
				if(Eradate::isSeirekiDate($SeirekiDate)){
					$retDate=$SeirekiDate;
				}
			}
		}
		return $retDate;
	}
		
	/*********************************************************
	** 関数名:ryakushotosei								  	**
	** 処　理 :略称から該当する関数 を呼ぶ	          		      	**
	** 引  数  :$date=日付                                                                                                   **
	** 戻り値:フォーマットされた日付                          					  	**
	** 履  歴 :2012-05-17 Pravin	                            **
	*********************************************************/		
	private static function ryakushotosei ($date){
		//CakeLog::write("log1", $date);
		$res="";
		$era=substr($date,0,1);
		$yr=substr($date,1,2);
		$era=strtoupper($era);
		if($era==Eradate::$meijiArr[1] || $era==Eradate::$meijiArr[2]){
			//1912-1868 =45
			if($yr<=45){
				$res=Eradate::getdatefromera(Eradate::$eraYr[0],$yr,$date);
			}
		}else if($era==Eradate::$taishoArr[1] || $era==Eradate::$taishoArr[2]){
			if($yr<=15){
				$res=Eradate::getdatefromera(Eradate::$eraYr[1],$yr,$date);
			}
		}else if($era==Eradate::$showaArr[1] || $era==Eradate::$showaArr[2]){
			if($yr<=64){
				$res=Eradate::getdatefromera(Eradate::$eraYr[2],$yr,$date);
			}
		}else if($era==Eradate::$heiseiArr[1] || $era==Eradate::$heiseiArr[2]){
			$res=Eradate::getdatefromera(Eradate::$eraYr[3],$yr,$date);
			//CakeLog::write("log1", $res);
		}
		return $res;		
	}
	
  	/*********************************************************
  	** 関数名:getdatefromera								  	**
	** 処　理 :	和暦日付から西暦日付 を取得処理  		            **
  	** 引  数  :$eraYr=和暦年 　　　　　　　　　　　　　　　　　　　　　　　　　　　**
  	**　　　　　:$yr= 年                                                                                          　　　　　**
  	**　　　　　:$date= 日付                                                                                             　**
  	** 戻り値:フォーマットされた日付                          					  	**
  	** 履  歴 :2012-05-17 Pravin	                            **
  	*********************************************************/	
	private static function getdatefromera($eraYr,$yr,$date){
		list($year, $month, $day) = preg_split( '/[-\.\/ ]/', $date );
			$res=(($eraYr+$yr)-1)."/".$month."/".$day;
			return $res;
	}
	
/*********************和暦→西暦変換関数  完了*********************/

	
 	/*********************************************************
  	** 関数名:nendotodate								   	**
  	** 処　理 :				          		                **
  	** 引  数  :$date=日付                                                                                                   **
  	** 戻り値:                          					  	**
  	** 履  歴 :2012-06-26 Amutha                              **
  	*********************************************************/
	public static function nendotodate($date){
		$res="";
		if(!empty($date)){
			$len=strlen($date);
			if(!preg_match( '/[-\.\/ ]/', $date)){
				if($len==3){
					$res=Eradate::getyear($date);
				}elseif($len==2){
					$date=Eradate::$CURRNENDO.$date;
					$res=Eradate::getyear($date);
				}
			}
		}
		return $res;
	}
 	
 	/*********************************************************
  	** 関数名:nendotodatecalc 							   	**
  	** 処　理 :	get year format H42/01->2013	            **
  	** 引  数  :$date=日付                                                                                                   **
  	** 戻り値: year                        					**
  	** 履  歴 :2013-01-21 		                              	**
  	**********************************************************/	
	public static function nendotodatecalc($date){
		$res="";
		if(!empty($date)){
			$s=explode("/",$date);
			$date=$s[0];
			$len=strlen($date);
			if(!preg_match( '/[-\.\/ ]/', $date)){
				if($len==3){
						//$era=substr($date, 0,1);
						$res=Eradate::getyear($date);
				}
				if($len==2){
					$date=Eradate::$CURRNENDO.$date;
					$res=Eradate::getyear($date);
				}
			}
			if($s[1]<=3){
				$res=$res+1;
			}
		}
		return $res;
	}
	
	/*********************************************************
  	** 関数名:getyear								       	**
  	** 処　理 :年度から年を取得     		                        **
  	** 引  数  :$Nendo=年度                                                                                                **
  	** 戻り値:YYYY	                  					  	**
  	** 履  歴 :2013/03/21 		タミル	                            **
  	**********************************************************/		
	private static function getyear ($Nendo){
		$res="";
		$era=substr($Nendo,0,1);
		$era=strtoupper($era);
		$yr=substr($Nendo,1,strlen($Nendo)-1);
		
		if($era==Eradate::$meijiArr[1] || $era==Eradate::$meijiArr[2]){
			//1912-1868 =45
			if($yr<=45){
				$res=Eradate::getyearformat(Eradate::$eraYr[0],$yr);
			}
		}else if($era==Eradate::$taishoArr[1] || $era==Eradate::$taishoArr[2]){
			if($yr<=15){
				$res=Eradate::getyearformat(Eradate::$eraYr[1],$yr);
			}
		}else if($era==Eradate::$showaArr[1] || $era==Eradate::$showaArr[2]){
			if($yr<=64){
				$res=Eradate::getyearformat(Eradate::$eraYr[2],$yr);
			}
		}else if($era==Eradate::$heiseiArr[1] || $era==Eradate::$heiseiArr[2]){
			$res=Eradate::getyearformat(Eradate::$eraYr[3],$yr);
		}
		return $res;		
	}
		
 	/*********************************************************
  	** 関数名:getdatefromera								  	**
  	** 処　理 :		                                        **
  	** 引  数  :$eraYr=和暦年 　　　　　　　　　　　　　　　　　　　　　　　　　　　**
  	**　　　　　:$yr= 年                                                                                          　　　　　**
  	** 戻り値:                        					    **
  	** 履  歴 :2012-06-26 Amutha	                            **
  	*********************************************************/	
	private static function getyearformat($eraYr,$yr){
		    $res=(($eraYr+$yr)-1);
			return $res;
	}	
	
  	/*********************************************************
  	** 関数名:getSystemYYYYMMDD							  	**
  	** 処　理 :		                                        **
  	** 引  数  :Current year calculation　　　　　　　　　　　　　　　　　　**
  	** 戻り値:                        					    **
  	** 履  歴 :2013-03-08 PRADEEP.J                           **
  	*********************************************************/	
	public static function getSystemYYYYMMDD(){
		$currentDate=date('Y/m/d');
		return $currentDate;
	}
	
  	/*********************************************************
  	** 関数名:isSeirekiDate							      	**
  	** 処　理 :有効な日付チェック(西暦)	          		        **
  	** 引  数  :$date=日付                                                                                                   **
  	** 戻り値:フォーマットされた日付                          					  	**
  	** 履  歴 :2012-05-18 Pravin                              **
  	*********************************************************/
	public static function isSeirekiDate($date){
		//CakeLog::write("log1", $date);
		list($year,$month, $day)= explode('[/.-]', $date);
		return checkdate($month,$day,$year);
	}
	
  	/*********************************************************
  	** 関数名:getDateByNendoMMDD						      	**
  	** 処　理 :日付チェック(西暦/和暦)			   		        **
  	** 引  数  :$kbn,$nendo,$mmdd                              **                                                  
  	** 戻り値: 日付                      					 				**
  	** 履  歴 :2013/03/11 Tamil	                            **
  	*********************************************************/
	public static function getDateByNendoMMDD($kbn,$nendo,$mmdd){
		//$kbn=1 西暦 else 和暦
		$month=null;
		$day=null;
		
		if(preg_match( '/[-\.\/ ]/', $mmdd)){
			list($month, $day)= explode('[/.-]', $mmdd);
		}elseif(strlen($mmdd)>2){
			$month=substr($mmdd, 0,strlen($mmdd)-2);
			$day=substr($mmdd, -2);
		}
		
		if($month==null || $day==null){
			return null; 
		}
		
		$year=self::nendotodate($nendo);
		//４月から３月
		if($month<4){
			$year=$year+1;
		}
		
		$month=str_pad($month, 2, "0", STR_PAD_LEFT);
		$day=str_pad($day, 2, "0", STR_PAD_LEFT);
		
		$SeirekiDate=$year.'/'.$month.'/'.$day;
		
		//CakeLog::write("log1", "1: ".$SeirekiDate);
		if(Eradate::isSeirekiDate($SeirekiDate)){
			//西暦
			if($kbn==1){
				return $SeirekiDate;
			}else{
				//和暦
				return $nendo.'/'.$month.'/'.$day;
			}
		}
		return null; 
	}
}	
?>