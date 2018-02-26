<?php 
namespace Rss2Mastodon;

use \Curl\Curl;
class MastodonService
{
    /** @var \Curl\Curl **/
    private $curl;
    private $shortLinkAccessToken = '';
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }
    
    public function addArticles(MastodonAccount $mastodonAccount, $articles) 
    {
        foreach($articles as $article) {
            $this->addArticle($mastodonAccount, $article);
        }
    }
    
    public function addArticle(MastodonAccount $mastodonAccount, RssArticle $article)
    {
        $content = $article->title;
        if ( empty($content) ){
            $content = $article->plainContent;
        }
        $link = $this->urlShort($article->link);
        if (strlen($content . ' ' . $link) > 500) {
            $content = substr($content,0,-1*strlen($link)+3) . ' ' . $link . '...';
        } else {
            $content .= ' ' . $link;
        }
        $this->postToMastodon($mastodonAccount, $content);
    }
    
    public function postToMastodon(MastodonAccount $mastodonAccount, $content)
    {
        echo "posting to mastodon: " . $content . chr(10);
        $action = '/api/v1/statuses';
        $url = $mastodonAccount->instanceUrl . $action . '?access_token=' . $mastodonAccount->token;
        $get = $this->curl->post($url, array('status' => $content));
    }
    
    public function urlShort($url)
    {
        if (empty($this->shortLinkAccessToken)) {
            return $url;
        }
        $response = $this->curl->post('http://tiny-url.info/api/v1/create', array('url' => $url, 'apikey' => $this->shortLinkAccessToken, 'format' => 'json', 'provider' => 'qr_cx'));
        $response = json_decode($response, true);
        return $response['shorturl'];
        
    }
    
    public function setShortLinkAccesToken($accessToken)
    {
        $this->shortLinkAccessToken = $accessToken;
    }
}

