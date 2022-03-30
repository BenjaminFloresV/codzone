<?php
/* @var string $viewAction */
/* @var string $viewTitle */
/* @var string $urlPrefix */
/* @var string $baseUrl */
/* @var string $newsView */
/* @var string $tutorialView */
/* @var string $homeView */
?>
<header class="section has-background-dark py-4 has-text-white">
    <h1 class="title has-text-centered has-text-white"><?=$viewTitle?></h1>
</header>
<section class="section columns min-height-100v" id="aside">
    <div class="column is-one-quarter ">
        <aside class="menu has-background-dark rounded-corners p-4">
            <p class="menu-label is-size-7 has-text-weight-bold has-text-white">
                General
            </p>
            <ul class="menu-list">
                <li class="white-a"><a class="li-panel">Dashboard</a></li>
                <li class="white-a"><a href="/admin/" class="li-panel">Panel Administrador</a></li>
            </ul>
            <p class="menu-label is-size-7 has-text-weight-bold has-text-white">
                Administración
            </p>
            <ul class="menu-list">
                <li class="white-a" ><a href="<?=$urlPrefix?>ver">Ver</a></li>
                <?php if( $newsView !== null ): ?>
                <li class="white-a"><a href="<?=$newsView['uri']?>"><?=$newsView['name']?></a></li>
                <?php endif;?>
                <?php if( $tutorialView !== null ): ?>
                    <li class="white-a"><a href="<?=$tutorialView['uri']?>"><?=$tutorialView['name']?></a></li>
                <?php endif;?>
                <?php if( $homeView !== null ): ?>
                    <li class="white-a"><a href="<?=$homeView['uri']?>"><?=$homeView['name']?></a></li>
                <?php endif;?>

                <li class="white-a"><a href="<?=$urlPrefix?>crear">Crear</a></li>
                <li class="white-a"><a href="/admin/logout" class="li-panel">Cerrar Sesión</a></li>
            </ul>

        </aside>
    </div>
    <div class="column is-centered">
        <?php include __DIR__."/$baseUrl/$viewAction.php"?>
    </div>

</section>

