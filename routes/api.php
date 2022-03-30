<?php

use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::all("/myapi/clases", [\CMS\API\SearchApi::class, 'index']);


SimpleRouter::all("/myapi/search", [\CMS\API\SearchApi::class, 'index']);
SimpleRouter::all("/myapi/search/loadout", [\CMS\API\SearchApi::class, 'getLoadouts']);
SimpleRouter::all("/myapi/search/news", [\CMS\API\SearchApi::class, 'getNews']);
SimpleRouter::all("/myapi/search/tutorial", [\CMS\API\SearchApi::class, 'getTutorials']);
