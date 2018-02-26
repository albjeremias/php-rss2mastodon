<?php 
namespace Rss2Mastodon;

class Rss2MastodonService{
    private $config;
    
    /** @var MastodonService **/
    private $mastodonService;
    /** @var RssService **/
    private $rssService;
    
    public function __construct(MastodonService $mastodonService, RssService $rssService)
    {
        $this->mastodonService = $mastodonService;
        $this->rssService = $rssService;
    }
    
    public function sync()
    {
        $this->mastodonService->setShortLinkAccesToken($this->config['short_link_access_token']);
        foreach ( $this->config['accounts'] as $collectiveName => $mastodonColletive ) {
            $this->syncMember($collectiveName);
        }
    }
    
    public function syncMember($collectiveName) 
    {
        $mastodonColletive = $this->config['accounts'][$collectiveName];
        
        echo "$collectiveName: ";
        
        $mastodonAccount = new MastodonAccount();
        $mastodonAccount->instanceUrl = $mastodonColletive['mastodon_url'];
        $mastodonAccount->token = $mastodonColletive['mastodon_token'];
        $lastUpdate = $mastodonColletive['last_update'];
        foreach ( $mastodonColletive['feeds'] as $feedUrl ) {
            $articles = $this->rssService->getFeed($feedUrl);
            $newArticles = $this->filterByLatestUpdate($articles, $lastUpdate);
            
            if (count($newArticles['articles']) == 0) {
                echo "no new articles found" . chr(10);
                continue;
            }
            echo "adding new " . count($newArticles['articles']). ' articles' . chr(10);
            $this->mastodonService->addArticles($mastodonAccount, $newArticles['articles']);
            $this->config['accounts'][$collectiveName]['last_update'] = $newArticles['new_last_update'];
        }
    }
    
    public function saveConfig($fileName)
    {
        file_put_contents($fileName, \json_encode($this->config, JSON_PRETTY_PRINT));
    }
    
    public function loadConfig($fileName)
    {
        $jsontring = file_get_contents($fileName);
        $this->config = json_decode($jsontring, true);
    }
    
    public function filterByLatestUpdate($articles, $lastUpdate = 'now')
    {
        $newLastUpdate = $lastUpdate;
        $newArticles = array();
        $i = 0;
        $articles = array_reverse($articles);
        foreach($articles as $article) 
        {
            if (strtotime($lastUpdate) >= strtotime($article->postDate) && $lastUpdate != 0)
            {
                continue;
            }
            $i++;
            if ($newLastUpdate == 0) {
                $newLastUpdate = $article->postDate;
            }
            if (strtotime($newLastUpdate) < strtotime($article->postDate))
            {
                $newLastUpdate = $article->postDate;
            }
            $newArticles[] = $article;
            if ($i > 10) {
                break;
            }
        }
        return array('articles' => $newArticles, 'new_last_update' => $newLastUpdate);
    }
}

