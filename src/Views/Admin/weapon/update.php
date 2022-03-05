<?php
?>

<?php
/* @var array $selects */
/* @var array $allData */
$games = $selects['Game'];
$wpcategories = $selects['WeaponCategory'];
?>
<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Actualizar Arma</h1>
        <form  class="" runat="server" method="post" action="/admin/editar/arma" enctype="multipart/form-data" >
            <input type="hidden" name="weapon_id" value="<?=$allData['weapon_id']?>">
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Nombre</label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Nombre del Arma" name="name" value="<?=$allData['name']?>">
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
                                        <?php if ( $game['game_id'] == $allData['game_id'] ) : ?>
                                        <option value="<?=$game['game_id']?>" selected="selected"><?=$game['name']?></option>
                                        <?php else: ?>
                                        <option value="<?=$game['game_id']?>"><?=$game['name']?></option>
                                        <?php endif; ?>
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
                                <?php if ( $wpcat['wpcategory_id'] == $allData['wpcategory_id'] ): ?>
                                <option value="<?=$wpcat['wpcategory_id']?>" selected="selected"><?=$wpcat['name']?></option>
                                <?php else: ?>
                                <option value="<?=$wpcat['wpcategory_id']?>"><?=$wpcat['name']?></option>
                                <?php endif; ?>
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
                                    <img id="blah" src="/uploads/images/weapon/<?=$allData['gameName']?>/<?=$allData['image']?>?<?=time()?>" alt="Imagen Arma" />
                                </figure>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <input type="submit" class="button is-dark" value="Editar">
                </div>
                <div class="control">
                    <a class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>
<script type="text/javascript" src="/js/weapons"></script>
