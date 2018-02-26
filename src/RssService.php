<?php 
namespace Rss2Mastodon;
use \SimplePie;

class RssService{
    private $feedService;
    public function __construct(SimplePie $feedService)
    {
        $this->feedService = $feedService;
    }
    
    public function getFeed($feedUrl)
    {
        $articles = array();
        $this->feedService->set_feed_url($feedUrl);
        $this->feedService->enable_cache(false);
        $this->feedService->init();
        
        $items = $this->feedService->get_items();
        foreach ($items as $node)
        {
            $article = new RssArticle();
            $article->title = $node->get_title();
            $article->rawContent = $node->get_content();
            
            $article->plainContent = \strip_tags($node->get_content());
            $article->postDate = $node->get_date("Y-m-d H:i");
            $article->link = $node->get_link();
            $article->images = $this->extractImages($article->rawContent);
            
            $articles[] = $article;
        }
        return $articles;
    }
    
    private function extractImages($htmlContent)
    {
        $images = array();
        $re = '/<img (?=[^>]*src=").*?src="([^"]*)"/';
        preg_match_all($re, $htmlContent, $matches, PREG_SET_ORDER, 0);
        
        foreach ($matches as $image) {
            $images[] = $image[1];
        }
        return $images;
    }
    
    /*public function extractUrls($htmlContent)
    {
        $images = array();
        $re = '/<img (?=[^>]*src=").*?src="([^"]*)"/';
        preg_match_all($re, $htmlContent, $matches, PREG_SET_ORDER, 0);
        
        foreach ($matches as $image) {
            return $image[1]
            $images[] = $image[1];
        }
        return $images;
    }*/
}

