<?php
declare(strict_types = 1);

namespace App\Core\Model\Feed;

use Suin\RSSWriter\SimpleXMLElement;

class Channel extends \Suin\RSSWriter\Channel
{
    public static string $dateFormat = DATE_RSS;

    /**
     * Return XML object
     *
     * @return SimpleXMLElement
     */
    public function asXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><channel></channel>',
            LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);
        $xml->addChild('title', $this->title);
        $xml->addChild('link', $this->url);
        $xml->addChild('description', $this->description);

        if ($this->feedUrl !== null) {
            $link = $xml->addChild('atom:link', '', "http://www.w3.org/2005/Atom");
            $link->addAttribute('href', $this->feedUrl);
            $link->addAttribute('type', 'application/rss+xml');
            $link->addAttribute('rel', 'self');
        }

        if ($this->language !== null) {
            $xml->addChild('language', $this->language);
        }

        if ($this->copyright !== null) {
            $xml->addChild('copyright', $this->copyright);
        }

        if ($this->pubDate !== null) {
            $xml->addChild('pubDate', date(self::$dateFormat, $this->pubDate));
        }

        if ($this->lastBuildDate !== null) {
            $xml->addChild('lastBuildDate', date(self::$dateFormat, $this->lastBuildDate));
        }

        if ($this->ttl !== null) {
            $xml->addChild('ttl', $this->ttl);
        }

        if ($this->pubsubhubbub !== null) {
            $feedUrl = $xml->addChild('xmlns:atom:link');
            $feedUrl->addAttribute('rel', 'self');
            $feedUrl->addAttribute('href', $this->pubsubhubbub['feedUrl']);
            $feedUrl->addAttribute('type', 'application/rss+xml');

            $hubUrl = $xml->addChild('xmlns:atom:link');
            $hubUrl->addAttribute('rel', 'hub');
            $hubUrl->addAttribute('href', $this->pubsubhubbub['hubUrl']);
        }

        foreach ($this->items as $item) {
            $toDom = dom_import_simplexml($xml);
            $fromDom = dom_import_simplexml($item->asXML());
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }

        return $xml;
    }
}