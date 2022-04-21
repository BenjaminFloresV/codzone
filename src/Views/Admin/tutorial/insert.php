<?php
/* @var array $selects */

$categoires = $selects['Tutorial'];
?>

<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Publicar Tutorial</h1>
        <form  class="" runat="server" method="post" action="<?=BASE_URL?>/admin/insertar/tutorial" enctype="multipart/form-data" >
            <?php include __DIR__."/../session-messages/success-error.phtml"?>
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Título</label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Título del tutorial" name="title">
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Categoría</label>
                        <div class="control has-icons-left has-icons-right">
                            <div class="select is-fullwidth">
                                <select id="g-select" name="category_id">
                                    <?php foreach ($categoires as $category):  ?>
                                        <option value="<?=$category['category_id']?>"><?=$category['name']?></option>
                                    <?php endforeach;  ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        <label class="label">Fecha de Publicación</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="date" name="creation_date" >
                        </div>
                    </div>
                </div>

            </div>



            <div class="field">
                <label class="label">Descripción -> Formato: DESCRIPCION INICIO_DESCRIPCION MEDIA_DESCRIPCIÓN FINAL</label>
                <div class="control">
                    <textarea class="textarea height-270px" name="description" placeholder="Descripcion de la clase" >DESCRIPCION DE INICIO_DESCRIPCION MEDIA_DESCRIPCION FINAL</textarea>
                </div>
            </div>


            <div class="field mb-6">
                <label class="label">Imagen del Titular</label>
                <div class="columns">
                    <div class="file">
                        <label class="file-label">
                            <div class="column">
                                <input accept="image/*" class="file-input" type="file" name="image_title" id="imgInp" >
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

            <div class="field mb-6">
                <label class="label">Imagen de la descripción</label>
                <div class="columns">
                    <div class="file">
                        <label class="file-label">
                            <div class="column">
                                <input accept="image/*" class="file-input" type="file" name="image_desc" id="descImg" >
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
                                    <img id="descImgTarget" src="" alt="Imagen" />
                                </figure>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="field mb-6">
                <label class="label">Imagen del Footer ( Opcional ) </label>
                <div class="columns">
                    <div class="file">
                        <label class="file-label">
                            <div class="column">
                                <input accept="image/*" class="file-input" type="file" name="image_footer" id="footerImg" >
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
                                    <img id="footerImgTarget" src="" alt="Imagen" />
                                </figure>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="field mb-6">
                <label class="label">Imagen Extra ( Opcional ) </label>
                <div class="columns">
                    <div class="file">
                        <label class="file-label">
                            <div class="column">
                                <input accept="image/*" class="file-input" type="file" name="image_extra" id="extraImg" >
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
                                    <img id="extraImgTarget" src="" alt="Imagen" />
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
                    <a href="<?=BASE_URL?>/admin/tutoriales" class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>
<script type="text/javascript" src="/js/news-image-input"></script>
