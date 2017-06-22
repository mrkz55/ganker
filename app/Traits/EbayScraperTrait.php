<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

use App\Jobs\ScrapeEbayIndexJob;
use App\Item;

trait EbayScraperTrait
{

    /**
     * The public function we call to search ebay, sends each page result job to a separate worker to process
     * the result
     *
     * @param $category_slug
     * @param $user
     * @param $search
     * 
     * @return bool
     */
    public static function searchEbay($category_slug, $user = false, $search = false)
    {
    	$search_index_page_list = self::getSearchIndexPageList($category_slug, $user, $search);

    	$item_list = [];

    	foreach ($search_index_page_list as $result_url)
    		//This is where we throw the job on the queue, the job itself is in the file corresponding to the namespace :)
    		dispatch( new ScrapeEbayIndexJob($result_url) );
    }

    /**
     * The public function we call to search a seller, sends each page result job to a separate worker to process
     * the result
     *
     * @param $store_slug
     * 
     * @return bool
     */
    public static function searchEbaySeller($seller_slug, $search = false)
    {

        return true;
        $search_index_page_list = self::getSearchIndexPageList($category_slug, $user, $search);

        $item_list = [];

        foreach ($search_index_page_list as $result_url)
            //This is where we throw the job on the queue, the job itself is in the file corresponding to the namespace :)
            dispatch( new ScrapeEbayIndexJob($result_url) );
    }

    /**
     * The public function we call to search an entire category, sends each page result job to a separate worker to process
     * the result
     *
     * @param $store_slug
     * 
     * @return bool
     */
    public static function searchEbayCategory($category_slug, $search = false)
    {

        return true;
        $search_index_page_list = self::getSearchIndexPageList($category_slug, $user, $search);

        $item_list = [];

        foreach ($search_index_page_list as $result_url)
            //This is where we throw the job on the queue, the job itself is in the file corresponding to the namespace :)
            dispatch( new ScrapeEbayIndexJob($result_url) );
    }


    /**
     * Helper function to extract an ebay item id from its url
     *
     * @param $item_url
     * 
     * @return string
     */
    private static function getItemIdFromUrl($item_url)
    {

    	$display = explode('/', $item_url);
    	$idValue = $display[5];

    	if ( strpos( $idValue, "#" ) !== false ) {
    		$display = explode('#', $idValue);
    	}

    	else

    	{
    		$display = explode('?', $idValue);
    	}

    	$ebayItemId = $display[0];

    	return $ebayItemId;
    }

    /**
     * Helper function to generate ebay search url
     *
     * @param $category_slug
     * @param $user
     * @param $search
     * @param $page
     * @param $count
     * 
     * @return string
     */
    private static function getSearchUrl($category_slug, $user = null, $search = null, $page = 1, $count = 1000)
    {

        $search = isset($search) ? urlencode($search) : null;

    	isset($user) ? $_from = "&_from={$user}" : null; //add seller paramater if specified
    	isset($search) ? $_nkw = "&_from={$search}" : null; //add search paramater if specified
    	isset($page) ? $_pgn = "&_pgn={$page}" : "&_pgn=1"; //add pagination count, defaults to 1
    	isset($count) ? $_skc = "&_results={$count}" : null; //add results per page count, defaults to 1000

    	$base_url = "https://www.ebay.com.au/sch/{$category_slug}/1/i.html?_sop=18&_armrs=1&LH_BIN=1";

    	$search_url = "{$base_url}{$_from}{$_nkw}{$_pgn}{$_skc}";

    	return $search_url;
    }

    /**
     * Generates an array of every search page to avoid paginating manually
     *
     * @param $category_slug
     * @param $user
     * @param $search
     * @param $start
     * @param $finish
     * @param $count 
     * 
     * @return array
     */
    private static function getSearchIndexPageList($category_slug, $user = false, $search = false, $start = 0, $finish = 100, $count = 1000)
    {
    	$search_index_page_list = [];

    	for ($i=$start; $i<$finish; $i++)
    	{
    		$search_index_page_list[] = self::getSearchUrl($category_slug, $user, $search, $i, $count);

    	}

    	return $search_index_page_list;

    }

    /**
     * Helper function to iterate through ebay search page result html and return
     * them as an array
     * 
     * @param $search_page_html
     * 
     * @return array
     */
    private static function getSearchPageItemList($search_page_html)
    {

        //some funky stuff here to make the pages play nicely with dom/xpath
    	$html = html_entity_decode($search_page_html, NULL, 'UTF-8');

    	$doc = new \DOMdocument('1.0', 'UTF-8');
    	$internalErrors = libxml_use_internal_errors(true);

    	$doc->loadHtml($html);

    	libxml_use_internal_errors($internalErrors);

    	$search_page_item_list = [];

        //if the result is null return empty array
        if (!$indexNode = $doc->getElementById('ListViewInner'))
            return [];

        $xpath = new \DOMXPath($doc);

    	// Iterate over ul results
        foreach ($indexNode->childNodes as $childNode)
        {
            ob_start();
            var_dump($childNode);
            $debug = ob_get_clean();

            Log::error($debug);

            if (!$childNode->attributes)
                continue;

            try {

                //scrape the id from the primary li under the attribute listingid
                if (!$id = $childNode->getAttribute('listingid'))
                    continue;

                //scrape the thumb source using xpath
                $thumb = ($node = $xpath->query(".//div[@class='lvpic pic img left']/div/a/img", $childNode)->item(0))
                ? $node->getAttribute('src')
                : false;

                //scrape the title using xpath
                $name = ($node = $xpath->query(".//h3[@class='lvtitle']/a", $childNode)->item(0))
                ? $node->textContent
                : false;

                $search_page_item_list[] = [
                'id' => $id,
                'thumb' => $thumb,
                'name' => $name
                ];

            } catch (Exception $e) {
                Log::error("{$e->code}: {$e->message}");
                continue;
            }

        }

        return $search_page_item_list;
    }    

    /**
     * Wrapper for Guzzle implementing any useragent and proxy masking stuf we want
     *
     * @param $url
     * 
     * @return string
     */
    private static function guzzleGrab($url)
    {

    	//not using proxies yet
    	$proxy = false;

    	try {
    		$response = \Guzzle::get($url, [
    		                         "User-Agent" => (\Faker\Factory::create())->userAgent,
    		                         "proxy" => $proxy,
    		                         "timeout" => 0]);
    		$body = $response->getBody()->getContents();

    		return $body;

    	} catch (\Exception $e) {
    		$log['_exception_code'] = $e->getCode();
    		$log['_exception_message'] = $e->getMessage();

            Log::error("guzzleGrab Code: {$e->getCode()}, Message: {$e->getMessage()}", $log);
    	}

    }

    public function scrapeEbayIndex($url)
    {

        $search_page_html = self::guzzleGrab($url);
        
        foreach (self::getSearchPageItemList($search_page_html) as $item)
        {
            //$id = isset($item->id) ? $item->id : 12345678; //throw any junk items into this record
            //$name = isset($item->name) ? $item->name : 'unknown'; //if it wasn't junk but we didn't scrape a title, make an entry

            Log::notice("Saved item: {$item['name']}");
            Item::updateOrCreate(['id' => $item['id']], $item); //update or create the db record
        }
    }


}