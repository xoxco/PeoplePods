<?php

/***************************************************************************
 *                         RSS 2.0 generation class
 *                         ------------------------
 *
 *   copyright            : (C) 2006 Marijo Galesic
 *   email                : mgalesic@gmail.com
 *
 *   Id: class.rss.php, v 1.1 2006/08/25
 *
 *   www.starmont.net
 *
 * Redistribution and use in source and binary forms,
 * with or without modification must retain the above copyright notice
 *
 ***************************************************************************/

class rss {

    var $rss;
    var $encoding;

    var $title;
    var $link;
    var $description;
    var $language;
    var $copyright;
    var $managingEditor;
    var $webMaster;
    var $pubDate;
    var $lastBuildDate;
    var $category;
    var $generator;
    var $docs;
    var $cloud;
    var $ttl;
    var $image;
    var $textinput;
    var $skipHours = array();
    var $skipDays = array();

    var $itemTitle;
    var $itemLink;
    var $itemDescription;
    var $itemAuthor;
    var $itemCategory;
    var $itemComments;
    var $itemEnclosure;
    var $itemGuid;
    var $itemPubDate;
    var $itemSource;

    var $path;
    var $filename;

    function rss($encoding = ''){
        $this->generator = 'RSS 2.0 generation class';
        $this->docs = 'http://blogs.law.harvard.edu/tech/rss';
        if(!empty($encoding)){ $this->encoding = $encoding; }
    }

    function channel($title, $link, $description){
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
    }

    function language($language){ $this->language = $language; }

    function copyright($copyright){ $this->copyright = $copyright; }

    function managingEditor($managingEditor){ $this->managingEditor = $managingEditor; }

    function webMaster($webMaster){ $this->webMaster = $webMaster; }

    function pubDate($pubDate){ $this->pubDate = $pubDate; }

    function lastBuildDate($lastBuildDate){ $this->lastBuildDate = $lastBuildDate; }

    function category($category, $domain = ''){
        $this->category .= $this->s(2) . '<category';
        if(!empty($domain)){ $this->category .= ' domain="' . $domain . '"'; }
        $this->category .= '>' . $category . '</category>' . "\n";
    }

    function cloud($domain, $port, $path, $registerProcedure, $protocol){
        $this->cloud .= $this->s(2) . '<cloud domain="' . $domain . '" port="' . $port . '" registerProcedure="' . $registerProcedure . '" protocol="' . $protocol . '" />';
    }

    function ttl($ttl){ $this->ttl = $ttl; }

    function image($url, $title, $link, $width = '', $height = '', $description = ''){
        $this->image = $this->s(2) . '<image>' . "\n";
        $this->image .= $this->s(3) . '<url>' . $url . '</url>' . "\n";
        $this->image .= $this->s(3) . '<title>' . $title . '</title>' . "\n";
        $this->image .= $this->s(3) . '<link>' . $link . '</link>' . "\n";
        if($width != ''){ $this->s(3) . '<width>' . $width . '</width>' . "\n"; }
        if($height != ''){ $this->s(3) . '<height>' . $height . '</height>' . "\n"; }
        if($description != ''){ $this->s(3) . '<description>' . $description . '</description>' . "\n"; }
        $this->image .= $this->s(2) . '</image>' . "\n";
    }

    function textInput($title, $description, $name, $link){
        $this->textInput = $this->s(2) . '<textInput>' . "\n";
        $this->textInput .= $this->s(3) . '<title>' . $title . '</title>' . "\n";
        $this->textInput .= $this->s(3) . '<description>' . $description . '</description>' . "\n";
        $this->textInput .= $this->s(3) . '<name>' . $name . '</name>' . "\n";
        $this->textInput .= $this->s(3) . '<link>' . $link . '</link>' . "\n";
        $this->textInput .= $this->s(2) . '</textInput>' . "\n";
    }

    function skipHours(){
        $this->skipHours = array();
        $args = func_get_args();
        $this->skipHours = array_values($args);
    }

    function skipDays(){
        $this->skipDays = array();
        $args = func_get_args();
        $this->skipDays = array_values($args);
    }

    function startRSS($path = '.', $filename = 'rss'){
        $this->path = $path;
        $this->filename = $filename;
        $this->rss = '<?xml version="1.0"';
        if(!empty($this->encoding)){ $this->rss .= ' encoding="' . $this->encoding . '"'; }
        $this->rss .= '?>' . "\n";
        $this->rss .= '<rss version="2.0">' . "\n";
        $this->rss .= $this->s(1) . '<channel>' . "\n";
        $this->rss .= $this->s(2) . '<title>' . $this->title . '</title>' . "\n";
        $this->rss .= $this->s(2) . '<link>' . $this->link . '</link>' . "\n";
        $this->rss .= $this->s(2) . '<description>' . $this->description . '</description>' . "\n";
        if(!empty($this->language)){ $this->rss .= $this->s(2) . '<language>' . $this->language . '</language>' . "\n"; }
        if(!empty($this->copyright)){ $this->rss .= $this->s(2) . '<copyright>' . $this->copyright . '</copyright>' . "\n"; }
        if(!empty($this->managingEditor)){ $this->rss .= $this->s(2) . '<managingEditor>' . $this->managingEditor . '</managingEditor>' . "\n"; }
        if(!empty($this->webMaster)){ $this->rss .= $this->s(2) . '<webMaster>' . $this->webMaster . '</webMaster>' . "\n"; }
        if(!empty($this->pubDate)){ $this->rss .= $this->s(2) . '<pubDate>' . $this->pubDate . '</pubDate>' . "\n"; }
        if(!empty($this->lastBuildDate)){ $this->rss .= $this->s(2) . '<lastBuildDate>' . $this->lastBuildDate . '</lastBuildDate>' . "\n"; }
        if(!empty($this->category)){ $this->rss .= $this->category; }
        $this->rss .= $this->s(2) . '<generator>' . $this->generator . '</generator>' . "\n";
        $this->rss .= $this->s(2) . '<docs>' . $this->docs . '</docs>' . "\n";
        if(!empty($this->cloud)){ $this->rss .= $this->cloud; }
        if(!empty($this->ttl)){ $this->rss .= $this->s(2) . '<ttl>' . $this->ttl . '</ttl>' . "\n"; }
        if(!empty($this->image)){ $this->rss .= $this->image; }
        if(!empty($this->textInput)){ $this->rss .= $this->textInput; }
        if(count($this->skipHours) > 0){
            $this->rss .= $this->s(2) . '<skipHours>' . "\n";
            for($i = 0; $i < count($this->skipHours); $i++){
                $this->rss .= $this->s(3) . '<hour>' . $this->skipHours[$i] . '</hour>' . "\n";
            }
            $this->rss .= $this->s(2) . '</skipHours>' . "\n";
        }
        if(count($this->skipDays) > 0){
            $this->rss .= $this->s(2) . '<skipDays>' . "\n";
            for($i = 0; $i < count($this->skipDays); $i++){
                $this->rss .= $this->s(3) . '<day>' . $this->skipHours[$i] . '</day>' . "\n";
            }
            $this->rss .= $this->s(2) . '</skipDays>' . "\n";
        }
    }

    function itemTitle($title){ $this->itemTitle = $title; }

    function itemLink($link){ $this->itemLink = $link; }

    function itemDescription($description){ $this->itemDescription = $description; }

    function itemAuthor($author){ $this->itemAuthor = $author; }

    function itemCategory($category, $domain = ''){
        $this->itemCategory .= $this->s(3) . '<category';
        if(!empty($domain)){ $this->itemCategory .= ' domain="' . $domain . '"'; }
        $this->itemCategory .= '>' . $category . '</category>' . "\n";
    }

    function itemComments($comments){ $this->itemComments = $comments; }

    function itemEnclosure($enclosure){ $this->itemEnclosure = $enclosure; }

    function itemGuid($guid, $isPermaLink = ''){
        $this->itemGuid = $this->s(3) . '<guid';
        if(!empty($isPermaLink)){ $this->itemGuid .= ' isPermaLink="' . $isPermaLink . '"'; }
        $this->itemGuid .= '>' . $guid . '</guid>' . "\n";
    }

    function itemPubDate($pubDate){ $this->itemPubDate = $pubDate; }

    function itemSource($source, $url){
        $this->itemSource = $this->s(3) . '<source url="' . $url . '">' . $source . '</source>' . "\n";
    }

    function addItem(){
        $this->rss .= $this->s(2) . '<item>' . "\n";
        if(!empty($this->itemTitle)){ $this->rss .= $this->s(3) . '<title>' . $this->itemTitle . '</title>' . "\n"; }
        if(!empty($this->itemLink)){ $this->rss .= $this->s(3) . '<link>' . $this->itemLink . '</link>' . "\n"; }
        if(!empty($this->itemDescription)){ $this->rss .= $this->s(3) . '<description>' . $this->itemDescription . '</description>' . "\n"; }
        if(!empty($this->itemAuthor)){ $this->rss .= $this->s(3) . '<author>' . $this->itemAuthor . '</author>' . "\n"; }
        if(!empty($this->itemCategory)){ $this->rss .= $this->itemCategory; }
        if(!empty($this->itemComments)){ $this->rss .= $this->s(3) . '<comments>' . $this->itemComments . '</comments>' . "\n"; }
        if(!empty($this->itemEnclosure)){ $this->rss .= $this->s(3) . '<enclosure>' . $this->itemEnclosure . '</enclosure>' . "\n"; }
        if(!empty($this->itemGuid)){ $this->rss .= $this->itemGuid; }
        if(!empty($this->itemPubDate)){ $this->rss .= $this->s(3) . '<pubDate>' . $this->itemPubDate . '</pubDate>' . "\n"; }
        if(!empty($this->itemSource)){ $this->rss .= $this->itemSource; }
        $this->rss .= $this->s(2) . '</item>' . "\n";

        $this->itemTitle = '';
        $this->itemLink = '';
        $this->itemDescription = '';
        $this->itemAuthor = '';
        $this->itemCategory = '';
        $this->itemComments = '';
        $this->itemEnclosure = '';
        $this->itemGuid = '';
        $this->itemPubDate = '';
        $this->itemSource = '';
    }

    function RSSdone(){
        $this->rss .= $this->s(1) . '</channel>' . "\n";
        $this->rss .= '</rss>';
		
		return $this->rss;

    }

    function clearRSS(){
        $this->title = '';
        $this->link = '';
        $this->description = '';
        $this->language = '';
        $this->copyright = '';
        $this->managingEditor = '';
        $this->webMaster = '';
        $this->pubDate = '';
        $this->lastBuildDate = '';
        $this->category = '';
        $this->cloud = '';
        $this->ttl = '';
        $this->skipHours = array();
        $this->skipDays = array();
    }

    function s($space){
        $s = '';
        for($i = 0; $i < $space; $i++){ $s .= '   '; }
        return $s;
    }

}

?>