<?php
/* @var array $selects */
$wpcategories = $selects['WeaponCategory'];
$games = $selects['Game'];
?>
<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Ingresar Arma</h1>
        <form  class="" runat="server" method="post" action="/admin/insertar/arma" enctype="multipart/form-data" >

            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Nombre</label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Nombre del Arma" name="name">
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Juego</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select">
                                <select id="games" name="game_id">
                                    <?php foreach ($games as $game):  ?>
                                        <option value="<?=$game['game_id']?>"><?=$game['name']?></option>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input id="gameName" type="hidden" value="" name="gameSubdirectoy">

            <div class="field">
                <label class="label">Categoría</label>
                <div class="control has-icons-left has-icons-right">
                    <div class="select is-one-quarter-desktop">
                        <select name="wpcategory_id">
                            <?php foreach ($wpcategories as $wpcat):  ?>
                                <option value="<?=$wpcat['wpcategory_id']?>"><?=$wpcat['name']?></option>
                            <?php endforeach;  ?>
                        </select>
                    </div>
                </div>
            </div>


            <div class="field">
                <label class="label">Imagen</label>
                <div class="columns">
                    <div class="file">
                        <label class="file-label">
                            <div class="column">
                                <input accept="image/*" class="file-input" type="file" name="image" id="imgInp" >
                                <span class="file-cta">
                                  <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                                  </span>
                                    <span class="file-label">
                                    Elija una imagen
                                    </span>
                                </span>
                            </div>
                            <div class="column">
                                <figure class="image is-144x112 is-right">
                                    <img id="blah" src="" alt="Imagen Arma" />
                                </figure>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <input type="submit" class="button is-dark" value="Crear">
                </div>
                <div class="control">
                    <a href="/admin/armas" class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>
<script type="text/javascript" src="/js/weapons"></script>