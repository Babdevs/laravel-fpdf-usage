<?php
use App\User;
use App\Applicant;
use App\Pin;
use App\Http\Requests\Admin\PinRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\Admin\ApplicantRequest;
use Anouar\Fpdf\Fpdf as baseFpdf;
class PDF extends baseFpdf
{
	private $number;
	function SetDash($black=false, $white=false)
    {
        if($black and $white)
            $s=sprintf('[%.3f %.3f] 0 d', $black*$this->k, $white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }

function setNumber($number){
$this->number = $number;

}

// Page header
function Header()
{
	
	 if($this->PageNo() == 1){
    // Logo
    $this->Image('images/yssb-form-logo.png',5,6);
	
    // Arial bold 15
    $this->SetFont('Arial','B',25);
    // Move to the right
    $this->Cell(28);
    // Title
	
	$this->SetTextColor(82,145,211);
    $this->Cell(30,10,'Document Header Goes here');
	$this->SetTextColor(0,0,0);
	$this->Ln(15);
	
		   
/****************************************************************/		   
	
	$this->SetX(145);
	$this->SetDash(); 
	$this->SetLineWidth(0.4);
   $this->SetDash(1, 1); //5mm on, 5mm off
    
	$this->SetFont('Arial','B',10);
	$this->Cell(30,10,'Applicant ID:');
	$this->Cell(30,10,$this->number,'B');
   $this->SetDash(); 
	
    // Line break
    $this->Ln(20);
	
		}
	
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',10);
    // Page number
	
    $this->Cell(0,10,$this->PageNo(),0,0,'C');
	
	$this->SetX(100);
	$this->SetY(-15);
	$this->SetFont('Arial','I',7);
	$this->Cell(50,10,'Yobe State Scholarships Board ',0,0,'L');
}
}

/****************   Model binding into route **************************/
Route::model('article', 'App\Article');
Route::model('articlecategory', 'App\ArticleCategory');
Route::model('language', 'App\Language');
Route::model('photoalbum', 'App\PhotoAlbum');
Route::model('photo', 'App\Photo');
Route::model('user', 'App\User');
Route::model('applicant', 'App\Applicant');
Route::pattern('id', '[0-9]+');
Route::pattern('slug', '[0-9a-z-_]+');

/***************    Site routes  **********************************/
Route::get('/', 'HomeController@index');
Route::get('home', 'HomeController@index');
Route::get('about', 'PagesController@about');
Route::get('contact', 'PagesController@contact');
Route::get('how-to-apply', 'PagesController@faqs');
Route::get('articles', 'ArticlesController@index');
Route::get('article/{slug}', 'ArticlesController@show');
Route::get('video/{id}', 'VideoController@show');
Route::get('photo/{id}', 'PhotoController@show');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

/***************    Admin routes  **********************************/
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function($id) {
	
	


    # Admin Dashboard
    Route::get('dashboard', 'Admin\DashboardController@index');

    # Language
    Route::get('language/data', 'Admin\LanguageController@data');
    Route::get('language/{language}/show', 'Admin\LanguageController@show');
    Route::get('language/{language}/edit', 'Admin\LanguageController@edit');
    Route::get('language/{language}/delete', 'Admin\LanguageController@delete');
    Route::resource('language', 'Admin\LanguageController');

    # Article category
    Route::get('articlecategory/data', 'Admin\ArticleCategoriesController@data');
    Route::get('articlecategory/{articlecategory}/show', 'Admin\ArticleCategoriesController@show');
    Route::get('articlecategory/{articlecategory}/edit', 'Admin\ArticleCategoriesController@edit');
    Route::get('articlecategory/{articlecategory}/delete', 'Admin\ArticleCategoriesController@delete');
    Route::get('articlecategory/reorder', 'ArticleCategoriesController@getReorder');
    Route::resource('articlecategory', 'Admin\ArticleCategoriesController');

    # Articles
    Route::get('article/data', 'Admin\ArticleController@data');
    Route::get('article/{article}/show', 'Admin\ArticleController@show');
    Route::get('article/{article}/edit', 'Admin\ArticleController@edit');
    Route::get('article/{article}/delete', 'Admin\ArticleController@delete');
    Route::get('article/reorder', 'Admin\ArticleController@getReorder');
    Route::resource('article', 'Admin\ArticleController');

    # Photo Album
    Route::get('photoalbum/data', 'Admin\PhotoAlbumController@data');
    Route::get('photoalbum/{photoalbum}/show', 'Admin\PhotoAlbumController@show');
    Route::get('photoalbum/{photoalbum}/edit', 'Admin\PhotoAlbumController@edit');
    Route::get('photoalbum/{photoalbum}/delete', 'Admin\PhotoAlbumController@delete');
    Route::resource('photoalbum', 'Admin\PhotoAlbumController');

    # Photo
    Route::get('photo/data', 'Admin\PhotoController@data');
    Route::get('photo/{photo}/show', 'Admin\PhotoController@show');
    Route::get('photo/{photo}/edit', 'Admin\PhotoController@edit');
    Route::get('photo/{photo}/delete', 'Admin\PhotoController@delete');
    Route::resource('photo', 'Admin\PhotoController');

    # Users
    Route::get('user/data', 'Admin\UserController@data');
    Route::get('user/{user}/show', 'Admin\UserController@show');
    Route::get('user/{user}/edit', 'Admin\UserController@edit');
    Route::get('user/{user}/delete', 'Admin\UserController@delete');
	Route::get('applicant/data', 'Applicant\ApplicantController@data');
	
	
    Route::resource('user', 'Admin\UserController');
	
	
	# Applicant
	
    Route::get('applicant/data', 'Admin\ApplicantController@data');
    Route::get('applicant/{applicant}/show', 'Admin\ApplicantController@show');
    Route::get('applicant/{applicant}/edit', 'Admin\ApplicantController@edit');
    Route::get('applicant/{applicant}/delete', 'Admin\ApplicantController@delete');
	Route::resource('applicant', 'Admin\ApplicantController');
	
	 # pin
    Route::get('generate_pin', 'Admin\PinController@index');
	Route::get('pin/store', 'Admin\PinController@store');
	
	Route::get('pin/excel', function()
{
	
$pins = Pin::select('serial', 'pin')->get();
Excel::create('pins', function($excel) use($pins) {
    $excel->sheet('Sheet 1', function($sheet) use($pins) {
        $sheet->fromArray($pins);
    });
})->export('xls');
	
});
	Route::resource('pin', 'Admin\PinController');

	
	/***********************Print****************************************/

	
Route::get('applicant/{id}/print', function($id)
{
	
				 
$user =User::find($id)->applicant;
	$firt_q =User::find($id)->firstq;
	$second_q =User::find($id)->secondq;
	$third_q =User::find($id)->thirdq;
	$fourth_q =User::find($id)->fourthq;
	
	$firtemployer =User::find($id)->firtemployer;
	$secondemployer =User::find($id)->secondemployer;
	$thirdemployer =User::find($id)->thirdemployer;
	$fourthemployer =User::find($id)->fourthemployer;
	
		$firstreferee=User::find($id)->firstreferee;
		$secondreferee=User::find($id)->secondreferee;
		$thirdreferee=User::find($id)->thirdreferee;
	 
	 $firstcertificate=User::find($id)->firstcertificate;
	 $secondcertificate=User::find($id)->secondcertificate;
	 $thirdcertificate=User::find($id)->thirdcertificate;
	 $fourthcertificate=User::find($id)->fourthcertificate;
	 
	   // Instanciation of inherited class
        $pdf = new PDF();
		$pdf->setNumber($user->applicant_no );
		
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,'APPLICATION FORM AWARD OF YOBE STATE GOVERNMENT SCHOLARSHIP',0,1);
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(0,5,'Read the following instructions',0,1);
		$pdf->Cell(0,5,'Carefully before completing the form',0,1);
		$pdf->SetFont('Times','B',12);
		
		$pdf->Cell(0,30,'INSTRUCTIONS',0,1);
	/***************************************************************************************/	
	$message1 = "Applicant should complete this form online, a copy of this form is to be printed and presented to the Board on the day of interview."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '1.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message1);
			$pdf->Ln(10);
			
	/****************************************************************************************************************************/

	$message2 = "On the day of interview an applicant must appear before the Board with the originals and photocopies of the following documents."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '2.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message2);
			
			$pdf->Ln(5);
			$pdf->Setx(15);
			$pdf->SetFont('Arial', 'B', 10); 
            $pdf->Cell(7, 3, '(a)'); 
			$pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(0, 5,'All Certificates and Testimonials,etc',0,1);
			
			
			$pdf->Ln(2);
			$pdf->Setx(15);
			$pdf->SetFont('Arial', 'B', 10); 
            $pdf->Cell(7, 3, '(b)'); 
			$pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(0, 5,'Birth Certificate or Statutory Declaration of Age',0,1);
			
			$pdf->Ln(2);
			$pdf->Setx(15);
			$pdf->SetFont('Arial', 'B', 10); 
            $pdf->Cell(7, 3, '(c)'); 
			$pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(0, 5,'Admission Letter and Registration Receipt.',0,1);


			
			$pdf->Ln(10);
/****************************************************************************************************************************/

	$message3 = "Two passports size photograph with name and normal signature of the applicant at the back."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '3.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message3);
			$pdf->Ln(10);


/**************************************************************************************************************************************/	
$message4 = "Where an applicant is an employee of Federal, State or Local Government he/she must present a letter or release on leave without pay or acceptance of resignation from service."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '4.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message4);
			
			$pdf->Ln(10);

/**************************************************************************************************************************************/
$message5 = "An applicant must ensure that the Local Government Indigenisation is signed and stamped by the Chairman of his/her Local Government Authority, Candidate who fails to comply will not be interviewed."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '5.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message5);
			
			$pdf->Ln(10);

/**************************************************************************************************************************************/

$message6 = " Where there is a Change of Name, applicant is strongly advised to go to a Court of Law and swear to an affidavit to effect."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '6.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message6);
			
			$pdf->Ln(10);

/**************************************************************************************************************************************/	
	
      
		 $message7 = "An alteration(s) on a credential must be countersigned stamped by the officer issuing the documents. (FAILURE TO COMPLY WOULD RENDER THE DOCUMENT(S) INVALID)"; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '7.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message7);
			
			$pdf->Ln(10);

	
/*******************************************************************************************************/

		 
		
		$pdf->AddPage();
		
		$pdf->Image('images/applicants/'.$user->filename,10,0,30);
		$pdf->SetX(100);
		
		
		$pdf->SetFont('Arial', 'B', 13); 
		$pdf->Cell(20,10,'Name:');
        $pdf->SetFont('Arial','I',10); 
		$pdf->Cell(25,10,User::find($id)->name,'B');
		$pdf->SetDash();
		
		$pdf->SetX(150);
		
		$pdf->SetFont('Arial', 'B', 13); 
		$pdf->Cell(30,10,'Surname:');
        $pdf->SetFont('Arial','I',10); 
		$pdf->Cell(25,10,User::find($id)->surname,'B');
		; 
   
		$pdf->Ln(20);
		$pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(40, 15, 'Date Of Birth:'); 
         $pdf->SetFont('Arial','I',10); 
         $pdf->Cell(30,12,$user->date_of_birth, 'B');
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(40, 15, 'Phone Number:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,User::find($id)->phone , 'B');
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(28, 15, 'Gender:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(20, 12,$user->gender, 'B',1);
		 $pdf->Ln(7);
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(40, 15, 'Place Of Birth:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(35, 12,$user->place_of_biirth, 'B');
		 
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(70, 15, 'Local Government Of Origin:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(45, 12,$user->lga , 'B',1);
		 $pdf->Ln(7);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(43, 15, 'State Of Origin:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->state , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Father'."'".'s Tribe:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(65, 12,$user->ftribe , 'B',1);
		 $pdf->Ln(7);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(33, 15,'Father'."'".'s L.G.A:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(35, 12,$user->lga , 'B');
		 
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(33, 15,'Father'."'".'s State:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,$user->state , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Mother'."'".'s Tribe:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,$user->mtribe , 'B',1);
		 
		 $pdf->Ln(7);
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Mother'."'".'s L.G.A:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(60, 12,$user->mlga , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Mother'."'".'s State:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->state , 'B',1);
		 
		 $pdf->Ln(10);
		
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(65, 15,'Applicant'."'".'s Present Address:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 132, 15, $user->address, 'B',1);
		 
		 $pdf->Ln(10);
		 
		   $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(70, 15,'Parent Or Guardian'."'".'s Address:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 130, 15, $user->paddress, 'B',1);
		 
		  $pdf->Ln(10);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(120, 15,'Have You Previously Applied For Scholarship?:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 40, 15, $user->applied_or_not, 'B',1);
			
			$pdf->Ln(5);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(125, 15,'(If yes give Date and File Number of Scholarship Award)'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 70, 15, $user->reason_applied, 'B');
		 
		 $pdf->Ln(15);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(100, 15,'Are you under any employment or contract?'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 20, 15, $user->contract_or_not, 'B');
		 $pdf->Ln(8);
		 
		 $pdf->Ln(12);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(75, 15,'(If yes give detail of employer)'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 110, 15, $user->details_of_employer, 'B');
		 
		 $pdf->Ln(15);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(125, 15,'Have you ever been convicted  by any court of law?'); 
		 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 25, 15, $user->convicted, 'B');
		 
		 $pdf->Ln(10);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(75, 15,'( If so Give Details)'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 110, 15, $user->details_of_conviction, 'B');
		 /*****************************************************************************************/
		 
		  $pdf->AddPage();
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Course Of Study:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->course_study , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Duration Of course:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->duration_of_course , 'B',1);
		 
		 $pdf->Ln(13);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Institution Type:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->it , 'B');
		 
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(50, 15,'Name Of Institution:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->noi , 'B',1);
		 $pdf->Ln(13);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Level:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,$user->ilevel , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(25, 15,'Session:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(90, 12,$user->session , 'B',1);
		 
		 $pdf->Ln(13);
		
		$pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(150, 15,'Proposed Occupation On Completion Of Course Or Next Course Of Study:'); 
		 $pdf->Ln(13);
		
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(140, 12,$user->p_occupation , 'B',1);
		 
		 $pdf->SetFont('Arial', 'B', 13); 
          $pdf->Ln(30);
		 $pdf->multicell(170,6,'Particulars Of Educational Institutions Attended ,Qualifications Obtained and Examination taken :');
		 $pdf->Ln(13);
		 
		
		$pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(80, 15,$firt_q->school); 
		 $pdf->Cell(40, 15,$firt_q->from.'--'.$firt_q->to); 
		 $pdf->Cell(70, 15,$firt_q->certificate);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(80, 15,$second_q->school); 
		 $pdf->Cell(40, 15,$second_q->from.'--'.$second_q->to); 
		 $pdf->Cell(70, 15,$second_q->certificate);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(80, 15,$third_q->school); 
		 $pdf->Cell(40, 15,$third_q->from.'--'.$third_q->to); 
		 $pdf->Cell(70, 15,$third_q->certificate);
		 
		 $pdf->Ln(13);
		  $pdf->Cell(5, 15,'4.'); 
         $pdf->Cell(80, 15,$fourth_q->school); 
		 $pdf->Cell(40, 15,$fourth_q->from.'--'.$fourth_q->to); 
		 $pdf->Cell(70, 15,$fourth_q->certificate);
		
		
		 $pdf->AddPage();

         $pdf->SetFont('Arial', 'B', 13); 
		 $pdf->multicell(170,6,'Particulars Of Employment Since Leaving School:');
	
		 
		
		$pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(80, 15,$firtemployer->name); 
		 $pdf->Cell(40, 15,$firtemployer->from.'--'.$firtemployer->to); 
		 $pdf->Cell(70, 15,$firtemployer->position);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(80, 15,$secondemployer->name); 
		 $pdf->Cell(40, 15,$secondemployer->from.'--'.$secondemployer->to); 
		 $pdf->Cell(70, 15,$secondemployer->position);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(80, 15,$thirdemployer->name); 
		 $pdf->Cell(40, 15,$thirdemployer->from.'--'.$thirdemployer->to); 
		 $pdf->Cell(70, 15,$thirdemployer->position);
		 
		 $pdf->Ln(13);
		 $pdf->Cell(5, 15,'4.'); 
         $pdf->Cell(80, 15,$fourthemployer->name); 
		 $pdf->Cell(40, 15,$fourthemployer->from.'--'.$fourthemployer->to); 
		 $pdf->Cell(70, 15,$fourthemployer->name);
		 /***************************************************************************************/
		 $pdf->Ln(30); 
		 
		   $pdf->SetFont('Arial', 'B', 13); 
		 $pdf->multicell(170,6,'Name, Phone Number And The Addresses Of Three Referees(NOT RELATIONS) :');
		 
		
		 $pdf->Ln(13);
		 $pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(50, 15, $firstreferee->name); 
		 $pdf->Cell(40, 15,$firstreferee->referee_phone); 
		 $pdf->Cell(80, 15,$firstreferee->referee_address);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(50, 15,$secondreferee->name); 
		 $pdf->Cell(40, 15,$secondreferee->referee_phone); 
		 $pdf->Cell(80, 15,$secondreferee->referee_address);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(50, 15,$thirdreferee->name); 
		 $pdf->Cell(40, 15,$thirdreferee->referee_phone); 
		 $pdf->Cell(80, 15,$thirdreferee->referee_address);
		 
        $pdf->Ln(20);
		 /***************************************************************************************/
		  $pdf->SetFont('Arial', 'B', 13); 
		 $pdf->multicell(170,6,'Details Of Documents Attatched To Application :');
		 $pdf->Ln(13);
		 
		 
		$pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(50, 15, $firstcertificate->certificateno); 
		 $pdf->Cell(40, 15,$firstcertificate->date); 
		 $pdf->Cell(80, 15,$firstcertificate->discription);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(50, 15,$secondcertificate->certificateno); 
		 $pdf->Cell(40, 15,$secondcertificate->date); 
		 $pdf->Cell(80, 15,$secondcertificate->discription);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(50, 15,$thirdcertificate->certificateno); 
		 $pdf->Cell(40, 15,$thirdcertificate->date); 
		 $pdf->Cell(80, 15,$thirdcertificate->discription);
		 
		 $pdf->Ln(13);
		  $pdf->Cell(5, 15,'4.'); 
         $pdf->Cell(50, 15,$fourthcertificate->certificateno); 
		 $pdf->Cell(40, 15,$fourthcertificate->date); 
		 $pdf->Cell(80, 15,$fourthcertificate->discription);
		 
		  $pdf->AddPage();
		 $pdf->SetFont('Arial', 'B', 10);
		$pdf->MultiCell(0, 5, 'TO BE COMPLETED BY TOP CIVIL SERVANT(NOT BELOW GL 14)OR MILITARY OFFICER(NOT BELLOW THE RANK OF CAPTAIN OR EQUIVALENT)APPLICATION IN SERVICE NEED NOT FILL THIS SECTION ');
		
			$pdf->Ln(15);
	        $pdf->SetX(130);
			$pdf->SetDash(); 
			$pdf->SetLineWidth(0.4);
			$pdf->SetDash(1, 1); 
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(23,8,'ADDRESS:');
			$pdf->Cell(55,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(80,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(80,8,'','B',1);
			$pdf->SetDash(); 
			
			
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(35,8,'The Executive Chairman,',0,1);
			
			$pdf->Cell(50,8,'Yobe State Scholarship Board,',0,1);
			
			$pdf->Cell(35,8,'PMB 1048,',0,1);
			$pdf->Cell(30,8,'Damaturu.',0,1);
			
			$pdf->SetFont('Arial','BU',12);
			$pdf->SetX(90);
			$pdf->Cell(35,8,'IDENTIFICATION',0,1);
			
			$pdf->SetFont('Arial','B',10);
		
			
			$pdf->SetDash(); 
			$pdf->SetLineWidth(0.4);
			$pdf->SetDash(1, 1); 
			$pdf->Cell(7, 8, '1.'); 
	        $pdf->Cell(35,8,'Alh/Mal/Mr/Mrs:,',0,0);
			$pdf->Cell(115,5,'','B');
			$pdf->Cell(100,5,'DO HEREBY CERTIFY',0,1);
			$pdf->Cell(65,20,'in my honour that the applicant name');
			$pdf->Cell(130,12,'','B',1);
			$pdf->Cell(40,20,'is the Son/Daughter of');
			$pdf->Cell(100,12,'','B');
			$pdf->Cell(30,20,'and was born at');
			$pdf->Cell(30,12,'','B',1);
			$pdf->Cell(80,12,'','B');
			$pdf->Cell(30,20,'Town/Village of');
			$pdf->Cell(45,12,'','B');
			$pdf->Cell(30,20,'L.G.A and is a bonafide',0,1);
			$pdf->Cell(30,1,'Indigene Of Yobe State.',0,1);
			$pdf->Ln(10);
			$msg1 = "That he/she is no an employee of any Government Department/Ministry or Agency."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '2.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $msg1);
			
			$msg3 = "Should this identification provided incorrect thereafter, I accept full responsibility for the misinformation and i am prepared to refund all monies that might have been expended by Government on the cadidate identified plus any other legal action the Government might wish to institute against me."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '3.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $msg3);
			$pdf->Ln(20);
			
			$pdf->SetX(130);
			$pdf->SetDash(); 
			$pdf->SetLineWidth(0.4);
			$pdf->SetDash(1, 1); 
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(28,8,'SIGNATURE:');
			$pdf->Cell(50,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(15,15,'DATE:');
			$pdf->Cell(65,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(30,15,'OCCUPATION:');
			$pdf->Cell(50,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(15,15,'RANK:');
			$pdf->Cell(65,8,'','B',1);
			$pdf->Ln(5);
			$pdf->SetX(130);
			$pdf->Cell(35,15,'OFFICIAL STAMP:');
			$pdf->Cell(45,8,'','B',1);
			
			
	
			
			$pdf->Output();
        exit;
		
		
	});	
		/*************************************************End Print***************************************/
});
Route::group(['prefix' => 'applicant', 'middleware' => 'auth'], function() {
	
	 # Applicant profile
	 
    Route::get('create', 'Applicant\ApplicantController@create');
	 Route::post('registration', 'Applicant\ApplicantController@store');
	 
	 
	 Route::get('profile/{id}/print', function($id)
{
	if(Auth::user()->id==$id)
	{
				 
				 
	$user =User::find($id)->applicant;
	$firt_q =User::find($id)->firstq;
	$second_q =User::find($id)->secondq;
	$third_q =User::find($id)->thirdq;
	$fourth_q =User::find($id)->fourthq;
	
	$firtemployer =User::find($id)->firtemployer;
	$secondemployer =User::find($id)->secondemployer;
	$thirdemployer =User::find($id)->thirdemployer;
	$fourthemployer =User::find($id)->fourthemployer;
	
		$firstreferee=User::find($id)->firstreferee;
		$secondreferee=User::find($id)->secondreferee;
		$thirdreferee=User::find($id)->thirdreferee;
	 
	 $firstcertificate=User::find($id)->firstcertificate;
	 $secondcertificate=User::find($id)->secondcertificate;
	 $thirdcertificate=User::find($id)->thirdcertificate;
	 $fourthcertificate=User::find($id)->fourthcertificate;
	 

	 
   
	   // Instanciation of inherited class
        $pdf = new PDF();
		$pdf->setNumber($user->applicant_no );
		
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,'APPLICATION FORM AWARD OF YOBE STATE GOVERNMENT SCHOLARSHIP',0,1);
		$pdf->SetFont('Times','B',11);
		$pdf->Cell(0,5,'Read the following instructions',0,1);
		$pdf->Cell(0,5,'Carefully before completing the form',0,1);
		$pdf->SetFont('Times','B',12);
		
		$pdf->Cell(0,30,'INSTRUCTIONS',0,1);
	/***************************************************************************************/	
	$message1 = "Applicant should complete this form online, a copy of this form is to be printed and presented to the Board on the day of interview."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '1.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message1);
			$pdf->Ln(10);
			
	/****************************************************************************************************************************/

	$message2 = "On the day of interview an applicant must appear before the Board with the originals and photocopies of the following documents."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '2.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message2);
			
			$pdf->Ln(5);
			$pdf->Setx(15);
			$pdf->SetFont('Arial', 'B', 10); 
            $pdf->Cell(7, 3, '(a)'); 
			$pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(0, 5,'All Certificates and Testimonials,etc',0,1);
			
			
			$pdf->Ln(2);
			$pdf->Setx(15);
			$pdf->SetFont('Arial', 'B', 10); 
            $pdf->Cell(7, 3, '(b)'); 
			$pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(0, 5,'Birth Certificate or Statutory Declaration of Age',0,1);
			
			$pdf->Ln(2);
			$pdf->Setx(15);
			$pdf->SetFont('Arial', 'B', 10); 
            $pdf->Cell(7, 3, '(c)'); 
			$pdf->SetFont('Arial', 'B', 9);
            $pdf->MultiCell(0, 5,'Admission Letter and Registration Receipt.',0,1);


			
			$pdf->Ln(10);
/****************************************************************************************************************************/

	$message3 = "Two passports size photograph with name and normal signature of the applicant at the back."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '3.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message3);
			$pdf->Ln(10);


/**************************************************************************************************************************************/	
$message4 = "Where an applicant is an employee of Federal, State or Local Government he/she must present a letter or release on leave without pay or acceptance of resignation from service."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '4.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message4);
			
			$pdf->Ln(10);

/**************************************************************************************************************************************/
$message5 = "An applicant must ensure that the Local Government Indigenisation is signed and stamped by the Chairman of his/her Local Government Authority, Candidate who fails to comply will not be interviewed."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '5.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message5);
			
			$pdf->Ln(10);

/**************************************************************************************************************************************/

$message6 = " Where there is a Change of Name, applicant is strongly advised to go to a Court of Law and swear to an affidavit to effect."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '6.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message6);
			
			$pdf->Ln(10);

/**************************************************************************************************************************************/	
	
      
		 $message7 = "An alteration(s) on a credential must be countersigned stamped by the officer issuing the documents. (FAILURE TO COMPLY WOULD RENDER THE DOCUMENT(S) INVALID)"; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '7.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $message7);
			
			$pdf->Ln(10);

	
/*******************************************************************************************************/

		 
		
		$pdf->AddPage();
		
		$pdf->Image('images/applicants/'.$user->filename,10,0,30);
		$pdf->SetX(100);
		
		
		$pdf->SetFont('Arial', 'B', 13); 
		$pdf->Cell(20,10,'Name:');
        $pdf->SetFont('Arial','I',10); 
		$pdf->Cell(25,10,User::find($id)->name,'B');
		$pdf->SetDash();
		
		$pdf->SetX(150);
		
		$pdf->SetFont('Arial', 'B', 13); 
		$pdf->Cell(30,10,'Surname:');
        $pdf->SetFont('Arial','I',10); 
		$pdf->Cell(25,10,User::find($id)->surname,'B');
		; 
   
		$pdf->Ln(20);
		$pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(40, 15, 'Date Of Birth:'); 
         $pdf->SetFont('Arial','I',10); 
         $pdf->Cell(30,12,$user->date_of_birth, 'B');
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(40, 15, 'Phone Number:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,User::find($id)->phone , 'B');
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(28, 15, 'Gender:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(20, 12,$user->gender, 'B',1);
		 $pdf->Ln(7);
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(40, 15, 'Place Of Birth:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(35, 12,$user->place_of_biirth, 'B');
		 
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(70, 15, 'Local Government Of Origin:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(45, 12,$user->lga , 'B',1);
		 $pdf->Ln(7);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(43, 15, 'State Of Origin:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->state , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Father'."'".'s Tribe:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(65, 12,$user->ftribe , 'B',1);
		 $pdf->Ln(7);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(33, 15,'Father'."'".'s L.G.A:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(35, 12,$user->lga , 'B');
		 
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(33, 15,'Father'."'".'s State:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,$user->state , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Mother'."'".'s Tribe:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,$user->mtribe , 'B',1);
		 
		 $pdf->Ln(7);
		 $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Mother'."'".'s L.G.A:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(60, 12,$user->mlga , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(35, 15,'Mother'."'".'s State:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->state , 'B',1);
		 
		 $pdf->Ln(10);
		
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(65, 15,'Applicant'."'".'s Present Address:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 132, 15, $user->address, 'B',1);
		 
		 $pdf->Ln(10);
		 
		   $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(70, 15,'Parent Or Guardian'."'".'s Address:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 130, 15, $user->paddress, 'B',1);
		 
		  $pdf->Ln(10);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(120, 15,'Have You Previously Applied For Scholarship?:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 40, 15, $user->applied_or_not, 'B',1);
			
			$pdf->Ln(5);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(125, 15,'(If yes give Date and File Number of Scholarship Award)'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 70, 15, $user->reason_applied, 'B');
		 
		 $pdf->Ln(15);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(100, 15,'Are you under any employment or contract?'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 20, 15, $user->contract_or_not, 'B');
		 $pdf->Ln(8);
		 
		 $pdf->Ln(12);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(75, 15,'(If yes give detail of employer)'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 110, 15, $user->details_of_employer, 'B');
		 
		 $pdf->Ln(15);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(125, 15,'Have you ever been convicted  by any court of law?'); 
		 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 25, 15, $user->convicted, 'B');
		 
		 $pdf->Ln(10);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(75, 15,'( If so Give Details)'); 
		  $pdf->SetFont('Arial','I',10);
		 $pdf->Cell( 110, 15, $user->details_of_conviction, 'B');
		 /*****************************************************************************************/
		 
		  $pdf->AddPage();
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Course Of Study:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->course_study , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Duration Of course:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->duration_of_course , 'B',1);
		 
		 $pdf->Ln(13);
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Institution Type:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->it , 'B');
		 
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(50, 15,'Name Of Institution:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(50, 12,$user->noi , 'B',1);
		 $pdf->Ln(13);
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(45, 15,'Level:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(30, 12,$user->ilevel , 'B');
		 
		  $pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(25, 15,'Session:'); 
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(90, 12,$user->session , 'B',1);
		 
		 $pdf->Ln(13);
		
		$pdf->SetFont('Arial', 'B', 13); 
         $pdf->Cell(150, 15,'Proposed Occupation On Completion Of Course Or Next Course Of Study:'); 
		 $pdf->Ln(13);
		
		 $pdf->SetFont('Arial','I',10);
		 $pdf->Cell(140, 12,$user->p_occupation , 'B',1);
		 
		 $pdf->SetFont('Arial', 'B', 13); 
          $pdf->Ln(30);
		 $pdf->multicell(170,6,'Particulars Of Educational Institutions Attended ,Qualifications Obtained and Examination taken :');
		 $pdf->Ln(13);
		 
		
		$pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(80, 15,$firt_q->school); 
		 $pdf->Cell(40, 15,$firt_q->from.'--'.$firt_q->to); 
		 $pdf->Cell(70, 15,$firt_q->certificate);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(80, 15,$second_q->school); 
		 $pdf->Cell(40, 15,$second_q->from.'--'.$second_q->to); 
		 $pdf->Cell(70, 15,$second_q->certificate);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(80, 15,$third_q->school); 
		 $pdf->Cell(40, 15,$third_q->from.'--'.$third_q->to); 
		 $pdf->Cell(70, 15,$third_q->certificate);
		 
		 $pdf->Ln(13);
		  $pdf->Cell(5, 15,'4.'); 
         $pdf->Cell(80, 15,$fourth_q->school); 
		 $pdf->Cell(40, 15,$fourth_q->from.'--'.$fourth_q->to); 
		 $pdf->Cell(70, 15,$fourth_q->certificate);
		
		
		 $pdf->AddPage();

         $pdf->SetFont('Arial', 'B', 13); 
		 $pdf->multicell(170,6,'Particulars Of Employment Since Leaving School:');
	
		 
		
		$pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(80, 15,$firtemployer->name); 
		 $pdf->Cell(40, 15,$firtemployer->from.'--'.$firtemployer->to); 
		 $pdf->Cell(70, 15,$firtemployer->position);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(80, 15,$secondemployer->name); 
		 $pdf->Cell(40, 15,$secondemployer->from.'--'.$secondemployer->to); 
		 $pdf->Cell(70, 15,$secondemployer->position);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(80, 15,$thirdemployer->name); 
		 $pdf->Cell(40, 15,$thirdemployer->from.'--'.$thirdemployer->to); 
		 $pdf->Cell(70, 15,$thirdemployer->position);
		 
		 $pdf->Ln(13);
		 $pdf->Cell(5, 15,'4.'); 
         $pdf->Cell(80, 15,$fourthemployer->name); 
		 $pdf->Cell(40, 15,$fourthemployer->from.'--'.$fourthemployer->to); 
		 $pdf->Cell(70, 15,$fourthemployer->name);
		 /***************************************************************************************/
		 $pdf->Ln(30); 
		 
		   $pdf->SetFont('Arial', 'B', 13); 
		 $pdf->multicell(170,6,'Name,Phone Number And The Addresses Of Three Referees(NOT RELATIONS) :');
		 
		
		 $pdf->Ln(13);
		 $pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(50, 15, $firstreferee->name); 
		 $pdf->Cell(40, 15,$firstreferee->referee_phone); 
		 $pdf->Cell(80, 15,$firstreferee->referee_address);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(50, 15,$secondreferee->name); 
		 $pdf->Cell(40, 15,$secondreferee->referee_phone); 
		 $pdf->Cell(80, 15,$secondreferee->referee_address);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(50, 15,$thirdreferee->name); 
		 $pdf->Cell(40, 15,$thirdreferee->referee_phone); 
		 $pdf->Cell(80, 15,$thirdreferee->referee_address);
		 
		 
		 /***************************************************************************************/
		 $pdf->Ln(20);
		  $pdf->SetFont('Arial', 'B', 13); 
		 $pdf->multicell(170,6,'Details Of Documents Attatched To Application :');
		 $pdf->Ln(13);
		 
		 
		$pdf->SetFont('Arial','I', 9); 
		 $pdf->Cell(5, 15,'1.'); 
         $pdf->Cell(50, 15, $firstcertificate->certificateno); 
		 $pdf->Cell(40, 15,$firstcertificate->date); 
		 $pdf->Cell(80, 15,$firstcertificate->discription);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'2.'); 
         $pdf->Cell(50, 15,$secondcertificate->certificateno); 
		 $pdf->Cell(40, 15,$secondcertificate->date); 
		 $pdf->Cell(80, 15,$secondcertificate->discription);
		 $pdf->Ln(13);
		 
		  $pdf->Cell(5, 15,'3.'); 
         $pdf->Cell(50, 15,$thirdcertificate->certificateno); 
		 $pdf->Cell(40, 15,$thirdcertificate->date); 
		 $pdf->Cell(80, 15,$thirdcertificate->discription);
		 
		 $pdf->Ln(13);
		  $pdf->Cell(5, 15,'4.'); 
         $pdf->Cell(50, 15,$fourthcertificate->certificateno); 
		 $pdf->Cell(40, 15,$fourthcertificate->date); 
		 $pdf->Cell(80, 15,$fourthcertificate->discription);
		 
		  $pdf->AddPage();
		 $pdf->SetFont('Arial', 'B', 10);
		$pdf->MultiCell(0, 5, 'TO BE COMPLETED BY TOP CIVIL SERVANT(NOT BELOW GL 14)OR MILITARY OFFICER(NOT BELLOW THE RANK OF CAPTAIN OR EQUIVALENT)APPLICATION IN SERVICE NEED NOT FILL THIS SECTION ');
		
			$pdf->Ln(15);
	        $pdf->SetX(130);
			$pdf->SetDash(); 
			$pdf->SetLineWidth(0.4);
			$pdf->SetDash(1, 1); 
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(23,8,'ADDRESS:');
			$pdf->Cell(55,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(80,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(80,8,'','B',1);
			$pdf->SetDash(); 
			
			
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(35,8,'The Executive Chairman,',0,1);
			
			$pdf->Cell(50,8,'Yobe State Scholarship Board,',0,1);
			
			$pdf->Cell(35,8,'PMB 1048,',0,1);
			$pdf->Cell(30,8,'Damaturu.',0,1);
			
			$pdf->SetFont('Arial','BU',12);
			$pdf->SetX(90);
			$pdf->Cell(35,8,'IDENTIFICATION',0,1);
			
			$pdf->SetFont('Arial','B',10);
		
			
			$pdf->SetDash(); 
			$pdf->SetLineWidth(0.4);
			$pdf->SetDash(1, 1); 
			$pdf->Cell(7, 8, '1.'); 
	        $pdf->Cell(35,8,'Alh/Mal/Mr/Mrs:,',0,0);
			$pdf->Cell(115,5,'','B');
			$pdf->Cell(100,5,'DO HEREBY CERTIFY',0,1);
			$pdf->Cell(65,20,'in my honour that the applicant name');
			$pdf->Cell(130,12,'','B',1);
			$pdf->Cell(40,20,'is the Son/Daughter of');
			$pdf->Cell(100,12,'','B');
			$pdf->Cell(30,20,'and was born at');
			$pdf->Cell(30,12,'','B',1);
			$pdf->Cell(80,12,'','B');
			$pdf->Cell(30,20,'Town/Village of');
			$pdf->Cell(45,12,'','B');
			$pdf->Cell(30,20,'L.G.A and is a bonafide',0,1);
			$pdf->Cell(30,1,'Indigene Of Yobe State.',0,1);
			$pdf->Ln(10);
			$msg1 = "That he/she is no an employee of any Government Department/Ministry or Agency."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '2.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $msg1);
			
			$msg3 = "Should this identification provided incorrect thereafter, I accept full responsibility for the misinformation and i am prepared to refund all monies that might have been expended by Government on the cadidate identified plus any other legal action the Government might wish to institute against me."; 
	
			$pdf->SetFont('Arial', 'B', 13); 
            $pdf->Cell(7, 5, '3.'); 
			$pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(0, 5, $msg3);
			$pdf->Ln(20);
			
			$pdf->SetX(130);
			$pdf->SetDash(); 
			$pdf->SetLineWidth(0.4);
			$pdf->SetDash(1, 1); 
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(28,8,'SIGNATURE:');
			$pdf->Cell(50,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(15,15,'DATE:');
			$pdf->Cell(65,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(30,15,'OCCUPATION:');
			$pdf->Cell(50,8,'','B',1);
			$pdf->SetX(130);
			$pdf->Cell(15,15,'RANK:');
			$pdf->Cell(65,8,'','B',1);
			$pdf->Ln(5);
			$pdf->SetX(130);
			$pdf->Cell(35,15,'OFFICIAL STAMP:');
			$pdf->Cell(45,8,'','B',1);
			
			
	
			
			$pdf->Output();
        exit;
}	else 
	
return view('auth.login');
		
	});	

});







