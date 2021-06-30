<?php

namespace App\Feeds\Vendors\SHA;

use App\Feeds\Feed\FeedItem;
use App\Feeds\Processor\HttpProcessor;
use App\Feeds\Utils\Data;
use App\Feeds\Utils\Link;

class Vendor extends HttpProcessor
{
    public const CATEGORY_LINK_CSS_SELECTORS = ['#AccessibleNav > li > a', '.pagination .page a'];
    public const PRODUCT_LINK_CSS_SELECTORS = ['.grid__item .grid-product__wrapper .grid-product__image-wrapper a:first-child'];

    protected const CHUNK_SIZE = 30;
    
    public array $first = ['https://www.gokalimba.com/'];

    public function getCategoriesLinks(Data $data, string $url): array
    {
        return array_map(
            static fn (Link $link) => new Link($link->getUrl() . '&viewAll=yes'),
            parent::getCategoriesLinks(...func_get_args())
        );
    }

    protected function isValidFeedItem(FeedItem $fi): bool
    {
        return count($fi->getImages()) && $fi->getCostToUs() > 0;
    }
}
