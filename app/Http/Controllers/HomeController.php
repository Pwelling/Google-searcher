<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Search;
class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
	public function search(){
		return view('search');
	}
	
	public function browse(Request $request){
		return view('browse',['results'=>$this->getResults(),'paginate'=>false,'key'=>false,'items'=>false]);
	}
	
	private function getResults(){
		$results = Search::select(\DB::raw('count(*) as key_count, `key`'))
                     ->groupBy('key')
                     ->get();
		return $results;
	}
	
	function browseItems(Request $request,$key){
		$results = $this->getResults();
		$paginate = Search::where('key','=',$key)->paginate(10);
		$page = $request->input('page');
		$page = ($page != '') ? $page-1 : 0;
		$skip = $page*10;
		$items = Search::select('key','result','url')->where('key','=',$key)->skip($skip)->take(10)->get();
		return view('browse',['results'=>$this->getResults(),'paginate'=>$paginate,'key'=>$key, 'items'=> $items]);
	}
	
	/**
	 * \brief Processes the search request and stores the results in the Database
	 * @author P.Welling
	 */
	public function searchGoogle(Request $request){
		$key = $request->input('key');
		$this->removeOldResults($key);
		$url  = 'http://www.google.com/search?hl=en&tbo=d&site=&source=hp&q='.urlencode($key).'&oq='.urlencode($key);
		$html = file_get_contents($url);
		preg_match_all('/<h3 class=\"r\">(.*?)<\/h3>/', $html, $result);
		$this->loopResult($result, $key);
		preg_match_all('/<a class=\"fl\" href=(.*?)\><span class=\"csb\"/',$html,$links);
		for($i=0,$il=9;$i<$il;$i++){
			preg_match('/\"(.*)\"/',$links[1][$i],$result);
			$url = 'http://www.google.com/'.$result[1];
			$html = file_get_contents($url);
			preg_match_all('/<h3 class=\"r\">(.*?)<\/h3>/', $html, $result);
			$this->loopResult($result, $key);
		}
		return redirect('/')->with('status', 'Search has been processed');
	}
	
	/**
	 * \brief Loops through the results and saves them to the Table
	 * @author P.Welling
	 */
	private function loopResult($result,$key){
		foreach($result[1] AS $url){
			$this->saveGoogleResult($url, $key);
		}
	}
	
	/**
	 * \brief Removes any previous result to keep the db clean
	 * @author P.Welling
	 */
	private function removeOldResults($key){
		Search::where('key', '=', $key)->delete();
	}
	
	/**
	 * \brief Saves the given result to the table
	 * @author P.Welling
	 */
	private function saveGoogleResult($url,$key){
		preg_match('/\"(.*)\"/',$url,$realUrl);
		if(isset($realUrl[1])){
			$newUrl = $realUrl[1];
			Search::insert(['key' => $key,'url'=>$newUrl,'result'=>strip_tags($url)]);
		}
	}
	
}
