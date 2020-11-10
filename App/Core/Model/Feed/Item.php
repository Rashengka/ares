<?php

namespace App\Core\Model\Feed;

use Suin\RSSWriter\SimpleXMLElement;

class Item extends \Suin\RSSWriter\Item
{
    public static string $dateFormat = DATE_RSS;

    public function asXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item></item>',
            LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        if ($this->preferCdata) {
            $xml->addCdataChild('title', $this->title);
        } else {
            $xml->addChild('title', $this->title);
        }

        $xml->addChild('link', $this->url);

        if ($this->preferCdata) {
            $xml->addCdataChild('description', $this->description);
        } else {
            $xml->addChild('description', $this->description);
        }

        if ($this->contentEncoded) {
            $xml->addCdataChild('xmlns:content:encoded', $this->contentEncoded);
        }

        foreach ($this->categories as $category) {
            $element = $xml->addChild('category', $category[0]);

            if (isset($category[1])) {
                $element->addAttribute('domain', $category[1]);
            }
        }

        if ($this->guid) {
            $guid = $xml->addChild('guid', $this->guid);

            if ($this->isPermalink === false) {
                $guid->addAttribute('isPermaLink', 'false');
            }
        }

        if ($this->pubDate !== null) {
            $xml->addChild('pubDate', date(self::$dateFormat, $this->pubDate));
        }

        if (is_array($this->enclosure) && (count($this->enclosure) == 3)) {
            $element = $xml->addChild('enclosure');
            $element->addAttribute('url', $this->enclosure['url']);
            $element->addAttribute('type', $this->enclosure['type']);

            if ($this->enclosure['length']) {
                $element->addAttribute('length', $this->enclosure['length']);
            }
        }

        if (!empty($this->author)) {
            $xml->addChild('author', $this->author);
        }

        if (!empty($this->creator)) {
            $xml->addChild('dc:creator', $this->creator, "http://purl.org/dc/elements/1.1/");
        }

        return $xml;
    }
}
