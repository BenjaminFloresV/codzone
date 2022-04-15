<?php
/* @var array $allData */

?>
<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Editar Datos de Compañía Desarrolladora</h1>
        <form  class="" runat="server" method="post" action="http://localhost:8001/admin/editar/desarrolladora" enctype="multipart/form-data" >
            <input type="hidden" name="company_id" value="<?=$allData['company_id']?>">
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Nombre</label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Nombre de la desarrolladora" name="name" value="<?=$allData['name']?>">
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <label class="label">Cantidad Empleados</label>
                        <div class="control has-icons-left has-icons-right">
                            <input class="input is-success" type="number" name="employees" min="0" placeholder="Cantidad de Empleados" value="<?=$allData['employees']?>">
                            <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                            <span class="icon is-small is-right">
                                        <i class="fas fa-check"></i>
                                    </span>
                        </div>
                    </div>
                </div>
            </div>



            <div class="columns">
                <div class="column is-one-quarter">
                    <div class="field">
                        <label class="label">Fecha de Fundación</label>
                        <div class="control has-icons-left has-icons-right">
                            <input type="date" name="foundationDate" value="<?=$allData['foundation']?>" >
                        </div>
                    </div>
                </div>
            </div>



            <div class="field">
                <label class="label">Descripción</label>
                <div class="control">
                    <textarea class="textarea" name="description" placeholder="Una descripción de la empresa, puede contener HTML" ><?=$allData['description']?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Logo de la Empresa</label>
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
                                    Choose a file…
                                    </span>
                                </span>
                            </div>
                            <div class="column">
                                <figure class="image is-128x128 is-right">
                                    <img id="blah" src="/uploads/images/company/<?=$allData['image']?>?<?=time()?>" alt="Logo" />
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
                    <a href="/admin/desarrolladoras/" class="button is-link is-light">Cancel</a>
                </div>
            </div>

        </form>


    </div>
</div>

<script type="text/javascript" src="/js/bulmacalendar"></script>