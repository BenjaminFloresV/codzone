<?php
/* @var array $selects */
$games = $selects['Game'];
$wpcategories = $selects['WeaponCategory'];
$weapons = $selects['Weapon'];
?>
<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Publicar Nueva Clase</h1>
        <form  class="" runat="server" method="post" action="/admin/insertar/clase" enctype="multipart/form-data" >

            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Título</label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Título de la clase" name="title">
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Juego</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select is-fullwidth">

                                <select id="g-select" name="game_id">
                                    <?php foreach ($games as $game):  ?>
                                        <option value="<?=$game['game_id']?>"><?=$game['name']?></option>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <input id="gameName" type="hidden" value="" name="gameSubDirectory">

                <div class="column">
                    <div class="field">
                        <label class="label">Categoria Arma</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select is-fullwidth">

                                <select id="wcat-select" name="wpcategory_id">
                                    <?php foreach ($wpcategories as $wpcat):  ?>
                                        <option value="<?=$wpcat['wpcategory_id']?>"><?=$wpcat['name']?></option>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Fecha de Publicación</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="date" name="creation_date" >
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Arma</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select is-fullwidth">
                                <select id="w-select" name="weapon_id">
                                    <?php foreach ($weapons as $weapon):  ?>
                                        <option gameId="<?=$weapon['game_id']?>" weaponCatId="<?=$weapon['wpcategory_id']?>" value="<?=$weapon['weapon_id']?>"><?=$weapon['name']?></option>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <label class="label">Clase Warzone</label>
                    <div class="control">
                        <label class="radio">
                            <input type="radio" name="isWarzone" value="true">
                            Sí
                        </label>
                        <label class="radio">
                            <input type="radio" name="isWarzone" value="false">
                            No
                        </label>
                    </div>
                    <input type="hidden">
                </div>
            </div>



            <div class="field">
                <label class="label">Descripción -> Formato: DESCRIPCION INICIO_ DESCRIPCION FINAL</label>
                <div class="control">
                    <textarea class="textarea height-270px" name="description" placeholder="Descripcion de la clase" >DESCRIPCION DE INICIO_ DESCRIPCION FINAL</textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Accesorios --> Formato: ACCESORIO_DESCRIPCION/ ...</label>
                <div class="control">
                    <textarea class="textarea height-270px" name="attachments" placeholder="Descripcion de los accesorios" >ACCESORIO_DESCRIPCION/ ...</textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Ventajas --> Formato: VENTAJA_DESCRIPCION/ ...</label>
                <div class="control">
                    <textarea class="textarea height-270px" name="perks" placeholder="Descripcion de las ventajas" >VENTAJA_DESCRIPCION/ ...</textarea>
                </div>
            </div>

            <div class="field mb-6">
                <label class="label">Imagen de la Clase</label>
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
                                <figure class="image is-128x128 is-right">
                                    <img id="blah" src="" alt="Imagen" />
                                </figure>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <input type="submit" class="button is-dark" value="Publicar">
                </div>
                <div class="control">
                    <a class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>
<script type="text/javascript" src="/js/loadout-selects"></script>
<script type="text/javascript" src="/js/loadout-selects-insert"></script>
