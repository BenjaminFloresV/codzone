<?php
/* @var array $selects */
/* @var array $allData */
$companies = $selects['DeveloperCompany'];
?>
<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Editar Juego</h1>
        <form  class="" runat="server" method="post" action="<?=BASE_URL?>/admin/editar/juego" enctype="multipart/form-data" >
            <input type="hidden" name="game_id" value="<?=$allData['game_id']?>">
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Nombre</label>
                        <div class="control">
                            <input class="input" type="text" value="<?=$allData['name']?>" placeholder="Nombre del Juego" name="name">
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Desarrolladora</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select">

                                <select name="company_id">
                                    <?php foreach ($companies as $company):  ?>
                                        <?php if($allData['company_id'] == $company['company_id']) :?>
                                        <option value="<?=$company['company_id']?>" selected="selected"><?=$company['name']?></option>
                                        <?php else: ?>
                                        <option value="<?=$company['company_id']?>"><?=$company['name']?></option>
                                        <?php endif; ?>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="columns">
                <div class="column is-one-quarter">
                    <div class="field">
                        <label class="label">Lanzamiento</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="date" name="releaseDate" value="<?=$allData['release_date']?>" >
                        </div>
                    </div>
                </div>
            </div>



            <div class="field">
                <label class="label">Descripción</label>
                <div class="control">
                    <textarea class="textarea" name="description" placeholder="Una descripción del juego, puede contener HTML" ><?=$allData['description']?></textarea>
                </div>
            </div>

            <div class="field mb-6">
                <label class="label">Imagen del Juego</label>
                <div class="columns">
                    <div class="file">
                        <label class="file-label">
                            <div class="column">
                                <input accept="image/*"  class="file-input" type="file" name="image" id="imgInp" >
                                <span class="file-cta">
                                  <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                                  </span>
                                    <span class="file-label">
                                    Choose a file…
                                    </span>
                                </span>
                            </div>
                            <div class="column">
                                <figure class="image is-144x112 is-right">
                                    <img id="blah" src="/uploads/images/game/<?=$allData['image']?>?<?=time()?>" alt="Imagen Juego" />
                                </figure>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <?php include __DIR__."/../session-messages/success-error.phtml"?>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <input type="submit" class="button is-dark" value="Editar">
                </div>
                <div class="control">
                    <a href="<?=BASE_URL?>/admin/juegos" class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>
