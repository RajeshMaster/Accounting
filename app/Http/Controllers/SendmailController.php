<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\Estimation;
use App\Model\Invoice;
use App\Model\SendingMail;
use DB;
use Input;
use Redirect;
use Session;
use FPDI_Protection;

ini_set('max_execution_time', 0);
class SendmailController extends Controller {

	function pdfEncrypts ($origFile, $password, $destFile, $request){
		//include the FPDI protection http://www.setasign.de/products/pdf-php-solutions/fpdi-protection-128/
		require_once('vendor/setasign/fpdiprotect/FPDI_Protection.php');
		$pdf = new FPDI_Protection();
		// set the format of the destinaton file, in our case 6×9 inch
		$pdf->FPDF('P', 'in');

		//calculate the number of pages from the original document
		$pagecount = $pdf->setSourceFile($origFile);

		// copy all pages from the old unprotected pdf in the new one
		for ($loop = 1; $loop <= $pagecount; $loop++) {
		    $tplidx = $pdf->importPage($loop);
		    $pdf->addPage();
		    $pdf->useTemplate($tplidx);
		}

		// protect the new pdf file, and allow no printing, copy etc and leave only reading allowed
		if ($request->nopassword != 1) {
			$pdf->SetProtection(array('print'),$password);
		}
		$pdf->Output($destFile, 'F');
	}
	function sendmailprocess(Request $request) { 
		$request->frmsendmail="1";
		$pdfname1="samplenamenew";
		$pdftotcnt=$request->pdftotcnt;
		$sendfiles[]="";
		$estimatenamemailflg[] = "";
		$request->attachments="";
		$request->pdfNames="";
		$normalpath= "resources/assets/uploadandtemplates/upload/".$request->sendmailfrom."/";
		$mailstatusid=Estimation::getautoincrement();
		if($request->mailstatusid!="") {
			$mailstatusid = $request->mailstatusid;
		}
		$mailstatuspath = "mailStatus".$mailstatusid."/";
		if(!is_dir($normalpath)){
			mkdir($normalpath, true);
		}
		if(!is_dir($normalpath."Protected_files/")){
			mkdir($normalpath."Protected_files/", true);
		}
		chmod($normalpath."Protected_files/", 0777); 
		$protectpath= $normalpath."Protected_files/".$mailstatuspath;
		if(!is_dir($protectpath)){
			mkdir($protectpath, true);
		}
		$filename="";
		$fileid="file1";
        if($request->$fileid != "") {
          $filename=$_FILES['file1']['name'];
          $file = $request->$fileid;
          $destinationPath = $protectpath;
          if(!is_dir($destinationPath)) {
            	mkdir($destinationPath, true);
            }
            chmod($destinationPath, 0777);
            $file->move($destinationPath,$filename);
            chmod($destinationPath."/".$filename, 0777);
        }
		if($request->mailstatusid=="") {
			chmod($protectpath, 0777); 
			for ($i=1; $i <= $pdftotcnt ; $i++) {
				// $pdfarr="pdfid".$i;
				$pdfnamearr="pdfname".$i;
				$estimatenamearr="estimatename".$i;
				$estimateflg = $request->$estimatenamearr;
				if($request->$estimateflg=="1") {
				// $request->estimate_id=$request->$pdfarr;
				// $filename=self::newpdf($request);
					if($request->$pdfnamearr=="") {
						$request->$pdfnamearr=$request->$estimatenamearr."_PROTECT";
					}
					$request->attachments.=$request->$estimatenamearr.",";
					$request->pdfNames.=$request->$pdfnamearr.",";
					$sendfiles[]=$request->$pdfnamearr;
					$estimatenamemailflg[]=$request->$estimatenamearr;
					//password for the pdf file
					$password = $request->pdfpassword;
					$origFile = glob($normalpath.$request->$estimatenamearr.'*');
					$origFile = end($origFile);
					$destFile = $protectpath.$request->$pdfnamearr.".pdf";
					//encrypt the book and create the protected file
					self::pdfEncrypts($origFile, $password, $destFile, $request);
				}
			}
		} else {
			$exdraftpdf = explode(",", $request->draftpdf);
			$exdraftattachments = explode(",", $request->draftattachments);
			for ($i=0; $i < count($exdraftattachments) ; $i++) { 
				$sendfiles[] = $exdraftpdf[$i];
				$estimatenamemailflg[] = $exdraftattachments[$i];
			}
		}
		if($request->$fileid != "") {
			$filename=$_FILES['file1']['name'];
		}
		// tomail final concept
		$splittolessmailfinal=array();
		$splittolessmailtofinalupdate="";
		$splitcclessmailfinal=array();
		$splitcclessmailccfinalupdate="";
		$splitto = explode(',', htmlspecialchars($request->tomail));
		for ($i=0; $i <count($splitto) ; $i++) {
			$splittoless = explode('&lt;', $splitto[$i]);
			if(isset($splittoless[1])) {
				$splittoless[1]=trim($splittoless[1] , " ");
				$splittolessmail=rtrim($splittoless[1] , "&gt;");
			} else {
				$splittolessmail=trim($splitto[$i] , " ");
			}
			array_push($splittolessmailfinal, $splittolessmail);
			if($splittolessmailtofinalupdate == "") {
				$splittolessmailtofinalupdate = $splitto[$i];
			} else {
				$splittolessmailtofinalupdate=$splittolessmailtofinalupdate.",".$splitto[$i];
			}
		}
		//---------------------
		// ccmail final concept
		if($request->ccname != "") {
			$splitcc = explode(',', htmlspecialchars($request->ccname));
			for ($i=0; $i <count($splitcc) ; $i++) {
				$splitccless = explode('&lt;', $splitcc[$i]);
				if(isset($splitccless[1])) {
					$splitccless[1]=rtrim($splitccless[1] , " ");
					$splitcclessmail=rtrim($splitccless[1] , "&gt;");
				} else {
					$splitcclessmail=trim($splitcc[$i] , " ");
				}
				array_push($splitcclessmailfinal, $splitcclessmail);
				if($splitcclessmailccfinalupdate == "") {
					$splitcclessmailccfinalupdate = $splitcc[$i];
				} else {
					$splitcclessmailccfinalupdate=$splitcclessmailccfinalupdate.",".$splitcc[$i];
				}
			}
		}
		//---------------------
		if($request->fordraft !="1") {
			$mailformat = [$request->content,$request->subject];
			$mailformatpwd = [str_replace('XXXXXXXX',$request->pdfpassword,$request->pwdcontent),$request->pwdsubject];
			$sendmail = SendingMail::sendIntimationMail($mailformat,$splittolessmailfinal,$request->subject,$splitcclessmailfinal,$protectpath,$sendfiles,$filename);
			if ($request->nopassword != 1 && $request->pdfcnt != 0) {
				$sendmailpwd = SendingMail::sendIntimationMail($mailformatpwd,$splittolessmailfinal,$request->pwdsubject,$splitcclessmailfinal);
			}
			if($sendmail) {
				if($request->sendmailfrom=="Estimation") {
					$mailflgup = Estimation::mailflgupdate($estimatenamemailflg);
				} else {
					$mailflgup = Invoice::mailflgupdate($estimatenamemailflg);
				}
				$mailflg=1;
				if($request->mailstatusid == "") {
					$done = Estimation::insertmailstatus($request,$splittolessmailfinal,$splitcclessmailfinal,$mailflg,$splitcclessmailccfinalupdate,$splittolessmailtofinalupdate);
				} else {
					$done = Estimation::updatemailstatus($request,$splittolessmailfinal,$splitcclessmailfinal,$mailflg);
					$request->sendmailfrom ="Mailstatus";
					$request->mainmenu ="mail";
				}
				if($done) {
		          Session::flash('success', 'Mail Send Successfully!'); 
		          Session::flash('type', 'alert-success'); 
		     	}
	        } else {
	          Session::flash('success', 'Mail Send Unsuccessful!'); 
	          Session::flash('type', 'alert-danger'); 
	        }
	    } else {
	    	//draft save
			$mailflg=0;
			$splitcclessmail="";
			$splittolessmail="";
			$done = Estimation::insertmailstatus($request,$splittolessmailfinal,$splitcclessmailfinal,$mailflg,$splitcclessmail,$splittolessmail);
			Session::flash('success', 'Mail Saved in Draft Successfully!'); 
			Session::flash('type', 'alert-success');
	    }
        Session::flash('selYear', $request->selYear); 
        Session::flash('selMonth', $request->selMonth); 
		return Redirect::to($request->sendmailfrom.'/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'));
	}
	
}