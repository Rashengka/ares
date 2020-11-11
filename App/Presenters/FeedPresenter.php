<?php
declare(strict_types = 1);

namespace App\Presenters;

use App\Core\Model\Feed\Channel;
use App\Core\Model\Feed\Item;
use Contributte\Application\Response\XmlResponse;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;
use Suin\RSSWriter\Feed;

class FeedPresenter extends Presenter
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * @see https://github.com/suin/php-rss-writer
     */
    public function renderDefault(bool $valid): void
    {
        Channel::$dateFormat = Item::$dateFormat = $valid ? DATE_RSS : DATE_ISO8601;

        $feed = new Feed();

        $channel = (new Channel())
            ->title('Sakila addresses')
            ->description('List of sakila addresses')
            ->url('https://hqm.cz')
            ->feedUrl($this->link("//this"))
            ->language('en-US')
            ->copyright('Copyright ' . date("Y") . ', Rashengka')
            ->pubDate(strtotime('yesterday 10:45:25'))
            ->lastBuildDate(strtotime('yesterday 11:00:00'))
            ->ttl(60)
            ->appendTo($feed);

        foreach ($this->getData() as $row) {
            (new Item())
                ->title($row["title"])
                ->url($row["url"])
                ->pubDate($row["time"])
                ->guid($row["url"], true)
                ->appendTo($channel);
        }

        $this->sendResponse(new XmlResponse($feed->render()));
    }

    private function getData(): array
    {
        $sql = <<<SQL
SELECT a.address_id, a.address, a.last_update 
FROM address AS a
ORDER BY a.last_update DESC
SQL;

        $data = [];
        foreach ($this->em->getConnection()->executeQuery($sql)->fetchAllNumeric() as $row) {
            $data[(int)$row[0]] = [
                "title" => $row[1],
                "time" => strtotime($row[2]),
                "url" => $this->link("//:Address:", [
                    "addressId" => (int)$row[0],
                ]),
            ];
        }

        return $data;
    }
}
