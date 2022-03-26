<?php
/* @var array $selects */
/* @var array $allData */

$games = $selects['Game'];
$wpcategories = $selects['WeaponCategory'];
$weapons = $selects['Weapon'];

?>
<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Editar Clase</h1>
        <form  class="" runat="server" method="post" action="/admin/editar/clase" enctype="multipart/form-data" >
            <input type="hidden" name="loadout_id" value="<?=$allData['loadout_id']?>">
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Título</label>
                        <div class="control">
                            <input class="input" type="text" value="<?=$allData['title']?>"  placeholder="Título de la clase" name="title">
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
                                        <?php if( $game['game_id'] == $allData['game_id'] ) :?>
                                        <option value="<?=$game['game_id']?>" selected><?=$game['name']?></option>
                                        <?php else : ?>
                                        <option value="<?=$game['game_id']?>"><?=$game['name']?></option>
                                        <?php endif; ?>
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
                                        <?php if( $wpcat['wpcategory_id'] == $allData['wpcategory_id'] ) : ?>
                                        <option value="<?=$wpcat['wpcategory_id']?>" selected><?=$wpcat['name']?></option>
                                        <?php else: ?>
                                        <option value="<?=$wpcat['wpcategory_id']?>"><?=$wpcat['name']?></option>
                                        <?php endif; ?>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <input type="hidden" name="date_update" value=" <?=date('d/m/Y') ?>" >
            <div class="columns">
                <div class="column is-4">
                    <div class="field">
                        <label class="label">Arma</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select is-fullwidth">
                                <select id="w-select" name="weapon_id">
                                    <?php foreach ($weapons as $weapon):  ?>
                                        <?php if ( $weapon['weapon_id'] == $allData['weapon_id'] ) : ?>
                                        <option gameId="<?=$weapon['game_id']?>" weaponCatId="<?=$weapon['wpcategory_id']?>" value="<?=$weapon['weapon_id']?>" selected><?=$weapon['name']?></option>
                                        <?php else: ?>
                                        <option gameId="<?=$weapon['game_id']?>" weaponCatId="<?=$weapon['wpcategory_id']?>" value="<?=$weapon['weapon_id']?>"><?=$weapon['name']?></option>
                                        <?php endif; ?>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <input type="hidden">
                </div>
            </div>



            <div class="field">
                <label class="label">Descripción -> Formato: DESCRIPCION INICIO_ DESCRIPCION FINAL</label>
                <div class="control">
                    <textarea class="textarea" name="description" placeholder="Una descripción de la clase, puede contener HTML" ><?=$allData['description']?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Accesorios --> Formato: ACCESORIO_DESCRIPCION/ ... </label>
                <div class="control">
                    <textarea class="textarea height-270px" name="attachments" placeholder="Descripcion de los accesorios" ><?=$allData['attachments']?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Ventajas --> Formato: VENTAJA_DESCRIPCION/ ...</label>
                <div class="control">
                    <textarea class="textarea height-270px" name="perks" placeholder="Descripcion de las ventajas" ><?=$allData['perks']?></textarea>
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
                                    <img id="blah" src="/uploads/images/loadout/<?=$allData['gameName']?>/<?=$allData['image']?>?<?=time()?>" alt="Imagen" />
                                </figure>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <input type="submit" class="button is-dark" value="Actualizar">
                </div>
                <div class="control">
                    <a href="/admin/clases/" class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>
<script type="text/javascript" src="/js/loadout-selects"></script>
