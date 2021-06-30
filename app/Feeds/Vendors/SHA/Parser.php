<?php

namespace App\Feeds\Vendors\SHA;

use App\Feeds\Parser\HtmlParser;
use App\Feeds\Utils\ParserCrawler;
use App\Helpers\StringHelper;

class Parser extends HtmlParser
{
    public function getProduct(): string
    {
        return trim($this->getAttr('meta[property="og:title"]', 'content'));
    }

    public function getMpn(): string
    {
        return $this->getText('#sku');
    }

    public function getListPrice(): ?float
    {
        if ($this->exists('#ProductPrice')) {
            return $this->getMoney('#ProductPrice');
        }

        return StringHelper::getMoney($this->getAttr('meta[ itemprop="price"]', 'content'));
    }

    public function getCostToUs(): float
    {
        if ($this->exists('#ProductPrice')) {
            return $this->getMoney('#ProductPrice');
        }

        return StringHelper::getMoney($this->getAttr('meta[ itemprop="price"]', 'content'));
    }

    public function getDescription(): string
    {
        $description = trim($this->getHtml('[itemprop="description"]'));
        if ($description === '') {
            $description = trim($this->getText('.product-single__description [style="font-size: 12pt; font-family: Arial;"]'));
        }
        return $description;
    }

    public function getShortDescription(): array
    {
        if ($this->exists('#details ul')) {
            return $this->getContent('#details ul li');
        }
        return [];
    }
    public function getOptions(): array
    {
        $options = [];
        $option_lists = $this->filter('#ProductSelect-option-0');

        if (!$option_lists->count()) {
            return $options;
        }

        $option_lists->each(function (ParserCrawler $list) use (&$options) {
            $label = $list->filter('label');
            if ($label->count() === 0) {
                return;
            }
            $name = trim($label->text(), ' : ');
            $options[$name] = [];
            $list->filter('input')->each(function (ParserCrawler $option) use (&$options, $name) {
                $options[$name][] = trim($option->text(), '  ');
            });
        });

        return $options;
    }

    public function getAvail(): ?int
    {
        return self::DEFAULT_AVAIL_NUMBER;
    }
}
