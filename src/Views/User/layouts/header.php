<?php
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CoD Zone</title>
        <link rel="stylesheet" type="text/css" href="/css/mystyles"><link>
        <script src="https://kit.fontawesome.com/81bf225702.js" crossorigin="anonymous"></script>
        <script src="/js/navigation"></script>
    </head>
    <body>
        <div id="search-nav">
            <div class="search-box">
                <i class="fas fa-regular fa-magnifying-glass is-size-3 is-align-self-center is-pointer mr-4"></i>
                <input class="input" type="text" placeholder="Escribe aquí tu búsqueda" >
            </div>
            <button id="search-button" class="button has-background-black-ter has-text-white is-align-self-center ml-3">BUSCAR</button>
            <i id="close-search" class="fas fa-regular fa-xmark is-align-self-center is-size-1 ml-5"></i>
        </div>
        <div id="ghost-background"></div>
        <section id="search-container" class="section">
            <div class="search-content-container is-relative">
                <div class="search-content">
                    <h2 class="title search-title">Resultados de la Búsqueda:</h2>
                    <div class="search-results is-flex is-flex-direction-column is-justify-content-center">
                        <article class="article is-flex is-flex-direction-column has-background-white p-2 mb-3 mt-3">
                            <div id="post-image" class="is-flex is-relative">
                                <a href="http://localhost:7882">
                                    <figure class="image pt-2 pl-2 pr-2">
                                        <img src="/uploads/images/company/arma.png">
                                    </figure>
                                </a>
                                <a href="https://www.facebook.com" id="post-category" class="category search-category" >CoD Vanguard</a>
                            </div>
                            <a href="https://www.facebook.com" class="pl-2 pr-2">
                                <div id="title" class="post-title">
                                    <h2 class="title post-title-h2">Mejor clase para la M13 </h2>
                                </div>
                                <div class="post-upload-info">
                                    <span><i class="fa-solid fa-clock"></i> 18 de Septiembre 2022</span>
                                    <span class="ml-2"><i class="fa-solid fa-circle i-font-size"></i> SudoKiss</span>
                                </div>
                                <div style="display: block" class="posts-description">
                                    <p class="is-text posts-description">
                                        Hola como estas esta es una prueba
                                        esta es otra pureba dfjsdalkfjdsalkfj
                                        quiafdsfasdf
                                    </p>
                                </div>
                            </a>
                            <div class="read-more">
                                <a href="" class="pb-2 pl-2 pr-2">Leer más <<<</a>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
        <nav id="navigation">
            <div class="is-flex is-justify-content-center">
                <figure class="image logo-width has-ratio justify-self-start ml-6 pt-1 pb-1">
                    <img class="is-centered is-align-self-center" src="/uploads/logo.png">
                </figure>
                <div id="close-nav" class="is-flex is-justify-content-center justify-self-end is-align-content-center close has-text-white mr-6">
                    <span class="is-align-self-center is-size-5">Cerrar</span>
                    <i class="fas fa-regular fa-xmark is-align-self-center is-size-3 mt-1 ml-2"></i>
                </div>
            </div>
            <ul class="menu-list categories has-text-white mt-3">
                <li><a href="/clases/">Clases <i class="fa-solid fa-angle-right is-pulled-right is-vcentered is-size-3 mr-6"></i></li></a>
                <li><a>Armas<i class="fa-solid fa-angle-right is-pulled-right is-vcentered is-size-3 mr-6"></i></a></li>
                <li><a>Juegos<i class="fa-solid fa-angle-right is-pulled-right is-vcentered is-size-3 mr-6"></i></a></li>
                <li><a>Tutoriales<i class="fa-solid fa-angle-right is-pulled-right is-vcentered is-size-3 mr-6"></i></a></li>
                <li><a>Noticias<i class="fa-solid fa-angle-right is-pulled-right is-vcentered is-size-3 mr-6"></i></a></li>
            </ul>
        </nav>
        <header id="header" class="is-block is-fullwidth" role="navigation" aria-label="main navigation">
            <div class="columns pt-2 pb-2 is-flex  is-fullwidth">
                <div class="column pb-2 pt-2">
                    <div  id="burguer" class="is-flex pl-4 has-text-centered has-text-black is-text is-100-height">
                        <a id="open-nav" class="is-align-self-center is-link-hoverable-white">
                            <i class="fas fa-regular fa-bars is-size-5 is-align-self-center"></i>
                            <span class="ml-2 is-text is-size-5 is-align-self-center burguer-text">MENÚ</span>
                        </a>
                    </div>
                </div>
                <div class="column pb-2 pt-2">
                    <figure class="image logo-width has-ratio m-auto">
                        <img class="is-centered is-align-self-center" src="/uploads/logo.png">
                    </figure>
                </div>
                <div class="column pb-2 pt-2">
                    <div id="search" class="is-flex pl-4 has-text-centered has-text-black is-text is-100-height is-justify-content-end pr-4">
                        <a id="youtube-logo" class="is-align-self-center is-link-hoverable-white" href="#">
                            <i class="fa-brands fa-youtube is-size-3 is-align-self-center mr-4 is-pointer"></i>
                        </a>
                        <a id="open-search" class="is-align-self-center is-link-hoverable-white">
                            <i class="fas fa-regular fa-magnifying-glass is-size-3 is-align-self-center is-pointer"></i>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <section id="main" class="section desktop main-background-color main-home">



