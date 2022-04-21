<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\Helpers;
use CMS\Middleware\RenderView;
use CMS\Models\Loadout;
use CMS\Models\News;
use CMS\Models\Settings;
use CMS\Models\Tutorial;

class UserController
{
    public function homePage()
    {
        try {

            $loadout = Loadout::getInstance();
            $loadouts = $loadout::getAll(true, 5, true);
            $news = News::getInstance();
            $lastNews = $news::getAll(true, 1, false, null, true );
            $someNews = $news::getAll(true,4, false,null, true);
            $tutorial = Tutorial::getInstance();
            $someTutorials = $tutorial::getAll(true,4, false,null, true);
            $lastTutorial = $tutorial::getAll(true, 1, false, null, true);

            if( !empty($loadouts) && !empty($lastNews)  && !empty($someNews) && !empty( $lastTutorial ) && !empty( $someTutorials )  ){

                Helpers::deleteSession('maintenance');
                //$loadouts[0]['description'] = DataConverter::convertLoadoutInfoFormat($loadouts[0]['description']);

                foreach ( $loadouts as $key=>$item ){
                    $item['description'] = DataConverter::explodeContent($item['description']);
                    $loadouts[$key] = $item;
                }


                $lastNews = $lastNews[0];
                $lastNews['lowerCatName'] = DataConverter::stringToUri($lastNews['categoryName']);
                $lastNews['uriTitle'] = DataConverter::stringToUri($lastNews['title']);
                $settings = Settings::getInstance();
                $settings = $settings->getOneSetting('lastHomeLoadouts')['value'];
                $gameSettings = explode(',', $settings);
                $randomLoadout = $loadout::getByGames($gameSettings);


                $lastTutorial = $lastTutorial[0];
                $lastTutorial['lowerCatName'] = DataConverter::stringToUri($lastTutorial['categoryName']);
                $lastTutorial['uriTitle'] = DataConverter::stringToUri($lastTutorial['title']);

                foreach ( $someTutorials as $key=>$tutorial ){
                    $tutorial['lowerCatName'] = DataConverter::stringToUri($tutorial['categoryName']);
                    $tutorial['uriTitle'] = DataConverter::stringToUri( $tutorial['title'] );

                    $someTutorials[$key] = $tutorial;
                }


                foreach ( $someNews as $key=>$news ){
                    $news['lowerCatName'] = DataConverter::stringToUri($news['categoryName']);
                    $news['uriTitle'] = DataConverter::stringToUri($news['title']);

                    $someNews[$key] = $news;
                }


                foreach ( $randomLoadout as $key=>$item ){
                    if( $item == false ){
                        continue;
                    }
                    $item['gameUri'] = DataConverter::stringToUri($item['shortName']);
                    $randomLoadout[$key] = $item;
                }

                foreach ( $randomLoadout as $key=>$item ){
                    if( $item == false ){
                        continue;
                    }
                    $item['loadoutUri'] = DataConverter::stringToUri($item['title']);
                    $randomLoadout[$key] = $item;
                }



                foreach ( $loadouts as $key=>$item ){
                    $item['gameUri'] = DataConverter::stringToUri($item['shortName']);
                    $loadouts[$key] = $item;
                }

                foreach ( $loadouts as $key=>$item ){
                    $item['loadoutUri'] = DataConverter::stringToUri($item['title']);
                    $loadouts[$key] = $item;
                }

                $view = __DIR__.'/../../src/Views/User/home.phtml';
                RenderView::renderHome($view, $lastNews, $someNews,$lastTutorial,$someTutorials,$loadouts,$randomLoadout);

            }else {
                $_SESSION['maintenance'] = true;
                RenderView::renderMaintenance();
            }

        }catch (\Exception $exception){
            $_SESSION['maintenance'] = true;
            RenderView::renderMaintenance();
        }


    }



}