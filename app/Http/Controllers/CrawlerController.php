<?php

namespace App\Http\Controllers;

use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    protected $url;

    public function __construct(){
        $this->url = "https://www.amazon.com/s?k=car+accessories&crid=33BFADB4EF442&sprefix=car+acc%2Caps%2C414&ref=nb_sb_ss_ts-doa-p_1_7";
    }
    public function index(Request $request){
        //Curl Initiate
        $curl = curl_init();
        $requestType = 'GET'; // GET POST DELETE etc.
        $url = 'https://www.imdb.com/chart/boxoffice';
        //Some Options values , i used defaults
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [],
        ));
        // Executing the cURL request and assigning the response

        $response = curl_exec($curl);
        curl_close($curl);
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();// create dom document
        $dom->loadHTML($response);// load html into dom document
        $xpath = new DOMXPath($dom);// this help to search the elements.
        $searchResults = $xpath->query('//*[@data-component-type="s-search-result"]/div');//search by attribute => data-component-type="s-search-result"
        $data = array();
        foreach($searchResults as $sr){
            //get required data (link, title, img etc..) from Dom
            $img = $sr->getElementsByTagName('img')[0];
            $img = $img->getAttribute('src');
            $title = $sr->getElementsByTagName('h2')[0];
            $link = $title->getElementsByTagName('a')[0];
            $link = $link->getAttribute('href');
            $title = $title->getElementsByTagName('span')[0]->nodeValue;
            // push into array
            array_push($data, [
                'title' => $title,
                'img' => $img,
                'link' => $link
            ]);
        }

        return view('amazon', compact('data'));
    }


}
