<?php defined('SYSPATH') or die('No direct script access.');

class Controller_katalog_cvbasen extends Controller_Katalog_SuperController {
	
    public function before(){
        parent::before();
        
        if(!$_SESSION['user']->is_company_user()) {
    		$this->request->redirect('/login/redirect/'.str_replace('/', '_', $this->request->uri()));
    	}
    }
    public function after(){
        $content = $this->content;
        $this->content = View::factory('/katalog/cvbasen/main');
        $this->content->content = $content;
        $this->css[] = '/css/katalog/cvbasen.css';
        parent::after();
    }
    
	public function action_index() {
	    $this->content = View::factory('/katalog/cvbasen/welcome');
	}
	public function action_interest(){
	    $this->css[] = '/css/katalog/ps.css';
	    $this->content = View::factory('/katalog/cvbasen/interest');
	    $cid = $_SESSION['user']->get_company_link();
	    $this->content->programs = Model::factory('data')->format_for_select(Model::factory('data')->get_program());
	    $this->content->users = 
	        DB::select_array(array('user.fname', 'user.lname', 'user.programId', 'user.user_id', 'interview_interest.company_request', 'interview_interest.company'))
	            ->from('user')
	            ->join('interview_interest')
	            ->on('user.user_id', '=', 'interview_interest.user')
	            ->where('interview_interest.company', '=', $cid)
	            ->execute()
	            ->as_array();
	}
	public function action_all(){
	    $this->css[] = '/css/katalog/ps.css';
	    $this->content = View::factory('/katalog/cvbasen/all');
	    $cid = $_SESSION['user']->get_company_link();
	    $this->content->programs = Model::factory('data')->format_for_select(Model::factory('data')->get_program());
	    
	    $selected = 
	    DB::select_array(array('user.fname', 'user.lname', 'user.programId', 'user.user_id', 'interview_interest.company_request', 'interview_interest.company'))
	            ->from('user')
	            ->join('interview_interest')
	            ->on('user.user_id', '=', 'interview_interest.user')
	            ->where('interview_interest.company', '=', $cid)
	            ->execute()
	            ->as_array();
	    $reformatted = array();
	    foreach($selected as $s){
	    	$reformatted[$s['user_id']] = $s['company_request'];
	    }
	    $this->content->selected = $reformatted;
	    $pdfs = kohana::list_files('../upload/user/cv');
	    $userids = array();
	    foreach($pdfs as $path => $abspath){
	    	if(strtolower(substr($path, -3)) == 'pdf'){
	    		$parts = explode('/', $path);
	    		$filename = array_pop($parts);
	    		$userids[] = substr($filename, 0, strpos($filename, '.'));
	    	}
	    }
	    $this->content->users =
	        DB::select_array(array('user.fname', 'user.lname', 'user.programId', 'user.user_id'))
	            ->from('user')
	            ->where('user.user_id', 'in', $userids)
	            ->execute()
                ->as_array();
	}
	
	public function action_details($uid){
	    $this->content = View::factory('/katalog/cvbasen/details');
		$this->content->interest =
                    DB::select('*')
                        ->from('interview_interest')
                        ->where('user', '=', $uid)
                        ->where('company', '=', $_SESSION['user']->get_company_link())
                        ->execute()
                        ->as_array();
		if(count($this->content->interest) > 0){
			list($this->content->interest) = $this->content->interest;
		} else {
			$this->content->interest = false;
		}
		
	    if(isset($_POST) && !empty($_POST)){
	        
	        $select = (isset($_POST['firsthand']) ? 1 : (isset($_POST['secondhand']) ? 2 : false));
	        if($this->content->interest){
            DB::update('interview_interest')
                   ->set(array(
                       'company_request' => $select
                   ))
                   ->where('company', '=', $_SESSION['user']->get_company_link())
                   ->where('user', '=', $uid)
                   ->execute();
	        } else {
	        	DB::insert('interview_interest', array('user', 'company', 'company_request','time'))
	        		->values(array(
	        		     $uid, 
	        		     $_SESSION['user']->get_company_link(),
	        		     $select,
	        		     time()
	        		))
	        		->execute();
	        }
	    }
	    list($this->content->user) =
	                DB::select('*')
                        ->from('user')
                        ->where('user_id', '=', $uid)
                        ->execute()
                        ->as_array();
        
        list($this->content->program) = Model::factory('data')->get_program($this->content->user['programId']);
  	}
	public function action_selected(){
	    $this->css[] = '/css/katalog/ps.css';
	    $this->content = View::factory('/katalog/cvbasen/selected');
	    $cid = $_SESSION['user']->get_company_link();
	    $this->content->programs = Model::factory('data')->format_for_select(Model::factory('data')->get_program());
	    $this->content->users = 
	        DB::select_array(array('user.fname', 'user.lname', 'user.programId', 'user.user_id', 'interview_interest.*'))
	            ->from('user')
	            ->join('interview_interest')
	            ->on('user.user_id', '=', 'interview_interest.user')
	            ->where('interview_interest.company', '=', $cid)
	            ->where('interview_interest.company_request', 'in', DB::expr('(1,2)'))
	            ->order_by('company_request', 'asc')
	            ->execute()
	            ->as_array();
	    $this->content->rooms = DB::select('*')
	                            ->from('room')
	                            ->order_by('name', 'asc')
	                            ->execute()
	                            ->as_array();
        $this->content->rooms = Model::factory('data')->format_for_select($this->content->rooms);
	    $periods = DB::select('*')
	                            ->from('period')
	                            ->order_by('start', 'asc')
	                            ->execute()
	                            ->as_array();
        $this->content->periods = array();
        foreach($periods as $p){
            $this->content->periods[$p['period_id']] = $p['start'].' - '.$p['end'];
        }
	}
	public function action_booked(){
	$this->css[] = '/css/katalog/ps.css';
	    $this->content = View::factory('/katalog/cvbasen/booked');
	    $cid = $_SESSION['user']->get_company_link();
	    $this->content->programs = Model::factory('data')->format_for_select(Model::factory('data')->get_program());
	    $this->content->users = 
	        DB::select_array(array('user.fname', 'user.lname', 'user.programId', 'user.user_id', 'interview_interest.*'))
	            ->from('user')
	            ->join('interview_interest')
	            ->on('user.user_id', '=', 'interview_interest.user')
	            ->where('interview_interest.company', '=', $cid)
	            ->where('interview_interest.company_request', 'in', DB::expr('(1,2)'))
	            ->where('interview_interest.room', '>', '0')
	            ->where('interview_interest.period', '>', '0')
	            ->order_by('company_request', 'asc')
	            ->execute()
	            ->as_array();
	    $this->content->rooms = DB::select('*')
	                            ->from('room')
	                            ->order_by('name', 'asc')
	                            ->execute()
	                            ->as_array();
        $this->content->rooms = Model::factory('data')->format_for_select($this->content->rooms);
	    $periods = DB::select('*')
	                            ->from('period')
	                            ->order_by('start', 'asc')
	                            ->execute()
	                            ->as_array();
        $this->content->periods = array();
        foreach($periods as $p){
            $this->content->periods[$p['period_id']] = $p['start'].' - '.$p['end'];
        }
	}
	public function action_confirmed(){
		$this->content = View::factory('/katalog/cvbasen/confirmed');
	    $cid = $_SESSION['user']->get_company_link();
	    $this->content->programs = Model::factory('data')->format_for_select(Model::factory('data')->get_program());
	    $this->content->users = 
	        DB::select_array(array('user.fname', 'user.lname', 'user.programId', 'user.user_id', 'interview_interest.company_request'))
	            ->from('user')
	            ->join('interview_interest')
	            ->on('user.user_id', '=', 'interview_interest.user')
	            ->where('interview_interest.company', '=', $cid)
	            ->where('interview_interest.company_request', 'not', 0)
	            ->execute()
	            ->as_array();
		
	}
}
