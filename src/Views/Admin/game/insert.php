<?php
/* @var array $selects */
$companies = $selects['DeveloperCompany'];

?>
<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Ingresar Juego</h1>
        <form  class="" runat="server" method="post" action="<?=BASE_URL?>/admin/insertar/juego" enctype="multipart/form-data" >
            <?php include __DIR__."/../session-messages/success-error.phtml"?>
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Nombre</label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Nombre del Juego" name="name">
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Nombre Corto</label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Nombre corto para las URLs" name="shortName">
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
                                    <option value="<?=$company['company_id']?>"><?=$company['name']?></option>
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
                            <input type="date" name="releaseDate" >
                        </div>
                    </div>
                </div>
            </div>



            <div class="field">
                <label class="label">Descripción</label>
                <div class="control">
                    <textarea class="textarea" name="description" placeholder="Una descripción del juego, puede contener HTML" ></textarea>
                </div>
            </div>

            <div class="field mb-6">
                <label class="label">Imagen del Juego</label>
                <div class="columns">
                    <div class="file">
                        <label class="file-label">
                            <div class="column">
                                <input accept="image/*" class="file-input" type="file" name="image" id="imgInp" >
                                <span class="file-cta is-pulled-left">
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
                                    <img id="blah" src="" alt="Imagen Juego" />
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
                    <a href="<?=BASE_URL?>/admin/juegos" class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>
